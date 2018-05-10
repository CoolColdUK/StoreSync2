<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Etsy2Controller as Controller;

//etsy2 controllers
use App\Http\Controllers\Etsy2\ListingInventoryRemote;
use App\Http\Controllers\Etsy2\ListingRemote;

//pinterest controllers
use App\Http\Controllers\Pinterest2\ConnectionController as PinterestConnectionController;
use App\Http\Controllers\Pinterest2\PinRemote as PinterestPinRemote;

use App\EtsyStore;
use App\Http\CcHelpers\CcArray;
use App\Http\Resources\EtsyListingCsvResource;
use App\Http\Resources\EtsyListingEtsyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use OAuth\OAuth1\Token\StdOAuth1Token;

class ResearchStoreController extends Controller
{
    //

    /**
     * download listing of given shop
     *
     * @return \Illuminate\Http\Response
     */
    public function shops(Request $request)
    {
        $etsyService = ConnectionController::GetPublicService();
        if($etsyService==null){self::ErrorCannotConnect();}
        $final = [];
        $shop = $request->shops;
        //////////////////////////////////download all listing
        $active = ListingRemote::findAllShopListingsActive(
            $etsyService, $shop, null, null);
        //$active = EtsyListingRemote::findShopListingsActive($etsyService, $etsyStore, 5);
        $sections = SectionRemote::toolShopSectionArray($etsyService, $shop);
        /////////////////combine listing
        $listings = $active['results'];

        /////////////////////////////////export to csv
        //get key for use in column name and data access
        //populate csv with listing
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $shop . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $callback = function () use ($listings, $sections) {
            $file = fopen('php://output', 'w');
            fputcsv($file, EtsyListingCsvResource::$COLUMNS);
            $row = 1;
            //////////////////////////section

            foreach ($listings as $listing) {
                $tmp = EtsyListingEtsyResource::fromEtsy($listing);
                
                fputcsv($file, EtsyListingCsvResource::toCsv($tmp, $sections));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
