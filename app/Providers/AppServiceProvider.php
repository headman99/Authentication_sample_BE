<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*Sanctum::authenticateAccessTokensUsing(function ($token, $isValid) {
            if($isValid) return true;
            return $token->can('remember') && $token->created_at->gt(now()->subYears(5));
        });*/
    }
}
