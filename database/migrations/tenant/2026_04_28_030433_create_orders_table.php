<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->enum('type', ['table', 'delivery']);
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'dispatched',
                'delivered',
                'cancelled'
            ])->default('pending');
            $table->enum('payment_method', ['cash', 'transfer'])->nullable();
            $table->enum('payment_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('waiter_id')->nullable()->constrained('tenant_users')->onDelete('set null');
            $table->foreignId('cashier_id')->nullable()->constrained('tenant_users')->onDelete('set null');
            $table->foreignId('delivery_id')->nullable()->constrained('tenant_users')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
