<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Etsy2\ConnectionController;

class UserRemote extends Controller
{
    //
    /**
     * get user account
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $user_id_or_name
     */
    public static function getUser(\OAuth\OAuth1\Service\Etsy &$etsyService, string $user_id_or_name = "")
    {
        if (strlen($user_id_or_name) == 0) {
            return ConnectionController::RequestGet($etsyService, '/private/users/__SELF__');
        }
        return ConnectionController::RequestGet($etsyService, '/users/' . $user_id_or_name);
    }
    /**
     * Find all users
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $keyword
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     */
    public static function findAllUsers(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $keyword = "", int $limit = 25, int $offset = 0, int $page = 1) {
        if ($limit < 1) {$limit = 1;}
        if ($offset < 0) {$offset = 0;}
        if ($page < 1) {$page = 1;}

        $param['keyword'] = $keyword;
        $param['limit'] = $limit;
        $param['offset'] = $offset;
        $param['page'] = $page;
        return ConnectionController::RequestGet($etsyService, '/users/', $param);
    }
}
