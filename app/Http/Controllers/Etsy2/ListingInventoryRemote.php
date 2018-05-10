<?php


namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Controller;

class ListingInventoryRemote extends Controller
{
    /**
     * get inventory
     *
     * @return array inventory result from etsy
     * @param  \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param  double $listing_id
     */
    public static function getInventory(
        \OAuth\OAuth1\Service\Etsy &$etsyService,
        double $listing_id) {
        
        return ConnectionController::RequestGet($etsyService, 
            '/listings/' . $listing_id . '/inventory', ['write_missing_inventory'=>true]);
    }
    /**
     * update inventory
     *
     * @return array assoc array with key for template name
     * @param \OAuth\OAuth1\Service\Etsy &$etsyService
     * @param double $listing_id
     * @param array $inventory inventory assoc array for updating
     */
    public static function updateInventory(
        \OAuth\OAuth1\Service\Etsy &$etsyService,
        double $listing_id,
        array $inventory) 
        {
            $param = array();
            if ($inventory !== null) {
                $param['products'] = json_encode($inventory['products']);
            }

            if ($inventory['price_on_property'] !== null) {
                $param['price_on_property'] = is_array($inventory['price_on_property']) ? join(",",$inventory['price_on_property'] ): $inventory['price_on_property'];
            }
            if ($inventory['quantity_on_property'] !== null) {
                $param['quantity_on_property'] = is_array($inventory['quantity_on_property']) ? join(",",$inventory['quantity_on_property'] ): $inventory['quantity_on_property'];
            }
            if ($inventory['sku_on_property'] !== null) {
                $param['sku_on_property'] = is_array($inventory['sku_on_property']) ? join(",",$inventory['sku_on_property'] ): $inventory['sku_on_property'];
            }
        
        return ConnectionController::RequestPut($etsyService, '/listings/' . $listing_id . '/inventory', $param);

    }
}
