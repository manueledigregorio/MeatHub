<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',         // 'in' o 'out'
        'quantity',
        'cost_price',
        'note',
        'order_id',
    ];

    public static function booted()
    {

        static::created(function ($stockMovement) {
            if ($stockMovement->type === 'in') {
                Product::where('id', $stockMovement->product_id)->increment('stock_quantity', $stockMovement->quantity);
            } else {
                Product::where('id', $stockMovement->product_id)
                    ->decrement('stock_quantity', $stockMovement->quantity);
            }
        });
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
