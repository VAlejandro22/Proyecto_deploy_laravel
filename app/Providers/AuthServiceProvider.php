<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Factura;
use App\Policies\FacturaPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Map model policies to their Policy classes.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Factura::class => FacturaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Aquí puedes definir Gates adicionales si los necesitas:
        // Gate::define('something', fn(User $user) => ...);
    }
}
