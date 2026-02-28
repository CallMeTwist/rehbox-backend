<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue (Last 6 Months)';
    protected static ?int    $sort    = 3;

    protected function getData(): array
    {
        $data   = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date   = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $data[]   = Subscription::where('status', 'active')
                ->whereMonth('starts_at', $date->month)
                ->whereYear('starts_at', $date->year)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (₦)',
                    'data'            => $data,
                    'borderColor'     => '#E8358A',
                    'backgroundColor' => 'rgba(232, 53, 138, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
