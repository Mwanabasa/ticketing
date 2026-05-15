<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);

        $this->configureUrlForSubdirectoryHosting();
    }

    /**
     * Fix asset and route URLs when the app runs under a path (e.g. XAMPP
     * http://localhost/web/app/public) and ASSET_URL is not set.
     */
    private function configureUrlForSubdirectoryHosting(): void
    {
        if ($this->app->runningInConsole() || $this->app->make('config')->get('app.asset_url')) {
            return;
        }

        $request = $this->app->make('request');
        $script = str_replace('\\', '/', $request->getScriptName());
        $basePath = dirname($script);

        if (in_array($basePath, ['/', '\\', '.'], true)) {
            return;
        }

        $root = rtrim($request->getSchemeAndHttpHost().$basePath, '/');

        URL::forceRootUrl($root);
    }
}
