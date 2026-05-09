<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'delivery_cost')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('delivery_cost', 10, 2)->default(0)->after('subtotal');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_cost');
        });
    }
};
