<?php

namespace App\Http\Resources;

use App\EtsyListing;
use App\Http\CcHelpers\CcArray;
use Illuminate\Http\Resources\Json\JsonResource;

class EtsyListingEtsyResource extends JsonResource
{
    public static $COLUMN = [
        'listing_id',
        'user_id',
        //'category_id',
        'taxonomy_id',
        'shop_section_id',
        'shipping_template_id',
        'state',
        'title',
        'description',
        'tags',
        'sku',
        'price',
        'quantity',
        'processing_min',
        'processing_max',
        'materials',
        'style',
        'url',
        'views',
        'num_favorers',
        'who_made',
        'when_made',
        'recipient',
        'occasion',
        'is_supply',
        'is_customizable',
        'is_digital',
        'has_variations',
        'non_taxable',
        'can_write_inventory',
        'should_auto_renew',
        'Images',
        'inventory',
    ];
    public static $COLUMN_SET = [
        'listing_id', 
        'renew', 
        'title', 'description',
            'materials', 'tags', 'style',
            'price','quantity',
            'processing_min', 'processing_max',
            'shipping_template_id', 'shop_section_id', 'taxonomy_id',//'category_id',
            'state', 'who_made', 'when_made', 'recipient', 'occasion',
            'image_ids',
            'is_customizable', 'non_taxable', 'is_supply',
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

    /**
     * transform data from etsy to etsylisting model
     *
     * @return \App\EtsyListing
     * @param  array $listing
     */
    public static function fromEtsy(array $listing)
    {
        $tmp = CcArray::WhitelistKey($listing, self::$COLUMN);

        //decode
        if (isset($tmp['title'])) {$tmp['title'] = html_entity_decode($tmp['title'], ENT_QUOTES);}
        if (isset($tmp['description'])) {$tmp['description'] = html_entity_decode($tmp['description'], ENT_QUOTES);}
        if (isset($tmp['tags'])) {$tmp['tags'] = html_entity_decode(join(",", $tmp['tags']), ENT_QUOTES);}
        if (isset($tmp['materials'])) {$tmp['materials'] = html_entity_decode(join(",", $tmp['materials']), ENT_QUOTES);}
        if (isset($tmp['style'])) {$tmp['style'] = html_entity_decode(join(",", $tmp['style']), ENT_QUOTES);}

        $rtn = new EtsyListing();
        $rtn->fillAll($tmp);
        return $rtn;
    }
    /**
     * transform data to etsy from etsylisting model
     *
     * @return array
     * @param  \App\EtsyListing $listing
     */
    public static function toEtsy(\App\EtsyListing $listing)
    {
        $rtn = $listing->toArray();
        $rtn = CcArray::WhitelistKey($rtn, self::$COLUMN_SET);
        $rtn['listing_id']=$listing->id;
        $rtn['description']=$listing->full_description;

        $rtn['shop_section_id']=$listing->etsy_section_id;
        $rtn['category_id']=$listing->etsy_category_id;


        $rtn['is_supply']=$listing->is_supply?1:0;
        $rtn['is_customizable']=$listing->is_customizable?1:0;
        $rtn['is_digital']=$listing->is_digital?1:0;
        $rtn['has_variations']=$listing->has_variations?1:0;
        $rtn['non_taxable']=$listing->non_taxable?1:0;
        $rtn['can_write_inventory']=$listing->can_write_inventory?1:0;
        $rtn['should_auto_renew']=$listing->should_auto_renew?1:0;
        
        return $rtn;

    }
    
}
