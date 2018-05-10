<?php
namespace App\Http\Controllers\Pinterest2;	

use App\Http\Controllers\Pinterest2Controller as Controller;
  

use Illuminate\Support\Facades\Auth;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\Pinterest;
use OAuth\OAuth2\Token\StdOAuth2Token;
use App\Http\CcHelpers\CcArray;

class ConnectionController extends Controller
{
    /**
     * Get the pinterest service for get access token/authorisation
     *
     * @return ServiceInterface
     * @param string $append_return_path
     * @param string $base_uri
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
            \Config::get('pinterest.api_key'),
            \Config::get('pinterest.api_secret'),
            preg_replace('$http://$', 'https://', $currentUri->getAbsoluteUri())
        );
        //print_r(\Config::get('pinterest.api_key'));exit;
        // Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
        //** @var $etsyService Etsy */
        //
        $serviceFactory = new \OAuth\ServiceFactory();
        $serviceFactory->setHttpClient(new CurlClient);
        return $serviceFactory->createService(self::SERVICE
            , $credentials
            , $storage
            , \Config::get('pinterest.permission'));
    }

    /**
     * Get the pinterest service for public access i.e. no oauth token required
     *
     * @return \OAuth\OAuth2\Service\PInterest
     * @param string $base_uri
     */
    public static function GetPublicService(string $base_uri = null)
    {
        //uri factory to make url
        $uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setQuery('');

        // Setup the credentials for the requests
        // always use default api for public service
        $credentials = new Credentials(
            \Config::get('pinterest.api_key'),
            \Config::get('pinterest.api_secret'),
            preg_replace('$http://$', 'https://', $currentUri->getAbsoluteUri())
        );

        // Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
        //** @var $etsyService Etsy */
        // curl do not need oauth access token
        $serviceFactory = new \OAuth\ServiceFactory();
        $serviceFactory->setHttpClient(new \OAuth\Common\Http\Client\CurlClient);
        $rtn = $serviceFactory->createService(self::SERVICE
            , $credentials
            , new Session()
            /*, null
        , ($base_uri === null) ? null : $uriFactory->createFromAbsolute($base_uri)*/
        );
        return $rtn;
    }
    /**
     * Load the store linked to current user
     *
     * @return \App\PinterestAccount
     * @param string $account_name
     */
    private static function LoadUserAccount(string $account_name)
    {
        return \App\User::find(Auth::id())->PinterestAccountsAll->where('username', $account_name)->first();
    }
    /**
     * Load the key from db into token
     *
     * @return OAuth\OAuth1\Token\StdOAuth1Token
     * @param \App\PinterestAccount $acc
     */
    private static function GetToken(\App\PinterestAccount $acc)
    {
        //set token into storage
        $tok = new StdOAuth2Token();
        try {
            $tok->setAccessToken($acc->pivot->access_token);
            $tok->setRefreshToken($acc->pivot->refresh_token);
        } catch (Exception $e) {
            return null;
        }
        return $tok;
    }
    /**
     * Get the Etsy oauth service for getting information available to public
     *
     * @return \OAuth\OAuth1\Service\Etsy
     * @param string $acc_name
     * @param string $base_uri
     */
    public static function GetPrivateService(string $acc_name, string $base_uri = null)
    {
        //uri factory to make url
        $uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
        $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
        $currentUri->setQuery('');

        // load token everytime from db in case user changed the url directly
        $acc = self::LoadUserAccount($acc_name);
        if ($acc === null) {return null;}
        $tok = self::GetToken($acc);
        if ($tok === null) {return null;}

        //make storage interface
        $storage = new Session();
        $storage->storeAccessToken(self::SERVICE, $tok);
        // Setup the credentials for the requests
        $credentials = new Credentials(
            $acc->pivot->api_key,
            $acc->pivot->api_secret,
            //\Config::get('etsy.api_key'),
            //\Config::get('etsy.api_secret'),
            $currentUri->getAbsoluteUri()
        );

        // Instantiate the Etsy service using the credentials, http client and storage mechanism for the token
        //** @var $etsyService Etsy */
        //
        $serviceFactory = new \OAuth\ServiceFactory();
        return $serviceFactory->createService(self::SERVICE
            , $credentials
            , $storage
            , \Config::get('pinterest.permission')
            , $base_uri);
        //, strlen($base_uri) > 0 ?  new \OAuth\Common\Http\Uri($base_uri) : null);
    }
    /**
     * Send get request to connection
     *
     * @return result array
     * @param \OAuth\OAuth2\Service\Pinterest $conn
     * @param string $uri
     * @param array $param_arr
     */
    public static function RequestGet(\OAuth\OAuth2\Service\Pinterest $conn, string $uri, array $param_arr = null)
    {
        return json_decode($conn->request($uri . "?" . \App\Http\CcHelpers\CcArray::ToQueryStr($param_arr)), true);
    }

    /**
     * Send post request to connection
     *
     * @return result array
     * @param \OAuth\OAuth2\Service\Pinterest $conn
     * @param string $uri
     * @param array $param_arr
     */
    public static function RequestPost(\OAuth\OAuth2\Service\Pinterest $conn, string $uri, array $param_arr = null)
    {
        return json_decode($conn->request($uri, 'POST', $param_arr), true);
    }

    /**
     * Send put request to connection
     *
     * @return result array
     * @param \OAuth\OAuth2\Service\Pinterest $conn
     * @param string $uri
     * @param array $param_arr
     */
    public static function RequestPut(\OAuth\OAuth2\Service\Pinterest $conn, string $uri, array $param_arr = null)
    {
        return json_decode($conn->request($uri, 'PUT',  $param_arr), true);
    }
}
