<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TableService;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    protected TableService $tableService;

    public function __construct(TableService $tableService)
    {
        $this->middleware(['auth', 'role:admin,staff']);
        $this->tableService = $tableService;
    }

    public function index()
    {
        $reservations = Reservation::with(['user', 'restaurantTable'])
            ->orderByRaw("FIELD(status, 'pending', 'confirmed', 'cancelled', 'completed')")
            ->orderBy('reservation_time')
            ->paginate(15);

        return view('admin.reservations.index', compact('reservations'));
    }

    public function edit(Reservation $reservation)
    {
        $tables = \App\Models\RestaurantTable::where('status', 'available')
            ->orWhere('id', $reservation->restaurant_table_id)
            ->get();

        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $oldTable  = $reservation->restaurant_table_id;
            $oldStatus = $reservation->status;    // 'pending' / 'confirmed' / ...

            $newStatus = $validated['status'];    // string
            $newTable  = $validated['table_id'] ?? null;

            // Nếu admin chọn "Đã xác nhận" mà không chọn bàn -> báo lỗi
            if ($newStatus === 'confirmed' && !$newTable) {
                throw new \Exception("Vui lòng chọn bàn khi xác nhận đơn.");
            }

            // Cập nhật trước
            $reservation->update([
                'status'              => $newStatus,
                'restaurant_table_id' => $newTable,
            ]);

            // Giải phóng bàn cũ nếu trước đó là confirmed
            if ($oldTable && $oldStatus === 'confirmed') {

                $shouldReleaseOldTable =
                    $newStatus !== 'confirmed' ||
                    ($newStatus === 'confirmed' && $oldTable != $newTable);

                if ($shouldReleaseOldTable) {
                    $this->tableService->releaseTable($oldTable);
                }
            }

            // Gán bàn mới nếu trạng thái là confirmed
            if ($newStatus === 'confirmed' && $newTable) {
                $assignResult = $this->tableService->assignTable($reservation, $newTable);

                if (!$assignResult) {
                    throw new \Exception("Bàn này đã được người khác chọn trước! Vui lòng chọn bàn khác.");
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.reservations.index')
                ->with('success', "Đã cập nhật đơn #{$reservation->id}");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Reservation update error: " . $e->getMessage());

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
