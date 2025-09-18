<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'type',
        'category_id',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // Relationship dengan Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    // Scope untuk filter berdasarkan type
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_INCOME);
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeInDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan bulan dan tahun
    public function scopeInMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('transaction_date', $month)
                    ->whereYear('transaction_date', $year);
    }

    // Accessor untuk menampilkan amount dengan format
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Accessor untuk menampilkan type dalam bahasa Indonesia
    public function getTypeInIndonesianAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INCOME => 'Pemasukan',
            self::TYPE_EXPENSE => 'Pengeluaran',
            default => $this->type
        };
    }

    // Method untuk mendapatkan total berdasarkan type
    public static function getTotalByType(string $type, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = self::where('type', $type);
        
        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }
        
        return $query->sum('amount');
    }

    // Method untuk mendapatkan saldo (pemasukan - pengeluaran)
    public static function getBalance(?string $startDate = null, ?string $endDate = null): float
    {
        $income = self::getTotalByType(self::TYPE_INCOME, $startDate, $endDate);
        $expense = self::getTotalByType(self::TYPE_EXPENSE, $startDate, $endDate);
        
        return $income - $expense;
    }

    // Method untuk mendapatkan kategori yang paling sering digunakan
    public static function getPopularCategories(int $limit = 5): array
    {
        return self::with('category')
                  ->whereNotNull('category_id')
                  ->selectRaw('category_id, COUNT(*) as count')
                  ->groupBy('category_id')
                  ->orderByDesc('count')
                  ->limit($limit)
                  ->get()
                  ->mapWithKeys(function ($item) {
                      return [$item->category->name => $item->count];
                  })
                  ->toArray();
    }
}