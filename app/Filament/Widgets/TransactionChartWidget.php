<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TransactionChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Pemasukan & Pengeluaran';
    
    protected static ?int $sort = 2;
    
    // protected int | string | array $columnSpan = 'full';

    // protected static ?string $slug = 'transaction-chart-widget';

    public ?string $filter = 'trend';

    protected function getFilters(): ?array
    {
        return [
            'trend' => 'Trend 6 Bulan',
            // 'category' => 'Per Kategori',
            'daily' => 'Harian (30 Hari)'
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;
        
        return match($filter) {
            'trend' => $this->getTrendData(),
            // 'category' => $this->getCategoryData(),
            'daily' => $this->getDailyData(),
            default => $this->getDailyData(),
        };
    }

    protected function getTrendData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            
            $income = Transaction::income()
                ->inMonth($date->month, $date->year)
                ->sum('amount');
                
            $expense = Transaction::expense()
                ->inMonth($date->month, $date->year)
                ->sum('amount');
            
            $months[] = $monthName;
            $incomeData[] = $income;
            $expenseData[] = $expense;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    // protected function getCategoryData(): array
    // {
    //     $currentMonth = now();
        
    //     // Ambil top 5 kategori pengeluaran bulan ini
    //     $expenseCategories = Transaction::expense()
    //         ->inMonth($currentMonth->month, $currentMonth->year)
    //         ->selectRaw('category, SUM(amount) as total')
    //         ->whereNotNull('category')
    //         ->groupBy('category')
    //         ->orderByDesc('total')
    //         ->limit(5)
    //         ->get();
            
    //     $labels = [];
    //     $data = [];
    //     $colors = [
    //         '#ef4444', '#f97316', '#eab308', '#84cc16', '#06b6d4'
    //     ];
        
    //     foreach ($expenseCategories as $index => $category) {
    //         $categoryNames = [
    //             'makanan' => 'Makanan & Minuman',
    //             'transportasi' => 'Transportasi',
    //             'belanja' => 'Belanja',
    //             'tagihan' => 'Tagihan',
    //             'kesehatan' => 'Kesehatan',
    //             'pendidikan' => 'Pendidikan',
    //             'hiburan' => 'Hiburan',
    //             'pakaian' => 'Pakaian',
    //             'rumah_tangga' => 'Rumah Tangga',
    //             'lainnya' => 'Lainnya',
    //         ];
            
    //         $labels[] = $categoryNames[$category->category] ?? $category->category;
    //         $data[] = $category->total;
    //     }
        
    //     return [
    //         'datasets' => [
    //             [
    //                 'data' => $data,
    //                 'backgroundColor' => array_slice($colors, 0, count($data)),
    //                 'borderWidth' => 0,
    //             ],
    //         ],
    //         'labels' => $labels,
    //     ];
    // }

    protected function getDailyData(): array
    {
        $days = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('d/m');
            
            $income = Transaction::income()
                ->whereDate('transaction_date', $date)
                ->sum('amount');
                
            $expense = Transaction::expense()
                ->whereDate('transaction_date', $date)
                ->sum('amount');
            
            $days[] = $dayName;
            $incomeData[] = $income;
            $expenseData[] = $expense;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return $this->filter === 'category' ? 'doughnut' : 'line';
    }

    protected function getOptions(): array
    {
        $baseOptions = [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
        
        if ($this->filter === 'category') {
            return array_merge($baseOptions, [
                'maintainAspectRatio' => false,
                'responsive' => true,
            ]);
        }
        
        return array_merge($baseOptions, [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "Rp " + value.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ]);
    }
}