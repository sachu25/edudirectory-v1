<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        // Register Gates dynamically based on DB Permissions
        if (Schema::hasTable('permissions')) {
            try {
                $permissions = \App\Models\Permission::all();
                foreach ($permissions as $permission) {
                    \Illuminate\Support\Facades\Gate::define($permission->name, function ($user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            } catch (\Exception $e) {
                // Prevent migration bootstrap errors
            }
        }
    }
}
