<?php

namespace App\Http\Resources;

use App\EtsyListing;
use App\Http\CcHelpers\CcArray;
use Illuminate\Http\Resources\Json\JsonResource;

class EtsyListingCsvResource extends JsonResource
{
    public static $COLUMNS = [
        'listing_id', 'sku', "title len",
        "title1", "title2", "title3", "title4", "title5",
        "title6", "title7", "title8", "title9", "title10",
        "description", "price", "quantity", "state",

        "section", "internal_tags", "template",
        "tag1", "tag2", "tag3", "tag4", "tag5", "tag6", "tag7",
        "tag8", "tag9", "tag10", "tag11", "tag12", "tag13",
        'who_made', 'when_made', 'recipient', 'occasion',
        'url', 'views', 'num_favorers',

        "image_url",
        "image_id1", "image_id2", "image_id3", "image_id4", "image_id5",
        "image_id6", "image_id7", "image_id8", "image_id9", "image_id10",
        "pinterest acc", "pinterest board",
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
     * convert etsy listing to csv format
     *
     * @return array
     * @param  \App\EtsyListing $listing
     * @param  array $sections used to convert section_id to section name
     */
    public static function toCsv(\App\EtsyListing $listing, array $sections)
    {
        $rtn = array_fill_keys(self::$COLUMNS, "");
        $rtn['listing_id'] = $listing->id;
        $rtn['sku'] = $listing->sku;

        $rtn['description'] = $listing->description;
        $rtn['price'] = $listing->price;
        $rtn['quantity'] = $listing->quantity;
        $rtn['state'] = $listing->state;

        $rtn = array_merge($rtn, CcArray::SplitStrToArray($listing->tags, 'tag'));
        $rtn = array_merge($rtn, CcArray::SplitStrToArray($listing->title, 'title', ",", true));

        if (isset($sections[$listing->etsy_section_id])) {$rtn['section'] = $sections[$listing->etsy_section_id];}
        $rtn['template'] = $listing->template;
        $rtn['internal_tags'] = $listing->internal_tags;

        $rtn['who_made'] = $listing->who_made;
        $rtn['when_made'] = $listing->when_made;
        $rtn['recipient'] = $listing->recipient;
        $rtn['occasion'] = $listing->occasion;

        $rtn['url'] = $listing->url;
        $rtn['views'] = $listing->views;
        $rtn['num_favorers'] = $listing->num_favorers;
        $rtn['image_url'] = $listing->image_url;
        if (strlen($listing->image_ids) > 0) {
            $rtn = array_merge($rtn, CcArray::SplitStrToArray($listing->image_ids, 'image_id', ",", true));
        }

        return $rtn;
    }
    /**
     * convert etsy listing from csv format
     *
     * @return \App\EtsyListing
     * @param  array $listing
     * @param  array $sections used to convert section_id to section name
     */
    public static function fromCsv(array $listing, array $sections)
    {

        //merge field as required////////////////////////////
        //manupliate tags to remove duplicate
        if (isset($listing['tag1'])) {
            $tmp_tag = [];
            for ($i = 1; $i <= EtsyListing::$SIZING['tag_array']; $i++) {
                //if (!isset($request['tag' . $i])) {break;}
                $tmp_tag['tag' . $i] = substr($listing['tag' . $i], 0, EtsyListing::$SIZING['tags']);
                unset($listing['tag' . $i]);
            }
            $listing['tags'] = array_unique($tmp_tag);
        }

        if (isset($listing['title1'])) {
            $listing['title'] = CcArray::JoinArrToStr($listing, 'title', ", ", true);
            for ($i = 1; $i <= EtsyListing::$SIZING['title_array']; $i++) {                
                unset($listing['title' . $i]);
            }
        }

        if (isset($listing['section'])) {
            $loc = array_search($listing['section'], $sections);
            if ($loc !== null) {
                $listing['etsy_section_id'] = $loc;
            } else { 
                $listing['etsy_section_id'] = null;
            }
            unset($listing['section']);
        }

        //combine images
        if (isset($listing['image_id1'])) {
            $tmp_img = [];
            for ($i = 1; $i <= EtsyListing::$SIZING['image_id_array']; $i++) {
                //if (!isset($request['tag' . $i])) {break;}
                $tmp_img['image_id' . $i] = substr($listing['image_id' . $i], 0, EtsyListing::$SIZING['image_id']);
                unset($listing['image_id' . $i]);
            }

            $listing['image_ids'] = $tmp_img;
        }

        //remove unwanted column
        $listing = CcArray::WhitelistKey($listing, EtsyListing::$COLUMN_FILLABLE);
        $rtn = new EtsyListing();
        $rtn->fillAll($listing);
        return $rtn;
    }
}
