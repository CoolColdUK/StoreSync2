<?php

namespace App\Http\Controllers;

use App\EtsyStore;
use App\Http\CcHelpers\CcArray;
use App\Http\Controllers\EtsyListingInventoryRemote;
use App\Http\Controllers\EtsyListingRemote;
use App\Http\Resources\EtsyListingCsvResource;
use App\Http\Resources\EtsyListingEtsyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Pinterest2\PinRemote;
use OAuth\OAuth1\Token\StdOAuth1Token;

class Etsy2Controller extends Controller
{
    //

    const SERVICE = "Etsy";
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of etsy store.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $param['etsy_name_list']=Session::get('menu.etsy.stores');
        $param['pinterest_name_list']=Session::get('menu.pinterest.accounts');
        return view('etsy2.index', $param);
    }

    /**
     * Link etsy account to user account - start
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //// Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
        //** @var $etsyService Etsy */
        $etsyService = EtsyConnectionController::GetAuthService("\complete");

        //set the scope required to get permission
        $etsyService->setScopes(\Config::get('etsy.permission'));

        //get temporary token and redirect
        $response = $etsyService->requestRequestToken();
        //print_r($etsyService);exit;
        $extra = $response->getExtraParams();
        $url = $extra['login_url'];
        return redirect($url);
        // header('Location: ' . $url);
        // exit; //needed to prevent further processing
    }

    /**
     * store etsy account to user account
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Get access token
        //get current user info from etsy
        //get current user permission
        //save information to db using model
        //////////////////////////Get access token///////////////////
        //if no oauth token, send back to start
        if (empty($_GET['oauth_token'])) {
            header('Location: ' . route('etsy2.link'));
            exit; //needed to prevent further processing
        }

        //get service variable
        $etsyService = EtsyConnectionController::GetAuthService();

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
        //$result = EtsyStoreController::update_self($etsyService);
        $result=EtsyUserRemote::getUser($etsyService);

        //format data
        $estore = new EtsyStore();
        $estore->fillAll($result['results'][0]);

        $estore = \App\EtsyStore::updateOrCreate(
            ['id'=>$estore->id],
            $estore->toArray());
        //print_r($estore);exit;
        // $result_store = EtsyConnectionController::RequestGet(
        //     $etsyService, '/private/users/__SELF__');

        // $estore = EtsyStore::UpdateOrCreateFromJson($result_store['results'][0]);
        // $estore->id = $result_store['results'][0]['user_id'];
        /////////////////////////get current user permission////////////////////////
        $result_permission = EtsyConnectionController::RequestGet($etsyService, '/oauth/scopes');
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

    /**
     * Remove the link between the store and the user
     *
     * @param  string  $etsy_name
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $etsyStore)
    {
        //find store
        $store = EtsyStore::where('name', $etsyStore)->first();

        //detach store
        $store->Owner()->detach(Auth::id());

        //repopulate menu
        event('menu.etsy.refresh');
        Session::save();

        //finish and back to main page
        return redirect()->route('etsy2');
    }
    /**
     * Download all listing to csv
     *
     * @return \Illuminate\Http\Response
     */
    public function download(string $etsyStore)
    {
        //get private service->load user all checks login and store to exist
        $etsyService = EtsyConnectionController::GetPrivateService($etsyStore);

        //////////////////////////////////download all listing
        $active = EtsyListingRemote::findAllShopListingsActive(
            $etsyService, $etsyStore, null, null, ['includes' => ['Images']]);
        //$active = EtsyListingRemote::findShopListingsActive($etsyService, $etsyStore, 5);
        $sections = EtsySectionRemote::toolShopSectionArray($etsyService, $etsyStore);
        /////////////////combine listing
        $listings = $active['results'];

        /////////////////////////////////export to csv
        //get key for use in column name and data access
        //print $store_name;exit;
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
            $row = 1;
            //////////////////////////section
            //$etsyService = EtsyConnectionController::GetPrivateService($etsyStore);

            foreach ($listings as $listing) {
                $tmp = EtsyListingEtsyResource::fromEtsy($listing);
                //fputcsv($file, EtsyListingCsvResource::arrayToArray(EtsyListingEtsyResource::arrayFromArray($listing), $sections));
                fputcsv($file, EtsyListingCsvResource::toCsv($tmp, $sections));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download all listing to csv
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, string $etsyStore)
    {
        //$p = PinterestConnectionController::GetPrivateService('storesync');
        //load data from file
        //extract data
        //update db with csv file
        //var_dump(Input::file('csv')->getFilepath());
        // $file_data = file_get_contents($request->csv->path());
        // $file_array = str_getcsv($file_data);
        // print_r($file_array);

        $param['store_name'] = $etsyStore;
        $param['table'] = [];

        //make sure file upload successful
        if ($request->csv == null) {
            return redirect()->route('etsy2');
        }

        //make connection and do background loading
        $etsyService = EtsyConnectionController::GetPrivateService($etsyStore);
        //print_r($etsyService);
        //$p = PinterestConnectionController::GetPrivateService('storesync');
        // print_r($p);
        // exit;

        $sections = EtsySectionRemote::toolShopSectionArray($etsyService, $etsyStore);

        $templates = EtsyListingRemote::toolFindAllTemplate($etsyService, $etsyStore);
        //print_r($templates);exit;
        $pinterestService = [];

        //print_r($request->csv);exit;
        //convert csv to array
        $data = \App\Http\CcHelpers\CcFile::FileToAssocArray($request->csv->path());
// print_r($data);exit;
        //loop through datay and process accordingly
        foreach ($data as $d) {
            //initialise feedback
            $feedback_row = [];
            $feedback_row['row'] = sizeof($param['table']) + 1;
            $feedback_row['id'] = "";
            $feedback_row['error'] = "";
            $feedback_row['etsy'] = "";
            $feedback_row['pinterest'] = "";

            //replace section with section id
            //replace template here
            //EtsyListing::updateOrCreateFromCsv($d);

            //merge data with template
            //$d['template']='mug';
            // if (strlen($d['template']) > 0) {
            //     $listing->fill(EtsyListingCsvResource::mergeCsvWithTemplate($d, $sections, $templates));
            // } else {
            //     $listing->fill(EtsyListingCsvResource::arrayFromArray($d, $sections));
            // }
            //$d['template']='mug';
            //$listing_arr = EtsyListingCsvResource::mergeCsvWithTemplate($d, $sections, $templates);
            
            $listing = EtsyListingCsvResource::fromCsv($d, $sections);
            if (strlen($listing->template) > 0 && isset($templates[$listing->template])) {
                $listing->fillTemplate($templates[$listing->template]);
            }
            // print_r($templates[$listing->template]);
            //  print_r($listing);
            // exit;
            //upload listing - skip upload listing if "title1" not exist
            if (isset($d['title1'])) {
                //TODO: upload image

                //upload listing
                try {
                    //   print_r(EtsyListingEtsyResource::toEtsy($listing));exit;
                    $result = EtsyListingRemote::toolUploadListing(
                        $etsyService, EtsyListingEtsyResource::toEtsy($listing));
                } catch (Exception $e) {
                    print_r($e);exit;
                }
                //print_r($result);
                if ($result > 0) {
                    //success

                    // print_r($result);exit;
                    //$listing->fill(EtsyListingEtsyResource::arrayFromArray($result['results'][0]));

                    $feedback_row['id'] = $result['results'][0]['listing_id'];
                    $feedback_row['etsy'] = "Success";

                    // print_r($listing->inventory);
                    // exit;
                    //upload inventory - only if it has template
                    if (strlen($listing->template) > 0 && isset($templates[$listing->template])) {
                        $result = EtsyListingInventoryRemote::updateInventory($etsyService,
                            $result['results'][0]['listing_id'], $listing->inventory);
                    }
                    //print_r($result);exit;
                } else {
                    //failed
                    $feedback_row['id'] = $listing->id;
                    $feedback_row['etsy'] = "Failed";
                    $feedback_row['error'] = "Cannot upload data";
                }

            }
//             print_r($feedback_row);
            // exit;
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

    /**
     * download listing of given shop
     *
     * @return \Illuminate\Http\Response
     */
    public function shops(Request $request)
    {
        //$keyword = "'".$request->keyword."'";
        $etsyService = EtsyConnectionController::GetPublicService();
        $final = [];
        $shop = $request->shops;
        //////////////////////////////////download all listing
        $active = EtsyListingRemote::findAllShopListingsActive(
            $etsyService, $shop, null, null);
        //$active = EtsyListingRemote::findShopListingsActive($etsyService, $etsyStore, 5);
        $sections = EtsySectionRemote::toolShopSectionArray($etsyService, $shop);
        /////////////////combine listing
        $listings = $active['results'];

        /////////////////////////////////export to csv
        //get key for use in column name and data access
        //print $store_name;exit;
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
            //$etsyService = EtsyConnectionController::GetPrivateService($etsyStore);

            foreach ($listings as $listing) {
                $tmp = EtsyListingEtsyResource::fromEtsy($listing);
                //fputcsv($file, EtsyListingCsvResource::arrayToArray(EtsyListingEtsyResource::arrayFromArray($listing), $sections));
                fputcsv($file, EtsyListingCsvResource::toCsv($tmp, $sections));
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
    /**
     * keyword generation
     *
     * @return \Illuminate\Http\Response
     */
    public function keyword(Request $request)
    {

        //$keyword = "'".$request->keyword."'";
        $etsyService = EtsyConnectionController::GetPublicService();
        $final = [];
        $keywords = explode("\n", $request->keyword);
        $groups = explode("\n", $request->groups);
        $result_tags = [];
        $result_title = [];
        $result_title1 = [];
        $result_title2 = [];
        $result_title3 = [];
        $result_title4 = [];
        $b = false;
        foreach ($keywords as $k) {
            //if($b) continue;
            if (strlen($k) == 0) {continue;}
            $kk = trim(preg_replace('/[^a-zA-Z0-9-_\s]/', '', $k));
//print_r($kk);
            $result = EtsyCommonController::GetKeyword($etsyService, $kk,
                $result_tags, $result_title, $result_title1, $result_title2, $result_title3, $result_title4);
            $b = true;
            //merge result
        }

        $result = CcArray::TransposeArray(
            [array_keys($result_tags), array_values($result_tags),
                array_keys($result_title), array_values($result_title),
                array_keys($result_title1), array_values($result_title1),
                array_keys($result_title2), array_values($result_title2),
                array_keys($result_title3), array_values($result_title3),
                array_keys($result_title4), array_values($result_title4)]);
// exit( "attachment; filename=" . trim(str_replace(" ", "_", $keywords[0])) . ".csv");
        //format output
        //print_r($request->keyword );exit;
        //define csv data
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . trim(str_replace(" ", "_", $keywords[0])) . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $callback = function () use ($result) {
            //print_r($result_tags);exit;
            $file = fopen('php://output', 'w');
            //$keys = array_keys($result);
            fputcsv($file, ['tags', 'tag count',
                'title', 'title count',
                'title1', 'title1 count',
                'title2', 'title2 count',
                'title3', 'title3 count',
                'title4', 'title4 count',
            ]);
            // $t_tags = array_keys($result_tags);
            // $t_title = array_keys($result_title);
            // $t_title1 = array_keys($result_title1);
            // $t_title2 = array_keys($result_title2);
            // $t_title3 = array_keys($result_title3);
            // $t_title4 = array_keys($result_title4);
            // print_r($result);exit;
            // for ($i = 0; $i < sizeof($result[$keys[0]]); $i++) {
            //     $output = [];
            //     foreach ($keys as $k) {
            //         $output[] = $result[$k][$i];
            //     }
            //     // print_r($output);
            //     // print_r($keys);exit;
            //     fputcsv($file, $output);
            // }
            foreach ($result as $r) {
                fputcsv($file, $r);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
