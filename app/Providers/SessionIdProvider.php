<?php

namespace App\Providers;

use App\Views\Composers\SessionIdComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SessionIdProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', SessionIdComposer::class);
    }
}
