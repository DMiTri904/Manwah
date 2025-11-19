<?php
// database/migrations/xxxx_add_momo_fields_to_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMomoFieldsToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('momo_transaction_id')->nullable()->comment('Mã giao dịch MoMo');
            $table->text('momo_response')->nullable()->comment('Phản hồi từ MoMo');
            $table->string('momo_request_id')->nullable()->comment('Mã yêu cầu gửi đến MoMo');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['momo_transaction_id', 'momo_response', 'momo_request_id']);
        });
    }
}