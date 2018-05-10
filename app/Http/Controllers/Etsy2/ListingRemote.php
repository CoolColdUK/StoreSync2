<?php

namespace App\Http\Controllers\Etsy2;

use App\EtsyListing;
use App\Http\CcHelpers\CcArray;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Etsy2\ConnectionController;
use App\Http\Resources\EtsyListingEtsyResource;

/**
 * Etsy listing remote access
 *
 */
class ListingRemote extends Controller
{

    /**
     * White list for key valid in create listing
     */

    public static $wl_create_listing = [
        'title', 'description',
        'materials', 'tags', 'style',
        'price', 'quantity',
        'processing_min', 'processing_max',
        'shipping_template_id', 'shop_section_id', 'taxonomy_id',
        'state', 'who_made', 'when_made', 'recipient', 'occasion',
        'image_ids',
        'is_customizable', 'non_taxable', 'is_supply',
    ];

    /**
     * White list for key valid in update listing
     */
    public static $wl_update_listing = [
        'listing_id', 'title', 'description',
        'materials', 'tags', 'style',
        'renew', 'processing_min', 'processing_max',
        'shipping_template_id', 'shop_section_id', 'taxonomy_id',
        'state', 'who_made', 'when_made', 'recipient', 'occasion',
        'image_ids',
        'is_customizable', 'non_taxable', 'is_supply',
    ];
    /**
     * create listing on etsy
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  array $listing
     */
    public static function createListing(\OAuth\OAuth1\Service\Etsy &$etsyService, array $listing)
    {
        //settings for new listing only
        $listing['who_made'] = EtsyListing::$enum_who_made[0]; //needed or fail without manufacturing partner
        $listing['state'] = EtsyListing::$enum_state[0]; //draft state by default

        $listing = ccArray::WhitelistKey($listing, self::$wl_create_listing);
        $listing = ccArray::RemoveNull($listing);

        //convert model between model and etsy
        return ConnectionController::RequestPost($etsyService, '/listings/', $listing);
    }

    /**
     * update listing
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  \App\EtsyListing $listing
     */
    public static function updateListing(\OAuth\OAuth1\Service\Etsy &$etsyService, array $listing)
    {
        //update requires empty certain parameters
        $listing = ccArray::WhitelistKey($listing, self::$wl_update_listing);
        $listing = ccArray::RemoveNull($listing);

        //format to associate array for etsy
        return ConnectionController::RequestPut($etsyService, '/listings/' . $listing['listing_id'], $listing);
    }

    /**
     * get listing
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  double $listing_id
     * @param  array $extra extra parameter
     */
    public static function getListing(\OAuth\OAuth1\Service\Etsy &$etsyService, double $listing_id, array $extra)
    {
        return ConnectionController::RequestGet($etsyService, '/listings/' . $listing_id, $extra);
    }

////////////////////////////////////////////////////////////////////////////////////////
    //all listing find
    ////////////////////////////////////////////////////////////////////////////////////////
    /**
     * find all template listing
     *
     * @return array assoc array with key in template name
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     */
    public static function toolFindAllTemplate(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name)
    {

        //get all draft listing first
        $tmp = self::findAllShopListingsDraft($etsyService,
            $shop_id_or_name,
            ['includes' => ['Images']]
        );

        //identify template via title - always start with template
        $rtn = [];
        foreach ($tmp['results'] as $r) {
            if (substr($r['title'], 0, strlen(EtsyListing::$template_prefix))
                != EtsyListing::$template_prefix) {
                continue;
            }
            $rtn[substr($r['title'], strlen(EtsyListing::$template_prefix))] = EtsyListingEtsyResource::fromEtsy($r)->toArray();

            //get inventory for template - cannot get as extra parameter
            $rtn[substr($r['title'], strlen(EtsyListing::$template_prefix))]['inventory']
            = ListingInventoryRemote::getInventory($etsyService, $r['listing_id'])['results'];
        }
        return $rtn;
    }
    /**
     * upload listing, it will decide whether it needs update or create listing
     *
     * @return array listing result from create or updated listing
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  array $listing
     */
    public static function toolUploadListing(\OAuth\OAuth1\Service\Etsy &$etsyService, array $listing)
    {
        if ($listing['listing_id'] > 0) {
            return self::updateListing($etsyService, $listing);
        }
        return self::createListing($etsyService, $listing);
    }
////////////////////////////////////////////////////////////////////////////////////////
    //all listing find
    ////////////////////////////////////////////////////////////////////////////////////////
    /**
     * find all listing
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $url
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    private static function findAllListings(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $url, string $keyword = null, array $tags = null, array $extra = null) {
        $rtn = [];
        $page = 1;

        while (!isset($rtn['count']) || sizeof($rtn['results']) < $rtn['count']) {
            set_time_limit(60);
            $tmp = self::findListings($etsyService,
                $url, 100, 0, $page, $keyword, $tags, $extra);
            if (isset($rtn['results'])) {
                $rtn['results'] = array_merge($rtn['results'], $tmp['results']);
            } else {
                $rtn = $tmp;
            }
            $page++;
        }
        return $rtn;
    }

    /**
     * find all active listing
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    public static function findAllListingsActive(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $keyword = null, array $tags = null, array $extra = null) {
        return self::findAllListings($etsyService,
            '/listings/active', $keyword, $tags, $extra);
    }
    /**
     * find all active listing in this shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    public static function findAllShopListingsActive(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, string $keyword = null, array $tags = null, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/active', $keyword, $tags, $extra);
    }

    /**
     * find all draft listing in this shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  array $extra
     */
    public static function findAllShopListingsDraft(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/draft', null, null, $extra);
    }

