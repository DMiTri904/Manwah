<?php
// app/Models/Order.php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','table_id','status','total_amount'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class);
    }
}
// app/Models/OrderItem.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','product_id','quantity','price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
