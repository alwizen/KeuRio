<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'month', 'year',
        'base_salary', 'bonus',
        'health_incentive', 'work_incentive',
        'other', 'cash_advance',
        'note', 'total_thp',
    ];

    protected $casts = [
        'month'            => 'integer',
        'year'             => 'integer',
        'base_salary'      => 'decimal:2',
        'bonus'            => 'decimal:2',
        'health_incentive' => 'decimal:2',
        'work_incentive'   => 'decimal:2',
        'other'            => 'decimal:2',
        'cash_advance'     => 'decimal:2',
        'total_thp'        => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Auto-calc total_thp on save if not set
    protected static function booted(): void
    {
        static::saving(function (self $p) {
            $p->total_thp = $p->total_thp ?: (
                ($p->base_salary ?? 0)
                + ($p->bonus ?? 0)
                + ($p->health_incentive ?? 0)
                + ($p->work_incentive ?? 0)
                + ($p->other ?? 0)
                - ($p->cash_advance ?? 0)
            );
        });
    }

    /** Helper: get "YYYY-MM" period string */
    public function getPeriodAttribute(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }

    /** Scope by period */
    public function scopePeriod($q, int $year, int $month)
    {
        return $q->where('year', $year)->where('month', $month);
    }
}
