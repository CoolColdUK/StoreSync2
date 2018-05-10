<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Etsy2Controller as Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class IndexController extends Controller
{
    /**
     * Display a listing of etsy store.
     *
     */
    public function index()
    {
        $param['etsy_name_list']=Session::get('menu.etsy.stores');
        $param['pinterest_name_list']=Session::get('menu.pinterest.accounts');
        return view('etsy2.index', $param);
    }

}
