<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = 'Ringkasan';
    protected static bool $isLazy = false;

    // satu sumber kebenaran nama kolom tanggal
    private const DATE_COL = 'transaction_date';

    protected function getStats(): array
    {
        // Total all-time
        $totalIncome  = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        // 12 bulan terakhir (terlama -> terbaru)
        $incomeSeries  = [];
        $expenseSeries = [];
        $balanceSeries = [];

        $start = now()->startOfMonth()->subMonths(11);
        for ($i = 0; $i < 12; $i++) {
            $month = (clone $start)->addMonths($i);

            $mIncome = Transaction::income()
                ->whereYear(self::DATE_COL, $month->year)
                ->whereMonth(self::DATE_COL, $month->month)
                ->sum('amount');

            $mExpense = Transaction::expense()
                ->whereYear(self::DATE_COL, $month->year)
                ->whereMonth(self::DATE_COL, $month->month)
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
