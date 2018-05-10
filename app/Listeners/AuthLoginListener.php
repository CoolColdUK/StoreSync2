<?php

    namespace App\Listeners;

//
//use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;

    class AuthLoginListener {

        /**
         * Create the event listener.
         *
         * @return void
         */
        public function __construct() {
            //
        }

        /**
         * Handle the event.
         *
         * @param  User  $user
         * @param  $remember
         * @return void
         */
        public function onLoggedIn($user) {
            event('menu.etsy.refresh');
            event('menu.pinterest.refresh');
        }

        /**
         * Handle the event.
         *
         * @param  User  $user
         * @param  $remember
         * @return void
         */
        public function onLoggedOut($user) {
            event('menu.etsy.expire');
            event('menu.pinterest.expire');
        }

    }
    