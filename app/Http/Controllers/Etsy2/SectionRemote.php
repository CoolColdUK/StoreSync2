<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Etsy2\ConnectionController;

class SectionRemote extends Controller
{
    //
    /**
     * find all shop sections
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     */
    public static function findAllShopSections(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name)
    {
        return ConnectionController::RequestGetPublic($etsyService,
            '/shops/' . $shop_id_or_name . '/sections');
    }

    /**
     * find all shop sections and convert to associate array using section id as key
     *
     * @return array
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     */
    public static function toolShopSectionArray(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name)
    {
        $sections = self::findAllShopSections($etsyService, $shop_id_or_name);
        $section_arr = [];
        foreach ($sections['results'] as $section) {
            $section_arr[$section['shop_section_id']] = $section['title'];
        }
        return $section_arr;
    }
}
