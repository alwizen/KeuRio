<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nip', 'name', 'address', 'phone',
    ];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    // Auto-generate 5-digit NIP if empty
    protected static function booted(): void
    {
        static::creating(function (self $employee) {
            if (empty($employee->nip)) {
                $employee->nip = self::generateNip();
            }
        });
    }

    public static function generateNip(): string
    {
        do {
            $nip = str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('nip', $nip)->exists());

        return $nip;
    }
}
