<?php


namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Etsy2Controller as Controller;

use App\Http\CcHelpers\CcArray;
use Illuminate\Support\Facades\Auth;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth1\Service\Etsy;
use OAuth\OAuth1\Token\StdOAuth1Token;

class ConnectionController extends Controller
{
    //

    const SERVICE = "Etsy";
    /**
     * Get the Etsy service for get access token
     *
     * @return ServiceInterface
     * @param string $append_return_path extra string to add to return path from oauth
     * @param string $base_uri api base url if different to default
     */
    public static function GetAuthService(string $append_return_path = "", string $base_uri = "")
    {
        //uri factory to make url
        $uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setPath($currentUri->getPath() . $append_return_path);
        $currentUri->setQuery('');

        // Session storage
        $storage = new Session();

        // Setup the credentials for the requests
        $credentials = new Credentials(
            \Config::get('etsy.api_key'), 
            \Config::get('etsy.api_secret'), 
            $currentUri->getAbsoluteUri()
        );

        // Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
        //** @var $etsyService Etsy */
        //
        $serviceFactory = new \OAuth\ServiceFactory();
        return $serviceFactory->createService(self::SERVICE
            , $credentials
            , $storage
            , null
            , strlen($base_uri) > 0 ? $base_uri : null);
    }

    /**
     * Get the Etsy service for public access i.e. no oauth token required
     *
     * @return \OAuth\OAuth1\Service\Etsy
     * @param string $base_uri
     */
    public static function GetPublicService(string $base_uri = null)
    {
        return self::GetAuthService("",$base_uri);
        //NOTE: previously used $serviceFactory->setHttpClient(new \OAuth\Common\Http\Client\CurlClient);
    }
    /**
     * Load the store linked to current user
     *
     * @return \App\EtsyStore
     * @param string $store_name
     */
    private static function LoadUserStore(string $store_name)
    {
        return \App\User::find(Auth::id())->EtsyStoresAll->where('name', $store_name)->first();
    }
    /**
     * Load the key from db into token
     *
     * @return OAuth\OAuth1\Token\StdOAuth1Token
     * @param \App\EtsyStore $store
     */
    private static function GetToken(\App\EtsyStore $store)
    {
        //set token into storage
        $tok = new StdOAuth1Token();
        try {
            $tok->setAccessToken($store->pivot->oauth_key);
            $tok->setAccessTokenSecret($store->pivot->oauth_secret);
        } catch (Exception $e) {
            return null;
        }
        return $tok;
    }
    /**
     * Get the Etsy oauth service for getting information available privately
     *
     * @return \OAuth\OAuth1\Service\Etsy
     * @param string $store_name
     * @param string $base_uri etsy api url base, if different to usual
     */
    public static function GetPrivateService($store_name, $base_uri = null)
    {
        //uri factory to make url
        $uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setQuery('');

        // load token everytime from db in case user changed the url directly
        $store = self::LoadUserStore($store_name);
        if ($store === null) {return null;}
        $tok = self::GetToken($store);
        if ($tok === null) {return null;}

        //make storage interface
        $storage = new Session();
        $storage->storeAccessToken(self::SERVICE, $tok);
        // Setup the credentials for the requests
        $credentials = new Credentials(
            $store->pivot->api_key,
            $store->pivot->api_secret,
            $currentUri->getAbsoluteUri()
        );

        // Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
        //** @var $etsyService Etsy */
        //
        $serviceFactory = new \OAuth\ServiceFactory();
        return $serviceFactory->createService(self::SERVICE
            , $credentials
            , $storage
            , null
            , $base_uri);
        //, strlen($base_uri) > 0 ?  new \OAuth\Common\Http\Uri($base_uri) : null);
    }
    /**
     * Send GET request to connection, using api key assume public connection
     *
     * @return array
     * @param \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param string $uri
     * @param array $param_arr parameter array if required
     */
    public static function RequestGetPublic(\OAuth\OAuth1\Service\Etsy &$etsyService, 
        string $uri, array $param_arr = null)
    {
        //api key in public query
        $param_arr['api_key']=\Config::get('etsy.api_key');
        return json_decode($etsyService->request($uri . "?" . CcArray::ToQueryStr($param_arr)), true);
    }
    /**
     * Send GET request to connection with oauth login
     *
     * @return array
     * @param \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param string $uri
     * @param array $param_arr parameter array if required
     */
    public static function RequestGet(\OAuth\OAuth1\Service\Etsy &$etsyService, 
        string $uri, array $param_arr = null)
    {
        //do not pass in api key if private/oauth get query
        return json_decode($etsyService->request($uri . "?" . CcArray::ToQueryStr($param_arr)), true);
    }

    /**
     * Send POST request to connection
     *
     * @return array
     * @param \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param string $uri
     * @param array $param_arr parameter array if required
     */
    public static function RequestPost(\OAuth\OAuth1\Service\Etsy &$etsyService, 
    string $uri, array $param_arr = null)
    {
        return json_decode($etsyService->request($uri, 'POST', $param_arr), true);
    }

    /**
     * Send PUT request to connection
     *
     * @return array
     * @param \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param string $uri
     * @param array $param_arr parameter array if required
     */
    public static function RequestPut(\OAuth\OAuth1\Service\Etsy &$etsyService, string $uri, $param_arr = null)
    {
        return json_decode($etsyService->request($uri, 'PUT',  $param_arr), true);
    }
}
