<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    protected $fillable = [
        'name',
        'type',        // peso / pezzo
        'unit',        // kg / pz
        'category_id', // collegamento alla categoria
        'sale_price',
        'cost_price',
        'stock_quantity',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'decimal:3',
    ];

    // Attributo calcolato per il margine di profitto
    protected function margin(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->sale_price || !$this->cost_price) {
                    return 0;
                }
                
                $profit = $this->sale_price - $this->cost_price;
                return round(($profit / $this->sale_price) * 100, 2);
            }
        );
    }

    // Attributo calcolato per il profitto per unità
    protected function unitProfit(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->sale_price || !$this->cost_price) {
                    return 0;
                }
                
                return round($this->sale_price - $this->cost_price, 2);
            }
        );
    }

    // Verifica se il prodotto è in stock
    protected function inStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock_quantity > 0
        );
    }

    // Verifica se il prodotto ha stock basso (sotto 5 unità)
    protected function lowStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock_quantity > 0 && $this->stock_quantity <= 5
        );
    }

    // Scope per prodotti in stock
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Scope per prodotti con stock basso
    public function scopeLowStock($query)
    {
        return $query->where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<=', 5);
    }

    // Validazioni personalizzate
    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            // Validazione prezzo di vendita > prezzo di acquisto
            if ($product->cost_price && $product->sale_price && $product->sale_price <= $product->cost_price) {
                throw new \InvalidArgumentException('Il prezzo di vendita deve essere maggiore del prezzo di acquisto.');
            }

            // Validazione stock quantity non negativo
            if ($product->stock_quantity < 0) {
                $product->stock_quantity = 0;
            }

            // Arrotondamento prezzi a 2 decimali
            if ($product->sale_price) {
                $product->sale_price = round($product->sale_price, 2);
            }
            if ($product->cost_price) {
                $product->cost_price = round($product->cost_price, 2);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
