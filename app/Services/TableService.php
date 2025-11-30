<?php

namespace App\Services;

use App\Models\RestaurantTable;
use App\Models\Reservation;

class TableService
{
    /**
     * Gán bàn mới – có check double booking + check trùng giờ.
     */
    public function assignTable(Reservation $reservation, $tableId)
    {
        $table = RestaurantTable::findOrFail($tableId);

        if ($this->isTableTaken($tableId, $reservation->reservation_time, $reservation->id)) {
            throw new \Exception("Bàn này đã được đặt cho thời điểm này.");
        }

        // Chỉ update khi bàn available
        $updated = RestaurantTable::where('id', $tableId)
            ->where('status', 'available')
            ->update(['status' => 'reserved']);

        if ($updated == 0) {
            throw new \Exception("Bàn này không còn khả dụng.");
        }
    }

    /**
     * Giải phóng bàn cũ.
     */
    public function releaseTable($tableId)
    {
        RestaurantTable::where('id', $tableId)
            ->where('status', 'reserved')
            ->update(['status' => 'available']);
    }

    /**
     * Kiểm tra bàn đã được đặt trong thời điểm đó chưa.
     */
    public function isTableTaken($tableId, $startTime, $ignoreReservationId = null)
    {
        return Reservation::where('table_id', $tableId)
            ->where('reservation_time', $startTime)
            ->where('status', 'confirmed')
            ->when($ignoreReservationId, fn($q) =>
                $q->where('id', '!=', $ignoreReservationId))
            ->exists();
    }
}
