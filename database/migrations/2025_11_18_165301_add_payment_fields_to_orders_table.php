<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
     {
         Schema::table('orders', function (Blueprint $table) {
             $table->string('payment_method')->nullable()->after('status'); // Phương thức thanh toán (momo, cash, v.v.)
             $table->string('transaction_id')->nullable()->after('payment_method'); // Mã giao dịch từ MoMo hoặc giả lập
         });
     }

     public function down()
     {
         Schema::table('orders', function (Blueprint $table) {
             $table->dropColumn(['payment_method', 'transaction_id']);
         });
     }
     
};
