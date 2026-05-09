<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->date('period_start'); // inicio del período
            $table->date('period_end'); // fin del período
            $table->decimal('sales_amount', 10, 2)->default(0); // ventas del período
            $table->decimal('commission_amount', 10, 2)->default(0); // comisión sobre ventas
            $table->decimal('monthly_fee', 10, 2)->default(0); // costo fijo mensual
            $table->decimal('total_amount', 10, 2)->default(0); // total a cobrar
            $table->string('status')->default('pending'); // pending, paid, overdue
            $table->date('due_date')->nullable(); // fecha límite de pago
            $table->date('paid_date')->nullable(); // fecha en que pagó
            $table->text('notes')->nullable(); // notas del pago
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_cycles');
    }
};
