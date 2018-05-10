<?php

namespace App\Http\Controllers\Etsy2;

use App\EtsyStore;
use App\Http\Controllers\Etsy2Controller as Controller;
use App\Http\Controllers\Etsy2\ListingRemote;
use App\User;
use Illuminate\Support\Facades\Auth;
use OAuth\OAuth1\Service\Etsy;

class CommonController extends Controller
{

    /**
     * Standard check for current user to have access to required store
     * Variables are passed by reference
     *
     * @return bool
     * @param \App\User &$usr
     * @param \App\EtsyStore &$store
     * @param string $etsyStore
     * @param OAuth\OAuth1\Service\Etsy &$etsyService
     */
    public static function GetStore(
        \App\User &$usr = null,
        \App\EtsyStore &$store = null,
        string $etsyStore = "",
        \OAuth\OAuth1\Service\Etsy &$etsyService = null) {

        //standard checks
        $usr = \App\User::find(Auth::id())->first();
        $store = $usr->EtsyStores->where('name', $etsyStore)->first();

        //update store if needed
        if ($store === null) {
            EtsyStoreController::update_self($etsyService, $etsyStore);
            $usr->setRelationship([]);
            $store = $usr->EtsyStores->where('name', $etsyStore)->first();
        }
        return $store !== null;
    }

    /**
     * Get keyword function to download required listing for tag and title keywords
     *
     * @return bool
     * @param \OAuth\OAuth1\Service\Etsy $etsyService service for connection
     * @param string $keyword
     * @param array &$result_tags
     * @param array &$result_title
     * @param array &$result_title1
     * @param array &$result_title2
     * @param array &$result_title3
     * @param array &$result_title4
     * @param double $scale scale down weight every listing it goes through
     * @param int $max_size maximum number of listing to go through
     */
    public static function GetKeyword(
        \OAuth\OAuth1\Service\Etsy $etsyService,
        string $keyword,
        array &$result_tags,
        array &$result_title,
        array &$result_title1,
        array &$result_title2,
        array &$result_title3,
        array &$result_title4,
        double $scale = null,
        int $max_size = null

    ) { //example: https://openapi.etsy.com/v2/listings/active?api_key=ghj6sc6gns53oxzidk6s185j&fields=title&limit=10&sort_on=score&keywords=camping%20and%20beer
        //replace space with url string
        $keyword = str_replace(" ", "%20", $keyword);
        if ($scale == null) {$scale = 0.99;}
        if ($max_size == null) {$max_size = 100;}
        $rtn = [];

        //search keyword
        $result = ListingRemote::findListingActive($etsyService, $max_size, 0, 1,
            $keyword, null, ['sort_on' => 'score', 'sort_order' => 'down', 'fields' => "title,tags"]);
        if ($result == null) {return null;}

        //analyse tags for all listing
        $weight = 1;    //weight decrease for lower ranking listing
        foreach ($result['results'] as $r) {
            //no tags defined
            if (!isset($r['tags'])) {continue;};

            //go through tags
            foreach ($r['tags'] as $t) {
                $t = trim($t);
                if (strlen($t) < 1) {continue;}
                if (isset($result_tags[$t])) {
                    $result_tags[$t] += $weight;
                } else {
                    $result_tags[$t] = $weight;
                }
            }
            //change weight every time the rank decrease
            $weight *= $scale;
        }
        //order tags in order of total weight
        arsort($result_tags);


        //analyse title
        $weight = 1;
        for ($i = 0; $i < sizeof($result['results']); $i++) {
            //skip if no title
            if (!isset($result['results'][$i]['title'])) {continue;}
            
            //format symbol, treat all no alphanumerical as spacer
            $title = preg_replace('/[^a-zA-Z0-9\s]/', ',', $result['results'][$i]['title']);
            $tmp_title = explode(",", $title);

            //terms spaced by symbols
            foreach ($tmp_title as $t) {
                $t = trim($t);
                if (isset($result_title[$t])) {
                    $result_title[$t] += $weight;
                } else {
                    $result_title[$t] = $weight;
                }

                //individual words
                $word = explode(" ", $t);
                foreach ($word as $w) {
                    $w = trim($w);
                    if (isset($result_title1[$w])) {
                        $result_title1[$w] += $weight;
                    } else {
                        $result_title1[$w] = $weight;
                    }
                }

                //2 word
                if (sizeof($word) > 1) {
                    for ($j = 0; $j < sizeof($word) - 1; $j++) {
                        $w = trim($word[$j] . " " . $word[$j + 1]);
                        if (isset($result_title2[$w])) {
                            $result_title2[$w] += $weight;
                        } else {
                            $result_title2[$w] = $weight;
                        }

                    }
                }

                //3 word
                if (sizeof($word) > 2) {
                    for ($j = 0; $j < sizeof($word) - 2; $j++) {
                        $w = trim($word[$j] . " " . $word[$j + 1] . " " . $word[$j + 2]);
                        if (isset($result_title3[$w])) {
                            $result_title3[$w] += $weight;
                        } else {
                            $result_title3[$w] = $weight;
                        }

                    }
                }

                //4 word
                if (sizeof($word) > 3) {
                    for ($j = 0; $j < sizeof($word) - 3; $j++) {
                        $w = trim($word[$j] . " " . $word[$j + 1] . " " . $word[$j + 2] . " " . $word[$j + 3]);
                        if (isset($result_title4[$w])) {
                            $result_title4[$w] += $weight;
                        } else {
                            $result_title4[$w] = $weight;
                        }

                    }
                }
            }
            $weight *= $scale;
        }

        //sort result
        arsort($result_title);
        arsort($result_title1);
        arsort($result_title2);
        arsort($result_title3);
        arsort($result_title4);
        return true;
    }
}