    /**
     * find all inactive listing in this shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    public static function findAllShopListingsInactive(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/inactive', null, null, $extra);
    }

    /**
     * find all expired listing in this shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  array $extra
     */
    public static function findAllShopListingsExpired(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/expired', null, null, $extra);
    }

    /**
     * find all featured listing in this shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  array $extra
     */
    public static function findAllShopListingsFeatured(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/featured', null, null, $extra);
    }

    /**
     * find all listing in this shop section
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $shop_section_id
     * @param  array $extra
     */
    public static function findAllShopSectionListings(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, int $shop_section_id, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/sections/' . $shop_section_id . '/listings', null, null, $extra);
    }
    /**
     * find all active listing in this shop section
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $shop_section_id
     * @param  array $extra
     */
    public static function findAllShopSectionListingsActive(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $shop_id_or_name, int $shop_section_id, array $extra = null) {
        return self::findAllListings($etsyService,
            '/shops/' . $shop_id_or_name . '/sections/' . $shop_section_id . '/listings/active', null, null, $extra);
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //individual listing find
    ////////////////////////////////////////////////////////////////////////////////////////
    /**
     * find listing
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $url
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    private static function findListings(\OAuth\OAuth1\Service\Etsy &$etsyService,
        string $url, int $limit = 25, int $offset = 0, int $page = 1,
        string $keyword = null, array $tags = null, array $extra = null) {
        $param = [];
        $param['limit'] = $limit;
        if ($param['limit'] < 1) {$param['limit'] = 1;}
        if ($param['limit'] > 100) {$param['limit'] = 100;}

        $param['offset'] = $offset;
        if ($param['offset'] < 0) {$param['offset'] = 0;}

        $param['page'] = $page;
        if ($param['page'] < 1) {$param['page'] = 1;}

        if ($keyword != null) {
            $param['keywords'] = $keyword;
        }

        if ($tags != null) {$param['tags'] = $tags;}
        if ($extra != null) {$param = array_merge($param, $extra);}

        return ConnectionController::RequestGet($etsyService, $url, $param);
    }
    /**
     * find active listing
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    public static function findListingActive(\OAuth\OAuth1\Service\Etsy &$etsyService,
        int $limit = 25, int $offset = 0, int $page = 1, string $keyword = null, array $tags = null, array $extra = null) {

        return self::findListings($etsyService,
            '/listings/active',
            $limit, $offset, $page, $keyword, $tags, $extra);
    }
    /**
     * find active listing in shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  string $keyword
     * @param  array $tags
     * @param  array $extra
     */
    public static function findShopListingsActive(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $limit = 25, int $offset = 0, int $page = 1, string $keyword = null, array $tags = null, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/active',
            $limit, $offset, $page, $keyword, $tags, $extra);
    }

    /**
     * find draft listing in shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  array $extra
     */
    public static function findShopListingsDraft(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $limit = 25, int $offset = 0, int $page = 1, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/draft',
            $limit, $offset, $page, null, null, $extra);
    }

    /**
     * find expired listing in shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  array $extra
     */
    public static function findShopListingsExpired(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $limit = 25, int $offset = 0, int $page = 1, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/expired',
            $limit, $offset, $page, null, null, $extra);
    }

    /**
     * find featured listing in shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  array $extra
     */
    public static function findShopListingsFeatured(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $limit = 25, int $offset = 0, int $page = 1, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/featured',
            $limit, $offset, $page, null, null, $extra);
    }

    /**
     * find inactive listing in shop
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  array $extra
     */
    public static function findShopListingsInactive(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $limit = 25, int $offset = 0, int $page = 1, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/listings/inactive',
            $limit, $offset, $page, null, null, $extra);
    }

    /**
     * find listing in shop section
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $shop_section_id
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  array $extra
     */
    public static function findShopSectionListings(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $shop_section_id, int $limit = 25, int $offset = 0, int $page = 1, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/sections/' . $shop_section_id . '/listings',
            $limit, $offset, $page, null, null, $extra);
    }

    /**
     * find active listing in shop section
     *
     * @return array json response from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  string $shop_id_or_name
     * @param  int $shop_section_id
     * @param  int $limit
     * @param  int $offset
     * @param  int $page
     * @param  array $extra
     */
    public static function findShopSectionListingsActive(\OAuth\OAuth1\Service\Etsy &$etsyService, string $shop_id_or_name,
        int $shop_section_id, int $limit = 25, int $offset = 0, int $page = 1, array $extra = null) {
        return self::findListings($etsyService,
            '/shops/' . $shop_id_or_name . '/sections/' . $shop_section_id . '/listings/active',
            $limit, $offset, $page, null, null, $extra);
    }
}
