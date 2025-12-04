<?php

namespace App\Http\Controllers\Backend\CasperVPN\Reports;

use Illuminate\Http\Request;
use App\Models\AppSignupReport;
use App\Http\Controllers\Controller;

class AppSignupReportController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 100;
        $date_from = date('2018-m-01'); //date('Y-m-d');
        $date_to = date('Y-m-d');
        //$date_to = '2021-01-02';
        $date_from = isset($request->date_from) ? $request->date_from : $date_from;
        $date_to = isset($request->date_to) ? $request->date_to : $date_to;
        $email = isset($request->email) ? $request->email : null;
        $os = isset($request->os) ? $request->os : null;
        $country = isset($request->country) ? $request->country : null;
        $stage = isset($request->stage) ? $request->stage : null;

        //var_dump($date_from);
        //exit();

//        $sql = "SELECT users.email,
//                IFNULL((SELECT 'First Time Connection' FROM `rad_accts` where username = users.email limit 1),IF(users.is_confirmed = 1, 'App Signed In','App Sign up')) as status,
//                create_time as signup_date,
//                IF(users.is_confirmed = 1, create_time,'N/A') as signedin_date,
//                IFNULL((select is_active FROM `user_subscriptions` where `user_subscriptions`.`user_id` = users.id limit 1),'NA / Trial') as subscription,
//                IFNULL((select count(*) FROM `s_m_t_p2_g_o_email_datas` where `s_m_t_p2_g_o_email_datas`.`recipient` = users.email and  `s_m_t_p2_g_o_email_datas`.status ='delivered'),'0' ) as emails,
//                IFNULL((select count(*) FROM `s_m_t_p2_g_o_email_datas` where `s_m_t_p2_g_o_email_datas`.`recipient` = users.email and  `s_m_t_p2_g_o_email_datas`.status !='delivered'),'0' ) as emails_problems,
//                IFNULL(IFNULL((select `intercom_marketing_datas`.`android_device` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),(select `intercom_marketing_datas`.`iOS_device` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1)),'N/A') as device,
//                IFNULL((select `intercom_marketing_datas`.`country` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),'N/A') as country,
//                IFNULL((select `intercom_marketing_datas`.os FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),'N/A') as OS,
//                IFNULL((select `intercom_marketing_datas`.`last_seen_date` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),'N/A') as last_seen
//                FROM `users` where create_time > '$date_from' and create_time < '$date_to'";
//
//        $users = \DB::connection('mysql')->select($sql);

//        $users = AppSignupReport::where('signup_date', '>', $date_from)
//            ->where('signup_date', '<', $date_to)
//            ->latest()
//            ->paginate($perPage);

        $users = AppSignupReport::query();
        $users->where('signup_date', '>', $date_from);
        $users->where('signup_date', '<', $date_to);

        if ($email) {
            $users->where('email', 'LIKE', "$email%");
        }

        if ($country) {
            $users->where('country', 'LIKE', "$country%");
        }
        if ($os) {
            $users->where('os', 'LIKE', "$os%");
        }
        if (($stage) && ($stage !=0)) {
            $users->where('status', '=', "$stage");
        }

       $users=$users->latest()->paginate($perPage);



        return view('backend.reports.app_signup_report', compact('users'));
    }
}
