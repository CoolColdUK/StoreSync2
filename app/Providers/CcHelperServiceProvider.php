<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CcHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //require_once app_path() . '/Http/CcHelpers/CcHelper.php';
        foreach (glob(app_path().'/Http/CcHelpers/*.php') as $filename){
            require_once($filename);
        }
    }
}
