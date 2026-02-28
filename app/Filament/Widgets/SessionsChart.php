<?php

namespace App\Filament\Widgets;

use App\Models\ExerciseSession;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SessionsChart extends ChartWidget
{
    protected static ?string $heading = 'Exercise Sessions (Last 8 Weeks)';
    protected static ?int    $sort    = 2;

    protected function getData(): array
    {
        $data   = [];
        $labels = [];

        for ($i = 7; $i >= 0; $i--) {
            $start = Carbon::now()->subWeeks($i)->startOfWeek();
            $end   = Carbon::now()->subWeeks($i)->endOfWeek();

            $labels[] = $start->format('d M');
            $data[]   = ExerciseSession::where('status', 'completed')
                ->whereBetween('completed_at', [$start, $end])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Sessions',
                    'data'            => $data,
                    'backgroundColor' => '#2C5FC3',
                    'borderColor'     => '#2C5FC3',
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
