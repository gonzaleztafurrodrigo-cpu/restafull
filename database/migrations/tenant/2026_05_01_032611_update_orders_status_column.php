<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'dispatched',
                'collecting',
                'delivered',
                'cancelled'
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'dispatched',
                'delivered',
                'cancelled'
            ])->change();
        });
    }
};
