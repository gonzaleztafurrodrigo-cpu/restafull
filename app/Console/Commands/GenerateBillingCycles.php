<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class GenerateBillingCycles extends Command
{
    protected $signature = 'billing:generate-cycles';
    protected $description = 'Genera ciclos de facturación para el mes actual y actualiza estados';

    public function handle()
    {
        $tenants = Tenant::where('is_active', true)->get();
        $today = now();

        foreach ($tenants as $tenant) {
            // Actualizar estados de facturación
            // Actualizar estados de facturación
            if ($tenant->next_billing_date) {
                $paymentDeadline = \Carbon\Carbon::parse($tenant->next_billing_date)
                    ->addMonth()->startOfMonth()->addDays(4);

                $daysUntilDeadline = $today->diffInDays($paymentDeadline, false);
                $daysUntilCut = $today->diffInDays($tenant->next_billing_date, false);

                if ($daysUntilDeadline < 0) {
                    $tenant->update(['billing_status' => 'overdue', 'is_active' => false]);
                    $this->warn("⚠ {$tenant->name} — VENCIDO y desactivado.");
                } elseif ($daysUntilCut <= 5 && $daysUntilCut >= 0) {
                    $tenant->update(['billing_status' => 'due_soon']);
                    $this->info("⏰ {$tenant->name} — próximo a vencer.");
                } elseif ($tenant->billing_status !== 'active' && $tenant->last_paid_date) {
                    $paidAfterCut = \Carbon\Carbon::parse($tenant->last_paid_date)
                        ->gte(\Carbon\Carbon::parse($tenant->next_billing_date)->startOfMonth());
                    if ($paidAfterCut) {
                        $tenant->update(['billing_status' => 'active', 'is_active' => true]);
                    }
                }
            }

            if ($today->day === 1) {
                // El ciclo que se genera es del mes ANTERIOR
                $periodStart = $today->subMonth()->startOfMonth()->toDateString();
                $periodEnd = $today->subMonth()->endOfMonth()->toDateString();
                $today = now(); // restaurar

                $exists = DB::table('billing_cycles')
                    ->where('tenant_id', $tenant->id)
                    ->where('period_start', $periodStart)
                    ->exists();

                if (!$exists) {
                    DB::table('billing_cycles')->insert([
                        'tenant_id' => $tenant->id,
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'sales_amount' => 0,
                        'commission_amount' => 0,
                        'monthly_fee' => $tenant->monthly_fee,
                        'total_amount' => $tenant->monthly_fee,
                        'status' => 'pending',
                        'due_date' => $periodEnd,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Actualizar próxima fecha de cobro
                    $tenant->update([
                        'next_billing_date' => now()->endOfMonth()->toDateString(),
                        'billing_status' => 'active',
                    ]);

                    $this->info("✓ {$tenant->name} — ciclo generado: {$periodStart} — {$periodEnd}");
                }
            }
        }

        $this->info('Proceso completado.');
    }
}
