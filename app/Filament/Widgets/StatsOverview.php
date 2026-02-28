<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\ExerciseSession;
use App\Models\Physiotherapist;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers  = User::whereIn('role', ['pt', 'client'])->count();
        $activeSubs  = Subscription::where('status', 'active')->count();
        $totalRevenue = Subscription::where('status', 'active')->sum('amount');
        $todaySessions = ExerciseSession::whereDate('created_at', today())
            ->where('status', 'completed')->count();
        $pendingVetting = Physiotherapist::where('vetting_status', 'pending')->count();
        $avgFormScore = ExerciseSession::where('status', 'completed')
            ->whereNotNull('form_score')->avg('form_score');

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('PTs + Clients')
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Active Subscriptions', $activeSubs)
                ->description('Paying clients')
                ->color('success')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Total Revenue', '₦' . number_format($totalRevenue, 0))
                ->description('All time subscriptions')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Sessions Today', $todaySessions)
                ->description('Completed today')
                ->color('info')
                ->icon('heroicon-o-play'),

            Stat::make('Pending Vetting', $pendingVetting)
                ->description('Awaiting approval')
                ->color($pendingVetting > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clock'),

            Stat::make('Avg Form Score', round($avgFormScore ?? 0) . '%')
                ->description('Across all sessions')
                ->color('primary')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
