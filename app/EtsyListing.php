<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\CcHelpers\CcArray;

//use App\CcHelpers\CcHelper;

class EtsyListing extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //ids
        'id', 'etsy_store_id', 'taxonomy_id', 'etsy_section_id', 'shipping_template_id',

        //other important info
        'state', 'title', 'description', 'tags',

        //internal
        'sku', 'internal_tags', 'template',

        //processing info
        'price', 'quantity', 'processing_min', 'processing_max',

        //array
        'materials', 'style',

        //listing info
        'url', 'views', 'num_favorers',

        //enum
        'who_made', 'when_made', 'recipient', 'occasion',

        //bool
        'is_supply', 'is_customizable', 'is_digital', 'has_variations',
        'non_taxable', 'can_write_inventory', 'should_auto_renew',

        //extras
        'inventory',
        "image_url",
        'image_ids', //image id, comma separated
    ];
    /**
     * Columns that will be filled from etsy array
     * @var array
     */
    public static $COLUMN_FILLABLE = [
        //ids
        'id', 'etsy_store_id', 'taxonomy_id',//'etsy_category_id', 
        'etsy_section_id', 'shipping_template_id',

        //other important info
        'state', 'title', 'description', 'tags',

        //internal
        'sku', 'internal_tags', 'template',

        //processing info
        'price', 'quantity', 'processing_min', 'processing_max',

        //array
        'materials', 'style',

        //listing info
        'url', 'views', 'num_favorers',

        //enum
        'who_made', 'when_made', 'recipient', 'occasion',

        //bool
        'is_supply', 'is_customizable', 'is_digital', 'has_variations',
        'non_taxable', 'can_write_inventory', 'should_auto_renew',

        //extras
        'inventory',
        "image_url",
        'image_ids', //image id, comma separated
        'shop_section_id', 'user_id', 'taxonomy_id','listing_id',
    ];
    protected $appends = ['template_description'];

    /**
     * String sizes
     * @var array
     */
    public static $SIZING = ['tags' => 20, 'tag_array' => 13,
        'title' => 140, 'title_array' => 10,
        'template' => 140,
        'image_id' => 50, 'image_id_array' => 10,
        'sku' => 100,
        'internal_tags' => 1000,
        'description' => 10000,
        'materials' => 255, 'style' => 255,
        'section' => 24, 'url' => 255,
    ];
    /**
     * Columns replaced by template
     */
    public static $COLUMN_TEMPLATE = [

        'shipping_template_id',
        'taxonomy_id',

        'processing_min', 'processing_max',

        //array
        'materials', 'style',

        //bool
        'is_supply', 'is_customizable', 'is_digital', 'has_variations',
        'non_taxable', 'can_write_inventory', 'should_auto_renew',

        //extras
        'inventory','image_ids',
    ];

    public static $template_prefix = 'template ';
    protected $keyType = 'biginteger';
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_supply' => 'boolean',
        'is_private' => 'boolean',
        'non_taxable' => 'boolean',
        'is_customizable' => 'boolean',
        'is_digital' => 'boolean',
        'can_write_inventory' => 'boolean',
        'has_variations' => 'boolean',
        'should_auto_renew' => 'boolean',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
    ];

    /**
     * Enum for state
     *
     * @var array
     */
    public static $enum_state = [
        "draft", "active", "inactive", "expired",
    ];
    /**
     * Enum for who made the item
     *
     * @var array
     */
    public static $enum_who_made = [
        "i_did", "collective", "someone_else",
    ];
    /**
     * Enum for when item is made
     *
     * @var array
     */
    public static $enum_when_made = [
        "made_to_order", "2010_2018", "2000_2009", "1999_1999", "before_1999", "1990_1998", "1980s", "1970s", "1960s", "1950s", "1940s", "1930s", "1920s", "1910s", "1900s", "1800s", "1700s", "before_1700",
    ];

    /**
     * Enum for recipient
     *
     * @var array
     */
    public static $enum_recipient = [
        "", "not_specified", "men", "women", "unisex_adults", "teen_boys", "teen_girls", "teens", "boys", "girls", "children", "baby_boys", "baby_girls", "babies", "birds", "cats", "dogs", "pets",
    ];
    
    /**
     * Enum for occasion
     *
     * @var array
     */
    public static $enum_occasion = [
        "", "anniversary", "baptism", "bar_or_bat_mitzvah", "birthday", "canada_day",
        "chinese_new_year", "cinco_de_mayo", "confirmation", "christmas", "day_of_the_dead",
        "easter", "eid", "engagement", "fathers_day", "get_well",
        "graduation", "halloween", "hanukkah", "housewarming", "kwanzaa", "prom",
        "july_4th", "mothers_day", "new_baby", "new_years", "quinceanera",
        "retirement", "st_patricks_day", 'sweet_16', 'sympathy', 'thanksgiving', 'valentines', 'wedding',
    ];
    ///////////////////////////////////////////////////////////////////
    //custom fill
    ///////////////////////////////////////////////////////////////////

    /**
     * Custom fill all that will trigger set attribute
     * 
     * @param array $template_data
     */
    public function fillAll(array $template_data)
    {
        foreach ($template_data as $k => $v) {
            $this->$k = $v;
        }
    }
    /**
     * fill data from template
     *
     * @var array $template_data_etsy listing assoc array data in etsy format
     */
    public function fillTemplate(array $template_data_etsy)
    {
        // print_r($template_data_etsy);exit;
        //append image
        if (isset($template_data_etsy['Images'])) {
            $template_image = [];
            foreach ($template_data_etsy['Images'] as $img) {
                $template_image[] = $img['listing_image_id'];
            }

            $this_image = explode(",", $this->image_ids);

            //remove template image form this image
            $listing_image = array_diff($this_image, $template_image);
            $this->image_ids = array_merge($listing_image, $template_image);
            //do not change image_url as it is not related to template
            unset($template_data_etsy['Images']);
        }
        
        //append image - image id
        if (isset($template_data_etsy['image_ids'])) {
            $template_image = explode(",",$template_data_etsy['image_ids']);

            $this_image = explode(",", $this->image_ids);

            //remove template image form this image
            $listing_image = array_diff($this_image, $template_image);
            $this->image_ids = array_merge($listing_image, $template_image);
            //do not change image_url as it is not related to template
            unset($template_data_etsy['image_ids']);
        }


        //filter with whitelist
        $result = ccArray::WhitelistKey($template_data_etsy, self::$COLUMN_TEMPLATE);

        //assign parameters
        $this->fillAll($result);
    }
    ///////////////////////////////////////////////////////////////////
    //mutator - called with direct access and set attribute but not fill function
    ///////////////////////////////////////////////////////////////////
    private static function CastId($value)
    {
        if (is_numeric($value)) {
            return (double) $value;
        }
        return 0;
    }
    public function setIdAttribute($value)
    {$this->attributes['id'] = self::CastId($value);}
    public function setEtsyStoreIdAttribute($value)
    {$this->attributes['etsy_store_id'] = self::CastId($value);}
    public function setEtsyCategoryIdAttribute($value)
    {$this->attributes['etsy_category_id'] = self::CastId($value);}
    public function setEtsySectionIdAttribute($value)
    {$this->attributes['etsy_section_id'] = self::CastId($value);}
    public function setShippingTemplateIdAttribute($value)
    {$this->attributes['shipping_template_id'] = self::CastId($value);}
    public function setTaxonomyIdAttribute($value)
    {$this->attributes['taxonomy_id'] = self::CastId($value);}
    
    //duplicate function used for etsy listing
    public function setListingIdAttribute($value)
    {$this->attributes['id'] = self::CastId($value);}
    public function setUserIdAttribute($value)
    {$this->attributes['etsy_store_id'] = self::CastId($value);}
    public function setCategoryIdAttribute($value)
    {$this->attributes['etsy_category_id'] = self::CastId($value);}
    public function setShopSectionIdAttribute($value)
    {$this->attributes['etsy_section_id'] = self::CastId($value);}

    // public function setCategoryPathIdsAttribute($value)
    // {$this->attributes['category_path_ids'] = self::CastId($value);}

    //enum///////////////////
    public function setStateAttribute($value)
    {$this->attributes['state'] = in_array(strtolower($value), self::$enum_state) ? $value : self::$enum_state[0];}
    public function setWhoMadeAttribute($value)
    {$this->attributes['who_made'] = in_array(strtolower($value), self::$enum_who_made) ? $value : self::$enum_who_made[0];}
    public function setWhenMadeAttribute($value)
    {$this->attributes['when_made'] = in_array(strtolower($value), self::$enum_when_made) ? $value : self::$enum_when_made[0];}
    public function setRecipientAttribute($value)
    {$this->attributes['recipient'] = in_array(strtolower($value), self::$enum_recipient) ? $value : self::$enum_recipient[0];}
    public function setOccasionAttribute($value)
    {$this->attributes['occasion'] = in_array(strtolower($value), self::$enum_occasion) ? $value : self::$enum_occasion[0];}

    //bool///////////////////
    private static function CastBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            return $value == "true" || $value == "1";
        }
        return (bool) ($value);
    }

    public function setIsSupplyAttribute($value)
    {$this->attributes['is_supply'] = self::CastBool($value);}
    public function setIsCustomizableAttribute($value)
    {$this->attributes['is_customizable'] = self::CastBool($value);}
    public function setIsDigitalAttribute($value)
    {$this->attributes['is_digital'] = self::CastBool($value);}
    public function setHasVariationsAttribute($value)
    {$this->attributes['has_variations'] = self::CastBool($value);}
    public function setNonTaxableAttribute($value)
    {$this->attributes['non_taxable'] = self::CastBool($value);}
    public function setCanWriteInventoryAttribute($value)
    {$this->attributes['can_write_inventory'] = self::CastBool($value);}
    public function setShouldAutoRenewAttribute($value)
    {$this->attributes['should_auto_renew'] = self::CastBool($value);}

    //int///////////////////
    private static function CastNumber($value)
    {
        if (is_numeric($value)) {
            return (double) $value;
        }
        return 0;
    }

    public function setPriceAttribute($value)
    {$this->attributes['price'] = self::CastNumber($value);}
    public function setQuantityAttribute($value)
    {$this->attributes['quantity'] = (int) self::CastNumber($value);}
    public function setProcessingMinAttribute($value)
    {$this->attributes['processing_min'] = (int) self::CastNumber($value);}
    public function setProcessingMaxAttribute($value)
    {$this->attributes['processing_max'] = (int) self::CastNumber($value);}
    public function setViewsAttribute($value)
    {$this->attributes['views'] = (int) self::CastNumber($value);}
    public function setNumFavorersAttribute($value)
    {$this->attributes['num_favorers'] = (int) self::CastNumber($value);}

    //string///////////////////
    private static function CastString($value, $max_size = 0)
    {
        $tmp = (string) $value; //convert value to string
        //trim string size
        if ($max_size > 0) {$tmp = substr($tmp, 0, $max_size);}
        //make sure string do not have special character
        return $tmp; // htmlentities($tmp, ENT_QUOTES);
    }

    public function setTitleAttribute($value)
    {$this->attributes['title'] = self::CastString($value, self::$SIZING['title']);}
    public function setSkuAttribute($value)
    {$this->attributes['sku'] = self::CastStringArray($value, self::$SIZING['sku']);}
    public function setInternalTagsAttribute($value)
    {$this->attributes['internal_tags'] = self::CastString($value, self::$SIZING['internal_tags']);}
    public function setTemplateAttribute($value)
    {$this->attributes['template'] = self::CastString($value, self::$SIZING['template']);}
    public function setUrlAttribute($value)
    {$this->attributes['url'] = self::CastString($value, self::$SIZING['url']);}
    public function setTemplateDescriptionAttribute($value)
    {$this->attributes['template_description'] = self::CastString($value, self::$SIZING['template']);}
    public function setDescriptionAttribute($value)
    {
        $loc = strpos($value,\Config::get('etsy.spacer'));

        //spacer not exist
        if($loc===false){//local description only
            $this->attributes['description'] = self::CastString($value, self::$SIZING['description']);
            return;
        }
        
        //if spacer is at the start of the string, it does not explode properly
        if($loc==0){
            $d = explode(\Config::get('etsy.spacer'), " ".$value);
        }else{
            $d = explode(\Config::get('etsy.spacer'), $value);
        }
        
        //the first one is always description. If prefix string with space, it will be removed here
        $this->attributes['description'] = trim(self::CastString($d[0], self::$SIZING['description']));

        //the last one is always treated as code
        $code = end($d);
        if (strpos("|",$code)===false) 
        {//no code in string
            return;
            
        }
        
        $info = explode("|", $code);

        if (isset($info[0])) {
            $this->sku = $info[0];
        }
        if (isset($info[1])) {
            $this->template = $info[1];
        }
        if (isset($info[2])) {
            $this->internal_tags = $info[2];
        }

    }
    //string///////////////////
    private static function CastStringArray($value, $max_size = 0, $max_array_size = 0)
    {
        //turn to array first
        if (is_array($value)) {
            $tmp = array_values($value);
        } else {
            $tmp = explode(",", $value); //convert value to string
        }

        //trim number of item in array if required
        if ($max_array_size > 0) {
            $tmp = array_slice($tmp, 0, $max_array_size);
        }

        //trim string size
        if ($max_size > 0) {
            for ($i = 0; $i < sizeof($tmp); $i++) {
                $tmp[$i] = trim(substr($tmp[$i], 0, $max_size));
            }
        }

        //make sure string do not have special character
        return join(",", $tmp);
    }

    public function setMaterialsAttribute($value)
    {$this->attributes['materials'] = self::CastStringArray($value, self::$SIZING['materials']);}
    public function setTagsAttribute($value)
    {$this->attributes['tags'] = self::CastStringArray($value, self::$SIZING['tags'], self::$SIZING['tag_array']);}

    public function setImageIdsAttribute($value)
    {
        $tmp = [];
        //if image array, extract image id
        if (is_array($value)) {
            //Images array directly from etsy
            if (isset($value[0]['listing_image_id'])) {
                foreach ($value as $img) {
                    $tmp[] = $img['listing_image_id'];
                }
            } else {
                //if image id array, join to string
                foreach ($value as $v) {
                    if (strlen($v) == 0) {continue;}
                    $tmp[] = $v;
                }
            }
        } else {
            //if string
            $tmp = $value;
        }

        //if string, explode to check and join again
        $this->attributes['image_ids'] = self::CastStringArray($tmp,
            self::$SIZING['image_id'], self::$SIZING['image_id_array']);
    }
    //other///////////////////
    public function setInventoryAttribute($value)
    {
        $this->attributes['inventory'] = $value;
    }
    public function setImagesAttribute($value)
    {
        $this->image_url = $value[0]['url_fullxfull'];
        $this->image_ids = $value;
    }
    public function setImageUrlAttribute($value)
    {
        $this->attributes['image_url'] = self::CastString($value, self::$SIZING['url']);
    }
    ///////////////////////////////////////////////////////////////////
    //accessor
    ///////////////////////////////////////////////////////////////////
    public function getFullDescriptionAttribute()
    {
        return $this->description .
        "\n" . \Config::get('etsy.spacer') ."\n" . 
        $this->template_description .
        "\n" . \Config::get('etsy.spacer') ."\n" . 
        $this->sku . "|" . $this->template . "|" . $this->internal_tags;
    }

    public function getTemplateDescriptionAttribute($value)
    {return $value;}

    /**
     * checks if data exist and assign data accordingly
     *
     * @var $arr json array
     * @var $key key string
     * @var $default default value
     */
    private static function set_value($arr, $key, $default)
    {
        if (!isset($arr[$key])) {return $default;}
        return $arr[$key] === null ? $default : $arr[$key];
    }

    /**
     * search for tempate title/name based on name given in csv.
     * Only use listing title in the same shop
     *
     * @param $name name of template
     * @return array
     */
    public static function getTemplateTitle($name, \App\EtsyStore $store)
    {
        //search for template name and use template name if exist
        //template only search for listing title in the same shop
        $tmp_template = $store->Listings->where('title', "template " . $name)->first();
        if ($tmp_template !== null) {
            return $tmp_template->title;
        }
        return "";
    }

    /**
     * validate individual tag
     *
     * @param $tag individual tag string
     * @return bool true if valid, false otherwise
     */
    private static function validateTag($tag)
    {
        if (strlen($tag > self::$SIZING['tag'])) {
            return false;
        }

        return true;
    }

    /**
     * check csv data
     *
     * @param $csv_data csv data to be checked
     */
    public function checkCsv($csv_data)
    {
        //basic check checks
        if (!is_numeric($csv_data['listing_id'])) {return array_search('listing_id', self::$CSV_COLUMNS) + 1;}
        if (in_array($csv_data['state'], self::$enum_state) == false) {return array_search('state', self::$CSV_COLUMNS) + 1;}

        if (!is_numeric($csv_data['price'])) {return array_search('price', self::$CSV_COLUMNS) + 1;}
        if (!is_numeric($csv_data['quantity'])) {return array_search('quantity', self::$CSV_COLUMNS) + 1;}
        if (strlen($csv_data['sku']) > 100) {return array_search('sku', self::$CSV_COLUMNS) + 1;}

        //check tags, length and others
        for ($i = 1; $i <= self::$SIZING['tag_array']; $i++) {
            if (!isset($csv_data['tag' . $i])) {break;}
            if (!self::validateTag($csv_data['tag' . $i])) {return array_search('tag' . $i, self::$CSV_COLUMNS) + 1;}
        }

        //check template to see if it existed
        if (strlen($this->getTemplateTitle($csv_data['template'])) == 0) {return array_search('template', self::$CSV_COLUMNS) + 1;}

        return 0;
    }

    /**
     * format for use with communication with etsy
     *
     * @return array for use with commuication with etsy
     */
    public function toEtsy()
    {
        $rtn = [];
        //get template
        $template = self::where('title', "template " . $name)->first();
        if ($template == null) {
            //no template found, use currently loaded listing as data
            $this->template = "";
            $rtn = $this->toArray();
        } else {
            //template found
            //use template data
            $rtn = $template->toArray();

            //overwrite certain field
            $rtn['id'] = $this->id;
            $rtn['title'] = $this->title;
            $rtn['quantity'] = $this->quantity;
            $rtn['price'] = $this->price;
            if (in_array($this->state, self::$enum_state)) {$rtn['state'] = $this->state;}
            $rtn['etsy_section_id'] = $this->etsy_section_id;
            $rtn['tags'] = $this->tags;

            $rtn['description'] = self::CombineDescription($this->description, $rtn['description'],
                $this->ss_sku, $this->template, $this->internal_tags);
        }

        ////////////string need escape quotes
        $rtn['title'] = substr(html_entity_decode($rtn['title'], ENT_QUOTES), 0, self::$SIZING['title']);
        $rtn['description'] = html_entity_decode($rtn['description'], ENT_QUOTES);
        $rtn['tags'] = html_entity_decode($rtn['tags'], ENT_QUOTES);

        //rename column
        $rtn['listing_id'] = $rtn['id'];
        unset($rtn['id']);
        $rtn['user_id'] = $rtn['etsy_store_id'];
        unset($rtn['etsy_store_id']);
        $rtn['category_id'] = $rtn['etsy_category_id'];
        unset($rtn['etsy_category_id']);
        $rtn['shop_section_id'] = $rtn['etsy_section_id'];
        unset($rtn['etsy_section_id']);

        return $rtn;
    }
    /**
     * depreciated
     */
    public static function CombineDescription($product_description,
        $template_description,
        $sku, $template_name, $internal_tags) {

        return $product_description .
        \Config::get('etsy.spacer') .
        $template_description .
        \Config::get('etsy.spacer') .
            $sku . "|" . $template_name . "|" . $internal_tags;
    }
    /**
     * link store listing to store
     */
    public function Store()
    {
        return $this->belongsTo('App\EtsyStore', 'etsy_store_id');
    }

    /**
     * link listing to section
     */
    public function Section()
    {
        return $this->belongsTo('App\EtsySection', 'etsy_section_id');
    }

    /**
     * link listing to image
     */
    public function Images()
    {
        return $this->belongsToMany('App\EtsyListingImage',
            'etsylisting_etsylistingimage', 'listing_id', 'listing_image_id')
            ->withTimestamps();
        //->where('updated_at', ">", Carbon::now()->addHours(-config('etsy.expiry_hr')));
    }
}
