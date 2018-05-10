<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PinterestAccount extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'username', 'first_name', 'last_name',
        'url', 
        //'image_url', 'image_width', 'image_height',
        //'counts_pins', 'counts_following', 'counts_followers', 'counts_boards',
    ];
    protected $keyType = 'biginteger';

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
     * Save json data to db. Update if exist, insert if not
     */
    public static function UpdateOrCreateFromJson($pin_acc_json)
    {
        
        return self::updateOrCreate(
            ['id' => $pin_acc_json['id']], [
                'id' => $pin_acc_json['id'],
                'username' => $pin_acc_json['username'],
                'first_name' => $pin_acc_json['first_name'],
                'last_name' => $pin_acc_json['last_name'],
                'url' => $pin_acc_json['url'],

                'image_url' => isset($pin_acc_json['image']['60x60']['url'])
                ? $pin_acc_json['image']['60x60']['url'] : "",
                'image_width' => isset($pin_acc_json['image']['60x60']['width'])
                ? $pin_acc_json['image']['60x60']['width'] : 0,
                'image_height' => isset($pin_acc_json['image']['60x60']['height'])
                ? $pin_acc_json['image']['60x60']['height'] : 0,

                'counts_pins' => isset($pin_acc_json['counts']['pins']) ? $pin_acc_json['counts']['pins'] : 0,
                'counts_following' => isset($pin_acc_json['counts']['following']) ? $pin_acc_json['counts']['following'] : 0,
                'counts_followers' => isset($pin_acc_json['counts']['followers']) ? $pin_acc_json['counts']['followers'] : 0,
                'counts_boards' => isset($pin_acc_json['counts']['boards']) ? $pin_acc_json['counts']['boards'] : 0,
            ]);
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
            'access_token' => $token,
            'refresh_token' => $token_secret,
            'read_public' => in_array("read_public", $permission),
            'write_public' => in_array("write_public", $permission),
            'read_relationships' => in_array("read_relationships", $permission),
            'write_relationships' => in_array("write_relationships", $permission),
        ]);
    }

    /**
     * link store to user
     */
    public function Owner()
    {
        return $this->belongsToMany('App\User', 'user_pinterest')
            ->withPivot('api_key'
                , 'api_secret'
                , 'access_token'
                , 'refresh_token'
                , 'read_public'
                , 'write_public'
                , 'read_relationships'
                , 'write_relationships'
            )
            ->withTimestamps();
    }

    /**
     * link account to boards, only items downloaded within last time period
     */
    public function BoardsAll()
    {
        //only shows board updated within certain period defined in $expiry_hr
        return $this->hasMany('App\PinterestBoard', 'pinterest_account_id');
    }
    /**
     * link account to boards, only items downloaded within last time period
     */
    public function Boards()
    {
        //only shows board updated within certain period defined in $expiry_hr
        return $this->hasMany('App\PinterestBoard', 'pinterest_account_id')
            ->where('updated_at',">",Carbon::now()->addHours(-config('pinterest.expiry_hr')));
    }


    /**
     * Download all boards from web
     */
    public function BoardsMirrorFromWeb()
    {
        //get the last update timestamp
        $board = $this->Boards()->orderBy('updated_at', desc)->first();

        return (strtotime($board->updated_at) < strtotime("-24 hours"));
    }
}
