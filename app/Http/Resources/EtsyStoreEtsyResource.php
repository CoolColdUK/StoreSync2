<?php

namespace App\Http\Resources;

use App\EtsyStore;
use App\Http\CcHelpers\CcArray;
use Illuminate\Http\Resources\Json\JsonResource;

class EtsyStoreEtsyResource extends JsonResource
{
    public static $COLUMN = [
        'user_id',
        'login_name',
        'primary_email',
        'feedback_info',
        'use_new_inventory_endpoints',
    ];
    /**
     * Not used
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $request
    }

    public static function fromEtsy(array $store)
    {
        $tmp = CcArray::WhitelistKey($store, self::$COLUMN);

        $rtn = new EtsyStore();
        $rtn->fillAll($tmp);
        return $rtn;
    }
    public static function toEtsy(\App\EtsyStore $store)
    {
        $rtn = $store->toArray();
        $rtn = CcArray::WhitelistKey($rtn, self::$COLUMN);
        
        return $rtn;
    }
    
}
