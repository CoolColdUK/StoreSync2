<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Etsy2Controller as Controller;
use App\Http\Controllers\Etsy2\ListingRemote;
use App\Http\Controllers\Etsy2\ListingInventoryRemote;
use App\Http\Controllers\Etsy2\SectionRemote;

use App\EtsyStore;
use App\Http\CcHelpers\CcArray;

use App\Http\Controllers\EtsyListingInventoryRemote;
use App\Http\Controllers\EtsyListingRemote;
use App\Http\Resources\EtsyListingCsvResource;
use App\Http\Resources\EtsyListingEtsyResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use OAuth\OAuth1\Token\StdOAuth1Token;

class DownloadController extends Controller
{
    //

    /**
     * Download all listing to csv
     *
     * @return \Illuminate\Http\Response
     * @param string $etsyStore
     */
    public function download(string $etsyStore)
    {
        //get private service->load user all checks login and store to exist
        $etsyService = ConnectionController::GetPrivateService($etsyStore);
        
        //////////////////////////////////download all listing and sections
        $active = ListingRemote::findAllShopListingsActive(
            $etsyService, $etsyStore, null, null, ['includes' => ['Images']]);
        //$active = EtsyListingRemote::findShopListingsActive($etsyService, $etsyStore, 5);
        $sections = SectionRemote::toolShopSectionArray($etsyService, $etsyStore);
        
        /////////////////combine listing
        $listings = $active['results'];

        /////////////////////////////////export to csv
        //get key for use in column name and data access
        //populate csv with listing
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $etsyStore . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $callback = function () use ($listings, $sections) {
            $file = fopen('php://output', 'w');
            fputcsv($file, EtsyListingCsvResource::$COLUMNS);

            foreach ($listings as $listing) {
                $tmp = EtsyListingEtsyResource::fromEtsy($listing);
                
                fputcsv($file, EtsyListingCsvResource::toCsv($tmp, $sections));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
