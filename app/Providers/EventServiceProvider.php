<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Ici tu peux lister les événements et leurs listeners si tu en as
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
