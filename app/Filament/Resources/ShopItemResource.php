<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopItemResource\Pages;
use App\Filament\Resources\ShopItemResource\RelationManagers;
use App\Models\ShopItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShopItemResource extends Resource
{
    protected static ?string $model = ShopItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Textarea::make('description')->rows(2),

            Forms\Components\Select::make('category')
                ->options([
                    'hydration' => 'Hydration',
                    'equipment' => 'Equipment',
                    'recovery'  => 'Recovery',
                    'apparel'   => 'Apparel',
                ])->required(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('coin_cost')
                    ->numeric()->nullable()
                    ->helperText('Leave empty if coins not accepted'),
                Forms\Components\TextInput::make('cash_price')
                    ->numeric()->prefix('₦')->nullable()
                    ->helperText('Leave empty if cash not accepted'),
            ]),

            Forms\Components\TextInput::make('stock')
                ->numeric()->default(100)
                ->helperText('-1 for unlimited stock'),

            Forms\Components\FileUpload::make('image_url')
                ->image()->directory('shop/items'),

            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image_url')->label('Image'),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::badge('category'),
            Tables\Columns\TextColumn::make('coin_cost')->suffix(' 🪙'),
            Tables\Columns\TextColumn::make('cash_price')->prefix('₦'),
            Tables\Columns\TextColumn::make('stock'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopItems::route('/'),
            'create' => Pages\CreateShopItem::route('/create'),
            'edit' => Pages\EditShopItem::route('/{record}/edit'),
        ];
    }
}
