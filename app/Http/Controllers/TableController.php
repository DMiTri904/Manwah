<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    // Hiển thị danh sách bàn
    public function choose()
    {
        $tables = RestaurantTable::orderBy('table_number')->get();
        return view('tables.choose_table', compact('tables'));
    }

    // Chọn bàn
    public function setTable(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
        ]);

        DB::beginTransaction();
        try {
            $table = RestaurantTable::lockForUpdate()->find($request->table_id);

            if ($table->status === 'occupied') {
                return back()->with('error', 'Bàn đang có khách! Không thể chọn.');
            }

            // Cập nhật bàn
            $table->status = 'occupied';

            // Gán ID nhân viên, không phải tên
            if (auth()->check()) {
                $table->employee_id = auth()->id();
            }

            $table->save();

            // Lưu vào session
            session(['table_id' => $table->id]);

            DB::commit();
            return redirect()->route('table.menu', $table->id)
                ->with('success', "Đã chọn bàn {$table->table_number} thành công!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    // Giải phóng bàn khi thanh toán xong
    public function releaseTable($id)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->status = 'available';
        $table->employee_id = null;
        $table->save();

        session()->forget('table_id');

        return back()->with('success', "Bàn {$table->table_number} đã được giải phóng!");
    }
}
