<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TableService;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    protected $tableService;

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
        // Danh sách bàn khả dụng + bàn đang gán
        $tables = \App\Models\RestaurantTable::where('status', 'available')
            ->orWhere('id', $reservation->table_id)
            ->get();

        return view('admin.reservations.edit', compact('reservation', 'tables'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $oldTable = $reservation->table_id;
            $oldStatus = $reservation->status;

            $newStatus = $validated['status'];
            $newTable = $validated['table_id'] ?? null;

            // =============== VALIDATION QUAN TRỌNG ===============
            // Nếu admin chọn "Xác nhận" nhưng chưa chọn bàn → báo lỗi
            if ($newStatus === ReservationStatus::CONFIRMED && !$newTable) {
                throw new \Exception("Vui lòng chọn bàn khi xác nhận đơn.");
            }

            // Cập nhật trước
            $reservation->update($validated);

            // =============== GIẢI PHÓNG BÀN CŨ ===============
            if ($oldTable && $oldStatus === ReservationStatus::CONFIRMED) {

                $shouldReleaseOldTable =
                    $newStatus !== ReservationStatus::CONFIRMED     // đổi sang cancelled/completed/pending
                    || ($newStatus === ReservationStatus::CONFIRMED && $oldTable != $newTable); // đổi bàn

                if ($shouldReleaseOldTable) {
                    $this->tableService->releaseTable($oldTable);
                }
            }

            // =============== GÁN BÀN MỚI ===============
            if ($newStatus === ReservationStatus::CONFIRMED) {

                // Chống DOUBLE BOOKING: kiểm tra bàn vẫn còn available
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

            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
