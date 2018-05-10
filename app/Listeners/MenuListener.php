<?php //

namespace App\Listeners;
//
//use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;

class MenuListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  User  $user
     * @param  $remember
     * @return void
     */
//    public function onMenuClear($user)
//    {
//        Event::fire('menu.*.expired');
//        //
//        //$store = collect(\App\User::EtsyStoreNameList(\Illuminate\Support\Facades\Auth::id()));
//        //session('menu_etsy_stores', $store);
//    }
    
    /**
     * Etsy menu expired, reload data to session
     *
     * @param  User  $user
     * @param  $remember
     * @return void
     */
    public function onMenuEtsyExpired()
    {
        //session('menu.etsy.stores', '');
        //Session(['menu.etsy.stores' => collect(\App\User::EtsyStoreNameList(\Illuminate\Support\Facades\Auth::id()))]);
    }
}
