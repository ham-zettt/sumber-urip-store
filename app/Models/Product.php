<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'image', 'unit', 'price', 'stock', 'min_stock', 'track_stock', 'is_active'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * The accessors to append to the model's array and JSON form.
     *
     * @var array<int, string>
     */
    protected $appends = ['image_url', 'category', 'description'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'track_stock' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->track_stock && $this->stock <= $this->min_stock;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('storage/'.$this->image))) {
            return asset('storage/'.$this->image);
        }

        if ($this->image && file_exists(storage_path('app/public/'.$this->image))) {
            return asset('storage/'.$this->image);
        }

        return asset('storage/products/'.($this->image ?: 'isi-ulang-air-galon.svg'));
    }

    public function getCategoryAttribute(): string
    {
        $lower = strtolower($this->name);
        if (str_contains($lower, 'isi ulang')) {
            return 'Isi Ulang';
        }
        if (str_contains($lower, 'paket')) {
            return 'Paket Hemat';
        }
        if (str_contains($lower, 'galon kosong') || str_contains($lower, 'galon baru')) {
            return 'Galon Baru';
        }
        if (str_contains($lower, 'segel') || str_contains($lower, 'tutup')) {
            return 'Aksesoris';
        }
        if (str_contains($lower, 'air')) {
            return 'Air Galon';
        }

        return 'Produk Depo';
    }

    public function getDescriptionAttribute(): string
    {
        $lower = strtolower($this->name);
        if (str_contains($lower, 'isi ulang')) {
            return 'Air mineral steril & higienis';
        }
        if (str_contains($lower, 'paket')) {
            return 'Galon baru terisi air siap pakai';
        }
        if (str_contains($lower, 'galon kosong') || str_contains($lower, 'galon baru')) {
            return 'Galon tebal food-grade 19 Liter SNI';
        }
        if (str_contains($lower, 'segel') || str_contains($lower, 'tutup')) {
            return 'Tutup segel higienis anti tumpah';
        }

        return 'Kualitas terjamin untuk kebutuhan harian';
    }
}
