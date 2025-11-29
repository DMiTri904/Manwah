<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;

class OrderItem extends Model
{

    use HasFactory;
    // public $timestamps = false;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'batch_number', 'batch_created_at', 'created_at', 'updated_at'];

    protected $casts = [
        'batch_created_at' => 'datetime',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
