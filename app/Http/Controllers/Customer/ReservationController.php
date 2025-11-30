<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function create()
    {
        // Trang form đặt bàn của khách
        return view('customer.reservations.create');
    }

    public function store(StoreReservationRequest $request)
{
    $validated = $request->validated();

    $dt = Carbon::parse($validated['reservation_time']);

    $data = [
        'user_id'             => Auth::check() ? Auth::id() : null, // nếu đang login thì gán user
        'customer_name'       => $validated['customer_name'],
        'customer_phone'      => $validated['customer_phone'],
        'restaurant_table_id' => null,
        'reservation_date'    => $dt->toDateString(),
        'reservation_time'    => $dt->format('H:i:s'),
        'guest_count'         => $validated['num_guests'],
        'status'              => 'pending',
        'special_requests'    => $validated['special_requests'] ?? null,
    ];

    $reservation = Reservation::create($data);

    return redirect()->route('reservations.success', $reservation->id);
}

    public function showSuccess($id)
    {
        $reservation = Reservation::with(['user', 'restaurantTable'])->findOrFail($id);

        return view('customer.reservations.success', compact('reservation'));
    }

    public function history()
    {
        abort_unless(Auth::check(), 403);

        $reservations = Reservation::where('user_id', Auth::id())
            ->orderByDesc('reservation_date')
            ->orderByDesc('reservation_time')
            ->get();

        return view('customer.reservations.history', compact('reservations'));
    }
}
