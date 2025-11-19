<?php
// database/migrations/2024_01_01_000000_add_payment_fields_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thêm các trường payment mới
            $table->string('payment_method')->nullable()->after('total_amount');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->string('momo_request_id')->nullable()->after('transaction_id');
            
            // Cập nhật trạng thái nếu cần (tuỳ chọn)
            // $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'transaction_id', 
                'momo_request_id'
            ]);
        });
    }
};