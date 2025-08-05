<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',         // 'in' o 'out'
        'quantity',
        'cost_price',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
