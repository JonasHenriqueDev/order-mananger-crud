<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'discount',
        'total',
        'notes',
        'processed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });

        static::created(function ($order) {
            OrderCreated::dispatch($order);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal + $this->tax - $this->discount;
        $this->save();
    }

    public function markAsProcessing(): void
    {
        $previousStatus = $this->status->value;
        $this->update([
            'status' => OrderStatus::PROCESSING,
            'processed_at' => now(),
        ]);
        OrderStatusChanged::dispatch($this, $previousStatus, OrderStatus::PROCESSING->value);
    }

    public function markAsCompleted(): void
    {
        $previousStatus = $this->status->value;
        $this->update([
            'status' => OrderStatus::COMPLETED,
            'completed_at' => now(),
        ]);
        OrderStatusChanged::dispatch($this, $previousStatus, OrderStatus::COMPLETED->value);
    }

    public function markAsCancelled(): void
    {
        $previousStatus = $this->status->value;
        $this->update([
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);
        OrderStatusChanged::dispatch($this, $previousStatus, OrderStatus::CANCELLED->value);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [OrderStatus::PENDING, OrderStatus::PROCESSING], true);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}

