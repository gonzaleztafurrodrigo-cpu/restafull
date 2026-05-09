<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan')->default('basic')->after('commission_percentage'); // basic o pro
            $table->decimal('monthly_fee', 10, 2)->default(0)->after('plan'); // costo fijo mensual
            $table->string('billing_status')->default('active')->after('monthly_fee'); // active, due_soon, overdue
            $table->date('billing_start_date')->nullable()->after('billing_status'); // fecha de inicio de facturación
            $table->date('next_billing_date')->nullable()->after('billing_start_date'); // próxima fecha de cobro
            $table->date('last_paid_date')->nullable()->after('next_billing_date'); // última fecha de pago
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['plan', 'monthly_fee', 'billing_status', 'billing_start_date', 'next_billing_date', 'last_paid_date']);
        });
    }
};
