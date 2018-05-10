<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Pinterest2Controller extends Controller
{
    
    const SERVICE = "pinterest";
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
