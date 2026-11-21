<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Uses firstOrCreate so the seeder is safe to run repeatedly: existing
     * users and products (including stock/price changes made by the owner)
     * are never overwritten.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('pamekasan'),
            ]
        );

        $this->firstOrCreateProduct('Air Galon Baru', 'pcs', 25000, 15, 5, true);
        $this->firstOrCreateProduct('Air Galon Isi Ulang Besar', 'galon', 7000, 25, 5, true);
        $this->firstOrCreateProduct('Air Galon Isi Ulang Kecil', 'galon', 5000, 25, 5, true);
    }

    private function firstOrCreateProduct(
        string $name,
        string $unit,
        int $price,
        int $stock,
        int $minStock,
        bool $trackStock
    ): void {
        Product::firstOrCreate(
            ['name' => $name],
            [
                'unit' => $unit,
                'price' => $price,
                'stock' => $stock,
                'min_stock' => $minStock,
                'track_stock' => $trackStock,
                'is_active' => true,
            ]
        );
    }
}
