<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransactionOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currentMonth = now();
        $lastMonth = now()->subMonth();
        
        // Data bulan ini
        $currentIncome = Transaction::income()
            ->inMonth($currentMonth->month, $currentMonth->year)
            ->sum('amount');
            
        $currentExpense = Transaction::expense()
            ->inMonth($currentMonth->month, $currentMonth->year)
            ->sum('amount');
            
        $currentBalance = $currentIncome - $currentExpense;
        
        // Data bulan lalu untuk perbandingan
        $lastIncome = Transaction::income()
            ->inMonth($lastMonth->month, $lastMonth->year)
            ->sum('amount');
            
        $lastExpense = Transaction::expense()
            ->inMonth($lastMonth->month, $lastMonth->year)
            ->sum('amount');
            
        $lastBalance = $lastIncome - $lastExpense;
        
        // Hitung persentase perubahan
        $incomeChange = $lastIncome > 0 
            ? (($currentIncome - $lastIncome) / $lastIncome) * 100 
            : 0;
            
        $expenseChange = $lastExpense > 0 
            ? (($currentExpense - $lastExpense) / $lastExpense) * 100 
            : 0;
            
        $balanceChange = $lastBalance != 0 
            ? (($currentBalance - $lastBalance) / abs($lastBalance)) * 100 
            : 0;

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($currentIncome, 0, ',', '.'))
                // ->description($incomeChange >= 0 
                //     ? number_format(abs($incomeChange), 1) . '% naik dari bulan lalu'
                //     : number_format(abs($incomeChange), 1) . '% turun dari bulan lalu'
                // )
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($incomeChange >= 0 ? 'success' : 'danger')
                ->chart([
                    $lastIncome,
                    $currentIncome,
                ]),
                
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($currentExpense, 0, ',', '.'))
                // ->description($expenseChange <= 0 
                //     ? number_format(abs($expenseChange), 1) . '% turun dari bulan lalu'
                //     : number_format(abs($expenseChange), 1) . '% naik dari bulan lalu'
                // )
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($expenseChange <= 0 ? 'success' : 'danger')
                ->chart([
                    $lastExpense,
                    $currentExpense,
                ]),
                
            Stat::make('Saldo Bersih', 'Rp ' . number_format($currentBalance, 0, ',', '.'))
                // ->description($balanceChange >= 0 
                //     ? number_format(abs($balanceChange), 1) . '% lebih baik dari bulan lalu'
                //     : number_format(abs($balanceChange), 1) . '% lebih buruk dari bulan lalu'
                // )
                ->descriptionIcon($balanceChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($currentBalance >= 0 ? 'success' : 'danger')
                ->chart([
                    $lastBalance,
                    $currentBalance,
                ]),
        ];
    }
}