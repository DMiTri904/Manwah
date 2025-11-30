<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function create()
    {
        return view('customer.create');
    }

    public function store(StoreReservationRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['reservation_time'] = $this->parseDate($validated['reservation_time']);

        $reservation = Reservation::create($validated);

        return redirect()->route('reservations.success', $reservation->id);
    }

    public function showSuccess($id)
    {
        $reservation = Reservation::findOrFail($id);
        return view('customer.reservation_success', compact('reservation'));
    }

    public function history()
    {
        abort_unless(Auth::check(), 403);

        $reservations = Reservation::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.reservations.history', compact('reservations'));
    }

    private function parseDate($time)
    {
        try {
            return Carbon::createFromFormat('m/d/Y h:i A', $time)->toDateTimeString();
        } catch (\Throwable) {
            return date('Y-m-d H:i:s', strtotime($time));
        }
    }
}
