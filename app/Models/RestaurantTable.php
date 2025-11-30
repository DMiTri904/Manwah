<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = ['table_number', 'capacity', 'status', 'employee_id'];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'table_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
