<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->decimal('paid_amount', 14, 2);
            $table->decimal('change_amount', 14, 2);
            $table->unsignedInteger('item_count')->default(0);
            $table->string('notes')->nullable();
            $table->timestamp('transacted_at');
            $table->timestamps();

            $table->index('transacted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
