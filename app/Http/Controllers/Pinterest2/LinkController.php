<?php

namespace App\Http\Controllers\Pinterest2;	
  

use App\Http\Controllers\Pinterest2Controller as Controller;
  
use Illuminate\Http\Request;
use App\Http\CcHelpers\CcArray;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\PinterestAccount;
use App\Http\Controllers\Pinterest2\ConnectionController;
use App\Http\Controllers\Pinterest2\UserRemote;
class LinkController extends Controller
{
    /**
     * Show the form for creating link to pinterest
     *
     * @return \Illuminate\Http\Redirect
     */
    public function create()
    {
        $pinterestService = ConnectionController::GetAuthService("/complete");
        $r = $pinterestService->getAuthorizationUri();

        //get temporary token and redirect
        return redirect($r);
    }

    /**
     * Store a newly created link
     *
     * @return \Illuminate\Http\Redirect
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        //////////////////////////Get access token///////////////////
        //if no code parameter, send back to start
        if (empty($_GET['code'])) {
            return redirect()->route('etsy2');
        }

        //get service variable
        $pinterestService = ConnectionController::GetAuthService("\complete");

        //get request token and secret from session/storage
        $storage = new \OAuth\Common\Storage\Session();

        //get access token from pinterest
        try {
            // This was a callback request from pinterest, get the token
            $access_token = $pinterestService->requestAccessToken(
                $_GET['code'], isset($_GET['state']) ? $_GET['state'] : null
            );
        } catch (\Exception $e) {
            //if failed, redirect back to pinterest/link

            return redirect()->route('pinterest2.link');
        }

        //////////////////////////get current user info from pin///////////////////
        // Send a request now that we have access token
        $account = UserRemote::getAccount($pinterestService);
        
        $m = new PinterestAccount();
        $m->fillAll($account['data']);
        /////////////////////////get current user permission////////////////////////
        $m->AttachWithDetail(Auth::id()
            , \Config::get('pinterest.api_key')
            , \Config::get('pinterest.api_secret')
            , $access_token->getAccessToken()
            , $access_token->getRefreshToken() == null ? "" : $access_token->getRefreshToken()
            , \Config::get('pinterest.permission'));

        //repopulate menu
        event('menu.pinterest.refresh');
        Session::save();

        //finish and back to main page
        return redirect()->route('etsy2');
    }

}
