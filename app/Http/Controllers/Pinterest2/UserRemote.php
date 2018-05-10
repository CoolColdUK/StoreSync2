<?php

namespace App\Http\Controllers\Pinterest2;	
  
use App\Http\Controllers\Controller;

use App\Http\Controllers\Pinterest2\ConnectionController;

class UserRemote extends Controller
{
    /**
     * get pinterest account
     * 
     * @return pinterest account information
     * @param \OAuth\OAuth2\Service\Pinterest &$pinterestService
     */
    public static function getAccount(\OAuth\OAuth2\Service\Pinterest &$pinterestService) 
    {
        return ConnectionController::RequestGet(
            $pinterestService, 'v1/me/',
            ['fields' => [
                'id', 'username',
                'first_name', 'last_name',
                'url', 
                //'counts', 'image'
            ]]
        );
    }
}
