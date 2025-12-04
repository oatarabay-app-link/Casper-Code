<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use DB;
/**
 * Class DashboardController.
 */
class ConnectedUsersController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the Count of Users per Server IP  Registered.
        $sql= "select  nasipaddress as 'Server_IP',count(*) as 'No_of_Users' FROM radacct WHERE acctstoptime IS NULL group by nasipaddress";
        $sql_total= "select  count(nasipaddress) as 'Total'  FROM radacct WHERE acctstoptime IS NULL";
        $users = \DB::connection("radius_mysql")->select($sql);
        $users_total =\DB::connection("radius_mysql")->select($sql_total);
        $users_total=$users_total[0]->Total;
        return view('backend.vpn.connected_users',compact('users','users_total'));
    }

    public function connected_users_by_country()
    {
        // Get the Count of Users per Server IP  Registered.
        $sql= "SELECT  count(*) as 'No_of_Users',IP2LocationLite.Country FROM radacct    
               left join IP2LocationLite on INET_ATON(callingstationid) Between  IP2LocationLite.IP_START AND IP2LocationLite.IP_END
               where  acctstoptime is Null
               group by IP2LocationLite.Country";
        $sql_total= "select  count(nasipaddress) as 'Total'  FROM radacct WHERE acctstoptime IS NULL";
        $users = \DB::connection("radius_mysql")->select($sql);
        $users_total =\DB::connection("radius_mysql")->select($sql_total);
        $users_total=$users_total[0]->Total;
        return view('backend.vpn.connected_users_by_country',compact('users','users_total'));
    }

    public function connected_users_per_server_by_country()
    {
        // Get the Count of Users per Server IP  Registered.
        $sql= "SELECT  nasipaddress as 'Server_IP',count(*) as 'No_of_Users',IP2LocationLite.Country FROM radacct    
               left join IP2LocationLite on INET_ATON(callingstationid) Between  IP2LocationLite.IP_START AND IP2LocationLite.IP_END
               where  acctstoptime is Null
               group by IP2LocationLite.Country";
        $sql_total= "select  count(nasipaddress) as 'Total'  FROM radacct WHERE acctstoptime IS NULL";
        $users = \DB::connection("radius_mysql")->select($sql);
        $users_total =\DB::connection("radius_mysql")->select($sql_total);
        $users_total=$users_total[0]->Total;
        return view('backend.vpn.connected_users_per_server_by_country',compact('users','users_total'));
    }
}
