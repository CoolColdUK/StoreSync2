<?php

return [
    /*
    |--------------------------------------------------------------------------
    | URL for pinterest api
    |--------------------------------------------------------------------------
    |
    | This is the pinterest api url
    |
     */
    /*
    |--------------------------------------------------------------------------
    | pInterest api key
    |--------------------------------------------------------------------------
    |
    | This is the pinterest api key for secret communication
    |

     */
    'api_key' => "",
    'api_secret' => "",

    //https://developers.pinterest.com/docs/api/overview/?
    //read_public,write_public,read_relationships,write_relationships
    'permission' => ["read_public", "write_public"],
    'expiry_hr' => 12,
];
