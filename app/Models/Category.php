<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Category extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'name', 'description'
    ];
    protected $hidden = [
        'remember_token',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function products() {
        return $this->hasMany(Product::class);
    }

    
}
