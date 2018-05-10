<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

/*
|--------------------------------------------------------------------------
| Pinterest
|--------------------------------------------------------------------------
|
| 
|
 */
    /**
     * List of all pinterest account
     *
     * @var array
     */
    public function PinterestAccountsAll()
    {
        return $this->belongsToMany('App\PinterestAccount', 'user_pinterest')
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
     * List of valid pinterest account - not expired
     *
     * @var array
     */
    public function PinterestAccounts()
    {
        return $this->belongsToMany('App\PinterestAccount', 'user_pinterest')
            ->withPivot('api_key'
                , 'api_secret'
                , 'access_token'
                , 'refresh_token'
                , 'read_public'
                , 'write_public'
                , 'read_relationships'
                , 'write_relationships'
            )
            ->withTimestamps()
            ->where('pinterest_accounts.updated_at',">",Carbon::now()->addHours(-config('pinterest.expiry_hr')));
    }

    /**
     * Get the list of pinterest account linked to user
     *
     * @var array
     */
    public static function PinterestAccountNameList($user_id)
    {
        $usr = self::find($user_id);
        $names = array();
        foreach ($usr->PinterestAccountsAll as $acc) {
            $names[] = $acc->username;
        }
        return $names;
    }
/*
|--------------------------------------------------------------------------
| Etsy
|--------------------------------------------------------------------------
|
| 
|
 */
    /**
     * List of etsy stores related to user
     *
     * @var array
     */
    public function EtsyStoresAll()
    {
        return $this->belongsToMany('App\EtsyStore', 'user_etsy')
            ->withPivot('api_key'
                , 'api_secret'
                , 'oauth_key'
                , 'oauth_secret'
                , 'perm_email_r'
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

    /**
     * List of etsy stores related to user
     *
     * @var array
     */
    public function EtsyStores()
    {
        return $this->belongsToMany('App\EtsyStore', 'user_etsy')
            ->withPivot('api_key'
                , 'api_secret'
                , 'oauth_key'
                , 'oauth_secret'
                , 'perm_email_r'
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
            ->withTimestamps()
            ->where('etsy_stores.updated_at',">",Carbon::now()->addHours(-config('etsy.expiry_hr')));;
    }
    /**
     * Get the list of store names linked to user
     *
     * @var array
     */
    public static function EtsyStoreNameList($user_id)
    {
        $usr = self::find($user_id);
        $names = array();
        foreach ($usr->EtsyStoresAll as $store) {
            $names[] = $store->name;
        }
        return $names;
    }

}
