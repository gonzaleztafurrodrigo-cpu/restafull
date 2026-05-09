<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantContract, TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'domain',
            'commission_percentage',
            'is_active',
            'plan',
            'monthly_fee',
            'billing_status',
            'billing_start_date',
            'next_billing_date',
            'last_paid_date',
        ];
    }

    // Verificar si está próximo a vencer (5 días o menos)
    public function isDueSoon(): bool
    {
        if (!$this->next_billing_date) return false;
        $daysUntilDue = now()->diffInDays($this->next_billing_date, false);
        return $daysUntilDue <= 5 && $daysUntilDue >= 0;
    }

    // Verificar si está vencido (pasó 5 días de gracia)
    public function isOverdue(): bool
    {
        if (!$this->next_billing_date) return false;
        $daysPastDue = now()->diffInDays($this->next_billing_date, false);
        return $daysPastDue < -5;
    }

    // Calcular monto proporcional del primer mes
    public static function calculateProportionalFee(float $monthlyFee, string $startDate): float
    {
        $start = \Carbon\Carbon::parse($startDate);
        $daysInMonth = $start->daysInMonth;
        $remainingDays = $daysInMonth - $start->day + 1;
        return round(($monthlyFee / $daysInMonth) * $remainingDays, 2);
    }
}
