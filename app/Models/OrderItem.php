<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Calcolo automatico del subtotale
    protected static function booted(): void
    {
        static::saving(function (OrderItem $orderItem) {
            $orderItem->subtotal = $orderItem->quantity * $orderItem->unit_price;
        });

        static::saved(function (OrderItem $orderItem) {
            // Aggiorna il totale dell'ordine quando un item cambia
            $orderItem->order->updateTotal();
        });

        static::deleted(function (OrderItem $orderItem) {
            // Aggiorna il totale dell'ordine quando un item viene eliminato
            $orderItem->order->updateTotal();
        });
    }

    // Attributo calcolato per il margine di profitto
    protected function margin(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->product || !$this->product->cost_price) {
                    return 0;
                }
                
                $profit = $this->unit_price - $this->product->cost_price;
                return round(($profit / $this->unit_price) * 100, 2);
            }
        );
    }

    // Attributo calcolato per il profitto totale
    protected function totalProfit(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->product || !$this->product->cost_price) {
                    return 0;
                }
                
                $profitPerUnit = $this->unit_price - $this->product->cost_price;
                return round($profitPerUnit * $this->quantity, 2);
            }
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
