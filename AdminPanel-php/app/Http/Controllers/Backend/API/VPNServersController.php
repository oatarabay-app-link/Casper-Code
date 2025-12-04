<?php


namespace App\Http\Controllers\Backend\API;


use App\Http\Controllers\Controller;
use App\VPNServer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VPNServersController extends Controller
{


    public function vpn_servers(Request $request)
    {

        $vpnservers = VPNServer::all();
        $result = array();
        $result["code"] = "success";
        $total = 0;
        $servers = array();
        foreach ($vpnservers as $server) {
            $protocols =array();
            foreach($server->protocols as $proto){

                array_push ($protocols, $proto->protocols->name);
            }

            $item = array(
                "serverId" => $server->uuid,
                "serverName" => $server->name,
                "serverIp" => $server->ip,
                "serverLongitude" => $server->longitude,
                "serverLatitude" => $server->latitude,
                "protocolTypes" =>$protocols,
                "disabled"=> $server->is_disabled == 0 ? false:true ,
                "parameters"=> json_decode($server->parameters),
                "createDate"=> Carbon::parse( $server->create_date)->timestamp,
                "country"=> $server->country,
                "systemInfo"=> array(
                "serverId"=> $server->uuid,
                        "lastUpdate"=> null,
                       "isActual"=> false,
                       "healthPercent"=> 1,
                        "serverParams"=>  array(
                            "uptime"=> "" .Carbon::parse( $server->create_date)->timestamp. " ",
                            "cpuLoad"=> 2,
                            "ramAll"=> (9600.00*1000000),
                            "ramUsed"=> (623.00*1000000),
                            "hddAll"=> (484518.00*1000000),
                            "hddUsed"=> (484.00*1000000),
                            "netAll"=> (100000000*1000000),
                            "netUsed"=> (10000*1000000),






                        )
                ),
                "connectionData"=> null

            );


        array_push($servers,$item);
        }

        $result["data"] = array(
            "items" => $servers,
            "page" => 0,
            "pageSize" => 100,
            "total" => $total
        );

        return response()->json($result, 200);


    }

    /*
     *  VPN Servers for User
     */

    public function vpn_servers_for_user(Request $request)
    {
        // todo override the servers  per users here  android request verfiried


        $vpnservers = VPNServer::take(50)->get();
        $result = array();
        $result["code"] = "success";
        $total = 0;
        $servers = array();
        foreach ($vpnservers as $server) {
            $protocols =array();
            foreach($server->protocols as $proto){

                array_push ($protocols, $proto->protocols->name);
            }
            $parameters= json_decode($server->parameters);
            $item = array(
                "serverId" => $server->uuid,
                "serverName" => $server->name,
                "serverIp" => $server->ip,
                "serverLongitude" => $server->longitude,
                "serverLatitude" => $server->latitude,
                "protocolTypes" =>$protocols,
                "disabled"=> $server->is_disabled == 0 ? false:true ,
                "parameters"=> $parameters->vpnServerPropertiesData,
                "createDate"=> Carbon::parse( $server->create_date)->timestamp,
                "country"=> $server->country,
                "systemInfo"=> array(
                    "serverId"=> $server->uuid,
                    "lastUpdate"=> null,
                    "isActual"=> false,
                    "healthPercent"=> 10,
                    "serverParams"=> array(
                        "uptime"=> "" .Carbon::parse( $server->create_date)->timestamp. " ",
                        "cpuLoad"=> 2,
                        "ramAll"=> (9600.00*1000000),
                        "ramUsed"=> (623.00*1000000),
                        "hddAll"=> (484518.00*1000000),
                        "hddUsed"=> (484.00*1000000),
                        "netAll"=> (100000000*1000000),
                        "netUsed"=> (10000*1000000),






                    )
                ),
                "connectionData"=> null

            );


            array_push($servers,$item);
        }

        $result["data"] = $servers;


        return response()->json($result, 200);


    }
}