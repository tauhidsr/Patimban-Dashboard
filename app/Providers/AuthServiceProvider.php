<?php

namespace App\Providers;

use App\Models\PopulationEvent;
use App\Policies\PopulationEventPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        PopulationEvent::class => PopulationEventPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
