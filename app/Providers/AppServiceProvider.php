<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenantCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(TenantCreated::class, function (TenantCreated $event) {
            $tenant = $event->tenant;

            Artisan::call('tenants:migrate', [
                '--tenants' => [$tenant->id],
                '--force' => true,
            ]);

            tenancy()->initialize($tenant);
            Artisan::call('db:seed', [
                '--class' => 'TenantDatabaseSeeder',
                '--force' => true,
            ]);
            tenancy()->end();
        });
    }
}
