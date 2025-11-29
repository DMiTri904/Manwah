<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use App\Models\User;
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

    // Danh sách bàn
    public function index()
    {
        // Eager load quan hệ employee
        $tables = RestaurantTable::with('employee')->orderBy('id')->get();

        return view('tables.index', compact('tables'));
    }


    // Form tạo mới
    public function create()
    {
        return view('tables.create');
    }

    // Lưu bàn mới
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|unique:restaurant_tables,table_number',
            'capacity' => 'required|numeric|min:1',
            // 'employee_id' => 'required|numeric',
            // 'employee_id' => 'nullable|numeric',

        ]);

        RestaurantTable::create([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'status' => 'available',
            // 'employee_id' => $request->employee_id,
            // 'employee_id' => $request->employee_id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tables.index')->with('success', 'Thêm bàn thành công');
    }

    // Form sửa
    public function edit($id)
    {
        $table = RestaurantTable::findOrFail($id);
        $employees = User::where('role', 'staff')->get();

        return view('tables.edit', compact('table', 'employees'));
    }

    // Cập nhật bàn
    public function update(Request $request, $id)
    {
        $table = RestaurantTable::findOrFail($id);

        $request->validate([
            'table_number' => 'required|unique:restaurant_tables,table_number,' . $id,
            'capacity' => 'required|numeric|min:1',
            'employee_id' => 'required|numeric',
        ]);

        $table->update([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->route('tables.index')->with('success', 'Cập nhật bàn thành công');
    }

    // Xóa bàn
    public function destroy($id)
    {
        RestaurantTable::destroy($id);
        return redirect()->route('tables.index')->with('success', 'Xóa bàn thành công');
    }
}
