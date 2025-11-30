<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin' || auth()->user()->role === 'staff';
    }

    public function rules()
    {
        return [
            'status'   => 'required|in:pending,confirmed,cancelled,completed',
            'table_id' => 'nullable|exists:restaurant_tables,id',
        ];
    }
}
