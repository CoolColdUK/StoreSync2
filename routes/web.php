<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test', 'EtsyListingController@test');

// /*
// |--------------------------------------------------------------------------
// | Etsy Routes
// |--------------------------------------------------------------------------
// |
// | Route for etsy only
// |
//  */
//no db, just direct access to and from etsy

//show all stores like in etsy
Route::get('/etsy2',
['uses' => 'Etsy2\IndexController@index'])
->name('etsy2');

//link etsy store
Route::get('/etsy2/link', 'Etsy2\LinkController@create')->name('etsy2.link');
Route::get('/etsy2/link/complete', 'Etsy2\LinkController@store')->name('etsy2.link.complete');
Route::get('/etsy2/unlink/{etsyStore}', ['uses' => 'Etsy2\UnlinkController@destroy'])
    ->where('etsyStore', '[A-Za-z0-9]+')
    ->name('etsy2.unlink');

//show all stores like in etsy
Route::post('/etsy2/keyword',
['uses' => 'Etsy2\KeywordController@keyword'])
//->where('keyword', '[A-Za-z0-9]+')
->name('etsy2.keyword');

//show all stores like in etsy
Route::post('/etsy2/shops',
['uses' => 'Etsy2\ResearchStoreController@shops'])
//->where('keyword', '[A-Za-z0-9]+')
->name('etsy2.shops');



//download all listing to db, regardless it existed or not
Route::get('/etsy2/{etsyStore}/download',
['uses' => 'Etsy2\DownloadController@download'])
->where('etsyStore', '[A-Za-z0-9]+')
->name('etsy2.download');

//upload file for updating db
Route::post('/etsy2/{etsyStore}/upload',
['uses' => 'Etsy2\UploadController@upload'])
->where('etsyStore', '[A-Za-z0-9]+')
->name('etsy2.upload');


/*
|--------------------------------------------------------------------------
| Pinterest 2 Routes
|--------------------------------------------------------------------------
|
| Route for pinterest only
|
 */

//link pinterest store
Route::get('/pinterest2/link', 'Pinterest2\LinkController@create')
    ->name('pinterest2.link');
Route::get('/pinterest2/link/complete', 'Pinterest2\LinkController@store')
    ->name('pinterest2.link.complete');
Route::get('/pinterest2/unlink/{pinterestAccount}', ['uses' => 'Pinterest2\UnlinkController@destroy'])
    ->where('pinterestAccount', '[A-Za-z0-9]+')
    ->name('pinterest2.unlink');