<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'type',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Konstanta untuk type
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';
    const TYPE_BOTH = 'both';

    // Boot method untuk auto generate slug
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationship dengan Transaction
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope untuk filter berdasarkan type
    public function scopeForIncome(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE_INCOME, self::TYPE_BOTH]);
    }

    public function scopeForExpense(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE_EXPENSE, self::TYPE_BOTH]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Scope untuk filter berdasarkan type spesifik
    public function scopeForTransactionType(Builder $query, string $transactionType): Builder
    {
        return match($transactionType) {
            'income' => $query->forIncome(),
            'expense' => $query->forExpense(),
            default => $query
        };
    }

    // Accessor untuk menampilkan type dalam bahasa Indonesia
    public function getTypeInIndonesianAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INCOME => 'Pemasukan',
            self::TYPE_EXPENSE => 'Pengeluaran',
            self::TYPE_BOTH => 'Keduanya',
            default => $this->type
        };
    }

    // Method untuk mendapatkan total transaksi dari kategori ini
    public function getTotalTransactions(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->transactions();
        
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }
        
        return $query->sum('amount');
    }

    // Method untuk mendapatkan jumlah transaksi dari kategori ini
    public function getTransactionCount(?string $startDate = null, ?string $endDate = null): int
    {
        $query = $this->transactions();
        
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }
        
        return $query->count();
    }

    // Method untuk cek apakah kategori bisa digunakan untuk jenis transaksi tertentu
    public function canBeUsedFor(string $transactionType): bool
    {
        return $this->type === $transactionType || $this->type === self::TYPE_BOTH;
    }

    // Static method untuk mendapatkan kategori berdasarkan type
    public static function getForSelect(string $transactionType = null): array
    {
        $query = self::active()->ordered();
        
        if ($transactionType) {
            $query->forTransactionType($transactionType);
        }
        
        return $query->get()->mapWithKeys(function ($category) {
            return [
                $category->id => ($category->icon ? $category->icon . ' ' : '') . $category->name
            ];
        })->toArray();
    }

    // Method untuk mendapatkan kategori paling populer
    public static function getMostUsed(int $limit = 5, ?string $transactionType = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::withCount('transactions')
            ->active()
            ->orderByDesc('transactions_count');
            
        if ($transactionType) {
            $query->forTransactionType($transactionType);
        }
        
        return $query->limit($limit)->get();
    }
}