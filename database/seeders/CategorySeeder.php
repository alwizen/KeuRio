<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gaji',
                'icon' => '💰',
                'color' => '#22c55e',
                'type' => Category::TYPE_EXPENSE,
                'description' => 'Pendapatan dari gaji bulanan',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Kas',
                'icon' => '🎁',
                'color' => '#16a34a',
                'type' => Category::TYPE_INCOME,
                'description' => 'Kas tambahan',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Makan',
                'icon' => '🍜',
                'color' => '#ef4444',
                'type' => Category::TYPE_EXPENSE,
                'description' => 'Pengeluaran untuk makanan',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Transportasi',
                'icon' => '🚌',
                'color' => '#f97316',
                'type' => Category::TYPE_EXPENSE,
                'description' => 'Biaya transportasi harian',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Lain-lain',
                'icon' => '📦',
                'color' => '#64748b',
                'type' => Category::TYPE_BOTH,
                'description' => 'Kategori umum untuk pemasukan/pengeluaran lainnya',
                'is_active' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['name' => $data['name']], // cek kalau sudah ada
                $data
            );
        }
    }
}
