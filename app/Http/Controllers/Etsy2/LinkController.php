<?php

namespace App\Http\Controllers\Etsy2;

use App\EtsyStore;
use App\Http\Controllers\Etsy2Controller as Controller;
use App\Http\Controllers\Etsy2\ConnectionController;
use App\Http\Controllers\Etsy2\UserRemote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Http\Resources\EtsyStoreEtsyResource;

class LinkController extends Controller
{
    /**
     * Link etsy account to user account - start
     *
     * @return \Illuminate\Http\Redirect
     */
    public function create()
    {
       $etsyService = ConnectionController::GetAuthService("\complete");

        //set the scope required to get permission
        $etsyService->setScopes(\Config::get('etsy.permission'));

        //get temporary token and redirect
        $response = $etsyService->requestRequestToken();
        
        $extra = $response->getExtraParams();
        return redirect($extra['login_url']);
    }

    /**
     * store etsy account to user account
     *
     * @return \Illuminate\Http\Response
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        //if no oauth token, send back to start
        if (empty($_GET['oauth_token'])) {
            return redirect()->route('etsy2.link');
        }

        //get service variable
        $etsyService = ConnectionController::GetAuthService();

        //////////////////////////Get access token///////////////////
        //get request token and secret from session/storage
        $storage = new \OAuth\Common\Storage\Session();
        $token = $storage->retrieveAccessToken(self::SERVICE);

        //get access token from etsy
        try {
            $access_token = $etsyService->requestAccessToken(
                $_GET['oauth_token'], $_GET['oauth_verifier'], $token->getRequestTokenSecret()
            );
        } catch (\Exception $e) {
            //if failed, redirect back to etsy/link
            return redirect()->route('etsy2.link');
        }

        //////////////////////////get current user info from etsy///////////////////
        // Send a request now that we have access token
        //////////////////////////save/update user information////////////////
        $result = UserRemote::getUser($etsyService);

        //format data
        $estore = EtsyStoreEtsyResource::fromEtsy($result['results'][0]);
        
        $estore = \App\EtsyStore::updateOrCreate(
            ['id' => $estore->id],
            $estore->toArray());
        /////////////////////////get current user permission////////////////////////
        $result_permission = ConnectionController::RequestGet($etsyService, 
            '/oauth/scopes');
        $estore->AttachWithDetail(Auth::id()
            , \Config::get('etsy.api_key')
            , \Config::get('etsy.api_secret')
            , $access_token->getRequestToken()
            , $access_token->getRequestTokenSecret()
            , $result_permission['results']);

        //repopulate menu
        event('menu.etsy.refresh');
//        Session::save();

        //finish and back to main page
        return redirect()->route('etsy2');
    }

}
