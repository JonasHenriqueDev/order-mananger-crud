<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'stock',
        'status',
        'is_featured',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->sku);
            }

            if (empty($product->sku)) {
                $product->sku = strtoupper('PROD-' . Str::random(6));
            }
        });

        static::created(fn($product) => self::clearCache());
        static::updated(fn($product) => self::clearCache());
        static::deleted(fn($product) => self::clearCache());
        static::restored(fn($product) => self::clearCache());
        static::forceDeleted(fn($product) => self::clearCache());
    }

    protected static function clearCache()
    {
        $perPage = 10;
        $totalProducts = self::where('status', 'active')->count();

        $totalPages = (int) ceil($totalProducts / $perPage);

        for ($i = 1; $i <= $totalPages; $i++) {
            Cache::forget("products_list_page_{$i}");
        }
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, ',', '.');
    }

    public function adjustStock(int $quantity): void
    {
        $this->stock += $quantity;
        if ($this->stock < 0) {
            $this->stock = 0;
        }
        $this->save();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function setMeta(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }

    public function getMeta(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }
}
