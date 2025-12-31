<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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
        //
        // Apply only if using MySQL
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::connection()->getPdo()->exec("SET SESSION wait_timeout=58800");
                DB::connection()->getPdo()->exec("SET SESSION interactive_timeout=58800");
            } catch (\Exception $e) {
                logger()->error('Failed to set MySQL timeout settings: ' . $e->getMessage());
            }
        }
    }
}
