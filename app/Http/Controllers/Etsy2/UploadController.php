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

class UploadController extends Controller
{

    /**
     * upload all listing to csv and pinterest if required
     *
     * @return \Illuminate\Http\Response
     * @param \Illuminate\Http\Request
     * @param string $etsyStore
     */
    public function upload(Request $request, string $etsyStore)
    {
        //load data from file
        //extract data
        //update db with csv file

        $param['store_name'] = $etsyStore;
        $param['table'] = [];

        //make sure file upload successful
        if ($request->csv == null) {
            return redirect()->route('etsy2');
        }

        //make connection and do background loading
        $etsyService = ConnectionController::GetPrivateService($etsyStore);
        if($etsyService==null){self::ErrorCannotConnect();}

        $sections = SectionRemote::toolShopSectionArray($etsyService, $etsyStore);

        $templates = ListingRemote::toolFindAllTemplate($etsyService, $etsyStore);
        $pinterestService = [];

        //convert csv to array
        $data = \App\Http\CcHelpers\CcFile::FileToAssocArray($request->csv->path());
        //loop through datay and process accordingly
        foreach ($data as $d) {
            //initialise feedback
            $feedback_row = [];
            $feedback_row['row'] = sizeof($param['table']) + 1;
            $feedback_row['id'] = "";
            $feedback_row['error'] = "";
            $feedback_row['etsy'] = "";
            $feedback_row['pinterest'] = "";

            $listing = EtsyListingCsvResource::fromCsv($d, $sections);
            if (strlen($listing->template) > 0 && isset($templates[$listing->template])) {
                $listing->fillTemplate($templates[$listing->template]);
            }
            
            //upload listing - skip upload listing if "title1" not exist
            if (isset($d['title1'])) {
                //TODO: upload image

                //upload listing
                try {
                    $result = ListingRemote::toolUploadListing(
                        $etsyService, EtsyListingEtsyResource::toEtsy($listing));
                } catch (Exception $e) {
                    print_r($e);exit;
                }
                
                if ($result > 0) {
                    //success
                    $feedback_row['id'] = $result['results'][0]['listing_id'];
                    $feedback_row['etsy'] = "Success";
                    
                    //upload inventory - only if it has template
                    if (strlen($listing->template) > 0 && isset($templates[$listing->template])) {
                        $result = ListingInventoryRemote::updateInventory($etsyService,
                            $result['results'][0]['listing_id'], $listing->inventory);
                    }
                } else {
                    //failed
                    $feedback_row['id'] = $listing->id;
                    $feedback_row['etsy'] = "Failed";
                    $feedback_row['error'] = "Cannot upload data";
                }
            }
            //pinterest, if account and board is defined and length>0
            if (
                isset($d['pinterest acc']) &&
                strlen($d['pinterest acc']) > 0 &&
                isset($d['pinterest board']) &&
                strlen($d['pinterest board']) > 0
            ) {
                $feedback_row['pinterest'] = "in";

                //try get service if not existed
                if (!isset($pinterestService[$d['pinterest acc']])) {
                    $pinterestService[$d['pinterest acc']] = PinterestConnectionController::GetPrivateService($d['pinterest acc']);
                }

                //if service created, try pin
                if ($pinterestService[$d['pinterest acc']] == null) {
                    $feedback_row['pinterest'] = "failed";
                    $feedback_row['error'] = "cannot connect to pinterest account";
                } else {
                    //$pinterst_listing = EtsyListingCsvResource::arrayFromArray($listing_arr,$sections);
                    PinterestPinRemote::createPin(
                        $pinterestService[$d['pinterest acc']],
                        $d['pinterest acc'],
                        $d['pinterest board'],
                        $listing['title'] . "\n" . $listing['description'],
                        $listing['url'],
                        $listing['image_url']
                    );
                    $feedback_row['pinterest'] = "success";
                }

            }
            //////////////////////////////finish process row
            $param['table'][] = $feedback_row;
        }

        return view('etsy2.import-result', $param);
    }

}
