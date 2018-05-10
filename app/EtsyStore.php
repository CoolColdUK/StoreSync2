<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EtsyStore extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'primary_email',
        'feedback_info_score', 'feedback_info_count',
        'use_new_inventory_endpoints',
    ];
    protected $keyType = 'biginteger';

    protected $appends = ['user_id','login_name','feedback_info'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
    ];

    /**
     * Size of string
     */
    public static $SIZING = ['name' => 100, 'primary_email' => 100,
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
    public function setUserIdAttribute($value)
    {$this->attributes['id'] = self::CastId($value);}

    //int///////////////////
    private static function CastNumber($value)
    {
        if (is_numeric($value)) {
            return (double) $value;
        }
        return 0;
    }

    public function setFeedbackInfoScoreAttribute($value)
    {$this->attributes['feedback_info_score'] = self::CastNumber($value);}
    public function setFeedbackInfoCountAttribute($value)
    {$this->attributes['feedback_info_count'] = self::CastNumber($value);}

    public function setFeedbackInfoAttribute($value)
    {
        $this->feedback_info_score = $value['score'];
        $this->feedback_info_count = $value['count'];
    }

    //string///////////////////
    private static function CastString($value, $max_size = 0)
    {
        $tmp = (string) $value; //convert value to string
        //trim string size
        if ($max_size > 0) {$tmp = substr($tmp, 0, $max_size);}
        //make sure string do not have special character
        return $tmp; // htmlentities($tmp, ENT_QUOTES);
    }

    public function setNameAttribute($value)
    {$this->attributes['name'] = self::CastString($value, self::$SIZING['name']);}
    public function setLoginNameAttribute($value)
    {$this->name = $value;}
    public function setPrimaryEmailAttribute($value)
    {$this->attributes['primary_email'] = self::CastString($value, self::$SIZING['primary_email']);}

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

    public function setUseNewInventoryEndpointsAttribute($value)
    {$this->attributes['use_new_inventory_endpoints'] = self::CastBool($value);}

    ///////////////////////////////////////////////////////////////////
    //accessor
    ///////////////////////////////////////////////////////////////////
    public function getFeedbackInfoAttribute()
    {
        return ['score'=>$this->feedback_info_score,
        'count'=>$this->feedback_info_count];
    }
    public function getLoginNameAttribute()
    {return $this->name;}
    public function getUserIdAttribute()
    {return $this->id;}

    /**
     * Save json data to db. Update if exist, insert if not
     * 
     * @param array $etsy_usr_json
     */
    public static function UpdateOrCreateFromJson(array $etsy_usr_json)
    {
        //print_r($etsy_usr_json);exit;
        $r = self::updateOrCreate(
            ['id' => $etsy_usr_json['user_id']], [
                'id' => $etsy_usr_json['user_id'],
                'name' => $etsy_usr_json['login_name'],
                'primary_email' => isset($etsy_usr_json['primary_email']) ? $etsy_usr_json['primary_email'] : "",
                'creation_tsz' => $etsy_usr_json['creation_tsz'],
                'user_pub_key_key' => $etsy_usr_json['user_pub_key']['key'],
                'user_pub_key_id' => $etsy_usr_json['user_pub_key']['key_id'],
                'referred_by_user_id' => isset($etsy_usr_json['referred_by_user_id']) ? $etsy_usr_json['referred_by_user_id'] : 0,
                'feedback_info_count' => $etsy_usr_json['feedback_info']['count'],
                'feedback_info_score' => $etsy_usr_json['feedback_info']['score'],
                'awaiting_feedback_count' => $etsy_usr_json['awaiting_feedback_count'],
                'use_new_inventory_endpoints' => $etsy_usr_json['use_new_inventory_endpoints'],
            ]);

        return $r;
    }

    /**
     * Link to user with given information
     */
    public function AttachWithDetail($user_id, $api_key, $api_secret
        , $token, $token_secret, $permission) {
        //print_r($etsy_usr_json);exit;
        $this->Owner()->detach($user_id);

        //////////////////////////save link///////////////////////////////
        return $this->Owner()->attach($user_id, [
            'api_key' => $api_key,
            'api_secret' => $api_secret,
            'oauth_key' => $token,
            'oauth_secret' => $token_secret,
            'perm_email_r' => in_array("email_r", $permission),
            'perm_listings_r' => in_array("listings_r", $permission),
            'perm_listings_w' => in_array("listings_w", $permission),
            'perm_listings_d' => in_array("listings_d", $permission),
            'perm_transactions_r' => in_array("transactions_r", $permission),
            'perm_transactions_w' => in_array("transactions_w", $permission),
            'perm_billing_r' => in_array("billing_r", $permission),
            'perm_profile_r' => in_array("profile_r", $permission),
            'perm_profile_w' => in_array("profile_w", $permission),
            'perm_address_r' => in_array("address_r", $permission),
            'perm_address_w' => in_array("address_w", $permission),
            'perm_favorites_rw' => in_array("favorites_rw", $permission),
            'perm_shops_rw' => in_array("shops_rw", $permission),
            'perm_cart_rw' => in_array("cart_rw", $permission),
            'perm_recommend_rw' => in_array("recommend_rw", $permission),
            'perm_feedback_r' => in_array("feedback_r", $permission),
            'perm_treasury_r' => in_array("treasury_r", $permission),
            'perm_treasury_w' => in_array("treasury_w", $permission),
        ]);
    }

    /**
     * link store to store section with expiry
     */
    public function Sections()
    {
        return $this->hasMany('App\EtsySection', 'etsy_store_id')
            ->where('updated_at', ">", Carbon::now()->addHours(-config('etsy.expiry_hr')));
    }
    /**
     * link store to store section
     */
    public function SectionsAll()
    {
        return $this->hasMany('App\EtsySection', 'etsy_store_id');
    }

    /**
     * link store to store listing with expiry
     */
    public function Listings()
    {
        return $this->hasMany('App\EtsyListing', 'etsy_store_id')
            ->where('updated_at', ">", Carbon::now()->addHours(-config('etsy.expiry_hr')));
    }
    /**
     * link store to owner
     */
    public function Owner()
    {
        return $this->belongsToMany('App\User', 'user_etsy')
            ->withPivot('api_key'
                , 'api_secret'
                , 'oauth_key'
                , 'oauth_secret'
                , 'perm_email'
                , 'perm_listings_r'
                , 'perm_listings_w'
                , 'perm_listings_d'
                , 'perm_transactions_r'
                , 'perm_transactions_w'
                , 'perm_billing_r'
                , 'perm_profile_r'
                , 'perm_profile_w'
                , 'perm_address_r'
                , 'perm_address_w'
                , 'perm_favorites_rw'
                , 'perm_shops_rw'
                , 'perm_cart_rw'
                , 'perm_recommend_rw'
                , 'perm_feedback_r'
                , 'perm_treasury_r'
                , 'perm_treasury_w'
            )
            ->withTimestamps();
    }

}
