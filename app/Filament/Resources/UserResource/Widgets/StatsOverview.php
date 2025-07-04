<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Agents', User::where('role', 'agent')->count())
                ->description('Support team members')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Regular Users', User::where('role', 'user')->count())
                ->description('Customer accounts')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),

            Stat::make('Active Tickets', Ticket::whereIn('status', ['open', 'in_progress'])->count())
                ->description('Tickets requiring attention')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('danger'),
        ];
    }
}
