<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Đảm bảo mảng $fillable chứa TẤT CẢ các trường bạn đang gửi
    protected $fillable = [
        'user_id',
        'table_id',
        'customer_name',
        'customer_phone',
        'num_guests',
        'reservation_time',
        'special_requests',
        'status',
    ];

    /**
     * Khai báo các thuộc tính nên được chuyển đổi kiểu dữ liệu.
     * Cần thiết để xử lý 'reservation_time' như một đối tượng DateTime.
     */
    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    // }

    // --- Mối quan hệ ---

    /**
     * Mối quan hệ với khách hàng (người dùng).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với bàn đã được chỉ định (RestaurantTable).
     * Dựa trên cột `table_id` trong schema.
     */
    public function restaurantTable()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }
}