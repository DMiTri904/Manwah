<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables'; // tên bảng
    protected $fillable = ['table_number','capacity','status'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
