<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransactionOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan';

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // --- Total all-time ---
        $totalIncome  = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        // --- Build charts: last 12 months trend (inclusive this month) ---
        $incomeSeries  = [];
        $expenseSeries = [];
        $balanceSeries = [];

        // Ambil 12 bulan ke belakang, dari terlama -> terbaru
        $start = now()->startOfMonth()->subMonths(11);

        for ($i = 0; $i < 12; $i++) {
            $month = (clone $start)->addMonths($i);

            $mIncome = Transaction::income()
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');

            $mExpense = Transaction::expense()
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');

            $incomeSeries[]  = (int) $mIncome;
            $expenseSeries[] = (int) $mExpense;
            $balanceSeries[] = (int) ($mIncome - $mExpense);
        }

        return [
            Stat::make('Total Pemasukan (All-time)', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->color('success')
                ->description('Akumulasi seluruh pemasukan')
                ->chart($incomeSeries),

            Stat::make('Total Pengeluaran (All-time)', 'Rp ' . number_format($totalExpense, 0, ',', '.'))
                ->color('danger')
                ->description('Akumulasi seluruh pengeluaran')
                ->chart($expenseSeries),

            Stat::make('Saldo Bersih (All-time)', 'Rp ' . number_format($totalBalance, 0, ',', '.'))
                ->color($totalBalance >= 0 ? 'success' : 'danger')
                ->description('Pemasukan − Pengeluaran')
                ->chart($balanceSeries),
        ];
    }
}
