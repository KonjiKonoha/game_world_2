<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $user = User::find(Auth::user()->id);

        return [
            Card::make('My credit', ($user) ? number_format($user->money, 2) : 0.00)
            ->description('Myanmar Kyat')
            ->descriptionIcon('heroicon-s-cash'),
        Card::make('Bounce rate', '21%')
            ->description('7% increase')
            ->descriptionIcon('heroicon-s-trending-down'),
        Card::make('Average time on page', '3:12')
            ->description('3% increase')
            ->descriptionIcon('heroicon-s-trending-up'),
        ];
    }
}
