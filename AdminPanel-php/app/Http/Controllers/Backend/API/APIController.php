<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use DB;

use Illuminate\Http\Request;

/**
 * Class APIController
 * @package App\Http\Controllers\Backend
 */
class APIController extends Controller
{


    /**
     * @path /subscriptions/getserverdate
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_server_time()
    {

        $result = array(
            'data' => time(),
            'code' => "success"
        );
        return response()->json($result, 200);

    }

    /**
     * @path /subscriptions/appversion
     * @return \Illuminate\Http\JsonResponse
     */
    public function versions()

    {
        $versions = array(
            "data" => array(
                "androidVersion" => 54.2,
                "iosVersion" => 2.6,
                "windowsVersion" => 1,
                "macVersion" => 1
            ),
            "code" => "success"
        );
        return response()->json($versions, 200);
    }


}
