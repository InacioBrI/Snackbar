<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'new' => 'Novo',
        'preparing' => 'Em preparo',
        'ready' => 'Pronto',
        'delivered' => 'Entregue',
        'cancelled' => 'Cancelado',
    ];

    public const PAYMENT_METHODS = [
        'pix' => 'PIX',
        'credit' => 'Cartão de Crédito',
        'debit' => 'Cartão de Débito',
    ];

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'location',
        'notes',
        'subtotal',
        'service_fee',
        'total',
        'payment_method',
        'payment_status',
        'payment_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentMethodLabel(): ?string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
