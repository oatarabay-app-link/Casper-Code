<?php


namespace App\Http\Controllers\Backend\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IntercomController extends Controller
{

    public function intercom_info(Request $request)
    {

        $result =array(
            "data" => array(
                "appId" => env("INTERCOM_APP_ID")
            ),
            "code" =>"success"
        );
        return response()->json($result, 200);
    }

}