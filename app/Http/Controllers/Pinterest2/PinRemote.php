<?php

namespace App\Http\Controllers\Pinterest2;	
  
use App\Http\Controllers\Controller;


use App\Http\Controllers\Pinterest2\ConnectionController;
use Illuminate\Http\Request;

class PinRemote extends Controller
{
    public static $wl_pin=['board','note','link','image_url'];

    
    /**
     * create a new pin
     *
     * @param  \OAuth\OAuth2\Service\Pinterest &$pinService
     * @param string $username
     * @param string $board_name
     * @param string $note
     * @param string $link
     * @param string $image_url
     * @return \Illuminate\Http\Redirect
     */
    public static function createPin(\OAuth\OAuth2\Service\Pinterest &$pinService,
        string $username, string $board_name, string $note, string $link, string $image_url) {
        $pin['board']=$username."/".$board_name;
        $pin['note']=$note;
        $pin['link']=$link;
        $pin['image_url']=$image_url;
        return ConnectionController::RequestPost($pinService, '/v1/pins/', $pin);
    }
}
