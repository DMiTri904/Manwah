<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Category;
use App\Models\Order;



class Product extends Model
{
    use  HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'description', 'price', 'stock', 'category_id'
    ];

    protected $hidden = [
        'remember_token',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }


}
