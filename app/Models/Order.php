<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model

{
    use HasFactory;

    // public $timestamps = false;

    protected $fillable = [
        'table_id',
        'user_id',
        'status',
        'total_amount',
        'payment_method',
        'transaction_id',
        'momo_request_id',
        'updated_at',
        'created_at',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class);
    }
      public function getCalculatedTotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
        public function getDisplayTotalAttribute()
    {
        $calculatedTotal = $this->calculated_total;
        
        // Nếu total trong DB khác với tính toán, có thể cập nhật lại
        if ($this->total != $calculatedTotal) {
            // Có thể tự động cập nhật ở đây nếu muốn
            // $this->update(['total' => $calculatedTotal]);
        }
        
        return $calculatedTotal > 0 ? $calculatedTotal : $this->total;
    }
        public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }
    
}
