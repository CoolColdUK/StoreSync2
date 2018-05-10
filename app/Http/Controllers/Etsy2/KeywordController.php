<?php

namespace App\Http\Controllers\Etsy2;

use App\Http\Controllers\Etsy2Controller as Controller;

//etsy2 controllers
use App\Http\Controllers\Etsy2\ListingInventoryRemote;
use App\Http\Controllers\Etsy2\ListingRemote;

use App\EtsyStore;
use App\Http\CcHelpers\CcArray;
use App\Http\Resources\EtsyListingCsvResource;
use App\Http\Resources\EtsyListingEtsyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class KeywordController extends Controller
{
    /**
     * keyword generation
     *
     * @return \Illuminate\Http\Response $request
     */
    public function keyword(Request $request)
    {
        $etsyService = ConnectionController::GetPublicService();
        
        $final = [];
        $keywords = explode("\n", $request->keyword);
        $groups = explode("\n", $request->groups);
        $result_tags = [];
        $result_title = [];
        $result_title1 = [];
        $result_title2 = [];
        $result_title3 = [];
        $result_title4 = [];
        foreach ($keywords as $k) {
            if (strlen($k) == 0) {continue;}
            $kk = trim(preg_replace('/[^a-zA-Z0-9-_\s]/', '', $k));
            
            //get keyword
            $result = CommonController::GetKeyword($etsyService, $kk,
                $result_tags, $result_title, $result_title1, 
                $result_title2, $result_title3, $result_title4);
        }

        $result = CcArray::TransposeArray(
            [array_keys($result_tags), array_values($result_tags),
                array_keys($result_title), array_values($result_title),
                array_keys($result_title1), array_values($result_title1),
                array_keys($result_title2), array_values($result_title2),
                array_keys($result_title3), array_values($result_title3),
                array_keys($result_title4), array_values($result_title4)]);
                
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . trim(str_replace(" ", "_", $keywords[0])) . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $callback = function () use ($result) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['tags', 'tag count',
                'title', 'title count',
                'title1', 'title1 count',
                'title2', 'title2 count',
                'title3', 'title3 count',
                'title4', 'title4 count',
            ]);
            foreach ($result as $r) {
                fputcsv($file, $r);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
