<?php

    namespace App\Providers;

    use Illuminate\Support\Facades\Event;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

    class EventServiceProvider extends ServiceProvider {

        /**
         * The event listener mappings for the application.
         *
         * @var array
         */
        protected $listen = [
            'Illuminate\Auth\Events\Login' => [
                'App\Listeners\AuthLoginListener@onLoggedIn'
            ],
            'Illuminate\Auth\Events\Logout' => [
                'App\Listeners\AuthLoginListener@onLoggedOut'
            ],
        ];

        /**
         * Register any events for your application.
         *
         * @return void
         */
        public function boot() {
            parent::boot();

            //expire menu will clear it
            Event::listen('menu.etsy.expire', function () {
                Session(['menu.etsy.stores' => '']);
            });

            //refresh menu will reload it
            Event::listen('menu.etsy.refresh', function () {
                $s = \App\User::EtsyStoreNameList(\Illuminate\Support\Facades\Auth::id());
                
                if (sizeof($s) == 0) {
                    Session(['menu.etsy.stores' => '']);
                } else {
                    Session(['menu.etsy.stores' => collect($s)]);
                }
            });

            //expire menu will clear it
            Event::listen('menu.pinterest.expire', function () {
                Session(['menu.pinterest.accounts' => '']);
            });

            //refresh menu will reload it
            Event::listen('menu.pinterest.refresh', function () {
                $s = \App\User::PinterestAccountNameList(\Illuminate\Support\Facades\Auth::id());
                if (sizeof($s) == 0) {
                    Session(['menu.pinterest.accounts' => '']);
                } else {
                    Session(['menu.pinterest.accounts' => collect($s)]);
                }
            });
            Session::save();
        }

    }
    