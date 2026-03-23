<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_mode')->default('pos')->after('invoice_number');
            $table->string('payment_status')->default('paid')->after('transaction_mode');
            $table->string('customer_name')->nullable()->after('user_id');
            $table->timestamp('due_date')->nullable()->after('transacted_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_mode', 'payment_status', 'customer_name', 'due_date']);
        });
    }
};
