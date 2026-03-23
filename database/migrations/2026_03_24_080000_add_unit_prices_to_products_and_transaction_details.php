<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_per_unit', 14, 2)->default(0)->after('price');
            $table->decimal('price_per_pack', 14, 2)->default(0)->after('price_per_unit');
            $table->decimal('price_per_dozen', 14, 2)->default(0)->after('price_per_pack');
        });

        DB::table('products')->update([
            'price_per_unit' => DB::raw('price'),
            'price_per_pack' => DB::raw('price * 10'),
            'price_per_dozen' => DB::raw('price * 12'),
        ]);

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('unit_type')->default('satuan')->after('product_code');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn('unit_type');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_per_unit', 'price_per_pack', 'price_per_dozen']);
        });
    }
};
