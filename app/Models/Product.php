<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'min_stock_threshold',
    ];

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