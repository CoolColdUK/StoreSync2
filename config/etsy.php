<?php

    return [
        /*
          |--------------------------------------------------------------------------
          | URL for etsy api
          |--------------------------------------------------------------------------
          |
          | This is the etsy api url
          |
         */

        'url' => "https://openapi.etsy.com/",
        'url_v2' => "https://openapi.etsy.com/v2/",
        'url_v3' => "https://openapi.etsy.com/v3/",
        /*
          |--------------------------------------------------------------------------
          | etsy api key
          |--------------------------------------------------------------------------
          |
          | This is the etsy api key for secret communication
          |
         */
        'api_key' => "api",
        'api_secret' => "secret",
        'permission' => ["listings_w", "listings_r", "listings_d", "email_r"],
        'expiry_hr' => 12,

        'spacer' => "*****storesync*****",
    ];
    
