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
            'reservation_time' => 'required',
            'special_requests' => 'nullable|string|max:500',
        ];
    }
}
