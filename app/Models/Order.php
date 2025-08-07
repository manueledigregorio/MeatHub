<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    protected $fillable = [
        'total',
        'created_at'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // Metodo per aggiornare automaticamente il totale
    public function updateTotal(): void
    {
        $this->total = $this->items()->sum('subtotal');
        $this->saveQuietly(); // Salva senza triggering degli eventi
    }

    // Attributo calcolato per il profitto totale dell'ordine
    protected function totalProfit(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->items->sum(function ($item) {
                    return $item->total_profit;
                });
            }
        );
    }

    // Attributo calcolato per il margine medio dell'ordine
    protected function averageMargin(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->items->count() === 0) {
                    return 0;
                }
                
                $totalMargin = $this->items->sum('margin');
                return round($totalMargin / $this->items->count(), 2);
            }
        );
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
