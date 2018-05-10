<?php

namespace App\Http\Controllers\Pinterest2;	
  
use App\Http\Controllers\Pinterest2Controller as Controller;

use Illuminate\Http\Request;
use App\Http\CcHelpers\CcArray;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\PinterestAccount;
class UnlinkController extends Controller
{
    /**
     * Remove the specified link from db
     *
     * @return \Illuminate\Http\Redirect
     * @param  \App\PinterestAccount  $pinterestAccount
     */
    public function destroy(string $pinterestAccount)
    {
        //find store
        $acc = PinterestAccount::where('username', $pinterestAccount)->first();

        //detach store
        $acc->Owner()->detach(Auth::id());

        //repopulate menu
        event('menu.pinterest.refresh');
        Session::save();

        //finish and back to main page
        return redirect()->route('etsy2');
    }
}
