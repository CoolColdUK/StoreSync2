<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Etsy2Controller as Controller;
use App\Http\Controllers\Etsy2\ConnectionController;

use App\EtsyStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class UnlinkController extends Controller
{
    /**
     * Remove the link between the store and the user
     *
     * @param  string  $etsyStore
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $etsyStore)
    {
        //find store
        $store = EtsyStore::where('name', $etsyStore)->first();

        //detach store
        $store->Owner()->detach(Auth::id());

        //repopulate menu
        event('menu.etsy.refresh');
        Session::save();

        //finish and back to main page
        return redirect()->route('etsy2');
    }
}
