<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShopItem;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->user()->client;
        $items  = ShopItem::active()
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->get()
            ->map(fn($item) => array_merge($item->toArray(), [
                'can_afford_with_coins' => $item->isAffordableWithCoins($client->coin_balance),
                'in_stock'              => $item->isInStock(),
            ]));

        return response()->json([
            'items'        => $items,
            'coin_balance' => $client->coin_balance,
        ]);
    }

    public function purchase(Request $request, ShopItem $item)
    {
        $data = $request->validate([
            'payment_method'   => 'required|in:coins,cash,mixed',
            'delivery_address' => 'required|string|max:500',
        ]);

        $client = $request->user()->client;

        if (!$item->isInStock()) {
            return response()->json(['message' => 'Item is out of stock.'], 422);
        }

        $coinsUsed = 0;
        $cashPaid  = 0;

        if ($data['payment_method'] === 'coins') {
            if (!$item->coin_cost) {
                return response()->json(['message' => 'This item cannot be purchased with coins.'], 422);
            }
            if ($client->coin_balance < $item->coin_cost) {
                return response()->json([
                    'message'        => 'Insufficient coin balance.',
                    'required'       => $item->coin_cost,
                    'your_balance'   => $client->coin_balance,
                ], 422);
            }
            $coinsUsed = $item->coin_cost;
            $client->spendCoins($coinsUsed, "Purchased: {$item->name}", $item);

        } elseif ($data['payment_method'] === 'cash') {
            $cashPaid = $item->cash_price;
            // Cash payment handled via Paystack separately

        } elseif ($data['payment_method'] === 'mixed') {
            $coinsUsed = $item->coin_cost ?? 0;
            $cashPaid  = max(0, ($item->cash_price ?? 0) - ($coinsUsed * 0.01));
            if ($coinsUsed > 0) {
                $client->spendCoins($coinsUsed, "Part payment: {$item->name}", $item);
            }
        }

        $order = Order::create([
            'client_id'        => $client->id,
            'shop_item_id'     => $item->id,
            'payment_method'   => $data['payment_method'],
            'coins_used'       => $coinsUsed,
            'cash_paid'        => $cashPaid,
            'status'           => 'confirmed',
            'delivery_address' => $data['delivery_address'],
        ]);

        // Decrement stock unless unlimited
        if ($item->stock !== -1) {
            $item->decrement('stock');
        }

        return response()->json([
            'message'    => 'Order placed successfully!',
            'order_id'   => $order->id,
            'new_balance'=> $client->fresh()->coin_balance,
        ], 201);
    }

    public function myOrders(Request $request)
    {
        $client = $request->user()->client;
        $orders = $client->orders()
            ->with('item:id,name,image_url,category')
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }
}
