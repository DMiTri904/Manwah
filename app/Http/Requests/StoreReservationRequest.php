<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Khách nào cũng gửi được
    }

    public function rules()
    {
        return [
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => 'required|string|max:20',
            'num_guests'       => 'required|integer|min:1',

            // reservation_time gửi lên dạng: 2025-11-30T19:30
            'reservation_time' => [
                'required',
                'date_format:Y-m-d\TH:i', // đúng format của input datetime-local
                'after:now',              // không cho chọn thời điểm trong quá khứ
            ],

            'special_requests' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'reservation_time.required'    => 'Vui lòng chọn ngày và giờ đặt bàn.',
            'reservation_time.date_format' => 'Thời gian đặt bàn không hợp lệ.',
            'reservation_time.after'       => 'Không thể đặt bàn cho thời điểm đã qua.',
        ];
    }
}
