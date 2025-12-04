<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\VPNServer;
use Illuminate\Http\Request;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        //check if the request contain date to from and platform
        $date_from = date('Y-m-01');;//date('Y-m-d');
        $date_to = date('Y-m-d');
        $platform = "all";

        //Load If we have request variables
        $date_from = isset($request->date_from) ? $request->date_from : $date_from;
        $date_to = isset($request->date_to) ? $request->date_to : $date_to;
        $platform = isset($request->platform) ? $request->platform : $platform;

        // Get a Count of Servers
        $servers_total = VPNServer::count();
        $servers_active = VPNServer::where("is_deleted", 1)
            ->where("is_disabled", 1)
            ->count();

        //Count the users
        $users_total = User::where('create_time','>',$date_from)
            ->where('create_time','<',$date_to)
            ->count();


        $users_active = User::where('active',1)
            ->where('is_confirmed',1)
            ->where('is_blocked',0)
            ->where('create_time','>',$date_from)
            ->where('create_time','<',$date_to)
            ->count();

        $filter_info = "Date From $date_from - $date_to Platform : $platform";


        $app_signups = User::where('create_time','>',$date_from)
            ->where('create_time','<',$date_to)
            ->count();
        $date_from_date=date_create($date_from);
        $date_to_date=date_create($date_to);
        $total_days = date_diff($date_to_date,$date_from_date);

        $total_days = $total_days->days;
        $remainder = ($total_days)/30;
        $sql_app_signups_chart= "select CONCAT(CONCAT(MAX(create_time),' - '),MIN(create_time)) DT,count(id)as total FROM users where create_time > '$date_from' AND create_time < '$date_to' group by floor(datediff(now(), create_time) / $remainder ) ORDER BY MIN(`create_time`) ASC";

        $app_signups_chart = \DB::connection("mysql")->select($sql_app_signups_chart);

        $app_signups_chart=\GuzzleHttp\json_encode($app_signups_chart);



        //var_dump($app_signups_chart);
        //exit();
        // App Signed In Code
        $app_signedins = User::where('create_time','>',$date_from)
            ->where('is_confirmed',1)
            ->where('create_time','<=',$date_to)
            ->count();

        $sql_app_signedins_chart= "select CONCAT(CONCAT(MAX(create_time),' - '),MIN(create_time)) DT,count(id)as total FROM users where create_time > '$date_from' AND create_time < '$date_to'  AND is_confirmed=1 group by floor(datediff(now(), create_time) / $remainder )  ORDER BY MIN(`create_time`) ASC";

        $app_signedins_chart = \DB::connection("mysql")->select($sql_app_signedins_chart);

        $app_signedins_chart=\GuzzleHttp\json_encode($app_signedins_chart);
        // End App Signed In Code


        //First time connections code
        $sql_first_time_connections="select max(d.days) as days,count(t.total) as total from (select count(`radacctid`) as total from radacct where username in (select email from users where create_time > '$date_from' and create_time <'$date_to' AND is_confirmed=1 ) group by username) t , (select datediff(MAX(acctstarttime),MIN(acctstarttime)) as days from radacct where username in (select email from users where create_time > '$date_from' and create_time <'$date_to' AND is_confirmed=1 ) ) d";

        $app_first_time_connections_chart = \DB::connection("mysql")->select($sql_first_time_connections);
        $app_first_time_connections_total= ($app_first_time_connections_chart[0]->total);
        $app_first_time_connections_days= ($app_first_time_connections_chart[0]->days);
        $remainder=$app_first_time_connections_days/30;
        //$sql_first_time_connections="select CONCAT(CONCAT(MAX(acctstarttime),' - '),MIN(acctstarttime)) DT,count(`radacctid`) as total from radacct where username in (select email from users where create_time > '$date_from' and create_time <'$date_to' AND is_confirmed=1 ) group by username,floor(datediff(now(), acctstarttime) / $remainder ) ORDER BY MIN(`acctstarttime`) ASC";
        $sql_first_time_connections ="select CONCAT(CONCAT(MAX(acctstarttime),' - '),MIN(acctstarttime)) DT , count(total) as total from (select MAX(acctstarttime) as acctstarttime, count(`radacctid`) as total from radacct where username in (select email from users where create_time > '$date_from' and create_time <'$date_to' AND is_confirmed=1 ) group by username) t group by floor(datediff(now(), acctstarttime) / $remainder ) ORDER BY MIN(`acctstarttime`) ASC";
        //echo $sql_first_time_connections;
        //exit();

        $app_first_time_connections_chart = \DB::connection("mysql")->select($sql_first_time_connections);
        $app_first_time_connections_chart=\GuzzleHttp\json_encode($app_first_time_connections_chart);
        // END First Time Connections code

        //check if they are being sent as 0s
        $users_active = intval($users_active);
        $users_total = intval($users_total);
        $servers_total = intval( $servers_total);
        if ($users_total==0){

            $users_total=1;
        }

        if($servers_total==0){
            $servers_total=1;

        }




        $sql_in_app_purchases="SELECT * FROM payments  where product_description in (\"1 Month\", \"1_Month\", \"1_Year\", \"6_Months\", \"6 Months\", \"1%20Month\", \"Lifetime\", \"1Y\", \"1 Year\", \"1 Month Subscription\", \"6M\", \"1M\", \"6 Month\", \"6 Month Subscription\") and payments.create_date > '$date_from' AND payments.create_date < '$date_to'   ";
        //echo $sql_in_app_purchases;
        //exit();

        $in_app_purchases=  \DB::connection("mysql")->select($sql_in_app_purchases);
        $in_app_purchases_total= count($in_app_purchases);
        $remainder=$in_app_purchases_total/30;

        $sql_in_app_purchases="SELECT CONCAT(CONCAT(MAX(payments.create_date),' - '),MIN(payments.create_date)) DT,count(id) as total  FROM payments  where product_description in (\"1 Month\", \"1_Month\", \"1_Year\", \"6_Months\", \"6 Months\", \"1%20Month\", \"Lifetime\", \"1Y\", \"1 Year\", \"1 Month Subscription\", \"6M\", \"1M\", \"6 Month\", \"6 Month Subscription\") and payments.create_date > '$date_from' AND payments.create_date < '$date_to' group by floor(datediff(now(), payments.create_date) / $remainder )  ORDER BY MIN(payments.create_date) ASC  ";
        $in_app_purchases_chart=  \DB::connection("mysql")->select($sql_in_app_purchases);
        $in_app_purchases_chart = \GuzzleHttp\json_encode($in_app_purchases_chart);




        /// SALES CHARTING
        ///
        ///
        $sql_sales="SELECT SUM(payments.payment_sum) as total  FROM payments inner join payment_infos on payments.id = payment_infos.payments_table_id where payment_infos.product_description in (\"1 Month\", \"1_Month\", \"1_Year\", \"6_Months\", \"6 Months\", \"1%20Month\", \"Lifetime\", \"1Y\", \"1 Year\", \"1 Month Subscription\", \"6M\", \"1M\", \"6 Month\", \"6 Month Subscription\") and payments.create_date > '$date_from' AND payments.create_date < '$date_to'  group by DATE(payments.create_date)  ";
        $sales=  \DB::connection("mysql")->select($sql_sales);
        $sales_total= count($sales);
        $remainder=$sales_total/30;

        $sql_sales="SELECT CONCAT(CONCAT(MAX(payments.create_date),' - '),MIN(payments.create_date)) DT,SUM(payments.payment_sum) as total FROM payments inner join payment_infos on payments.id = payment_infos.payments_table_id where payment_infos.product_description in (\"1 Month\", \"1_Month\", \"1_Year\", \"6_Months\", \"6 Months\", \"1%20Month\", \"Lifetime\", \"1Y\", \"1 Year\", \"1 Month Subscription\", \"6M\", \"1M\", \"6 Month\", \"6 Month Subscription\") and payments.create_date > '$date_from' AND payments.create_date < '$date_to' group by floor(datediff(now(), payments.create_date) / $remainder )  ORDER BY MIN(payments.create_date) ASC  ";
        $sales_chart=  \DB::connection("mysql")->select($sql_sales);
        $sales_chart = \GuzzleHttp\json_encode($sales_chart);



        /// SALES Free Premiums
        $sql_sales_free_premium_trial="SELECT  datediff(MAX(payments.create_date), MIN(payments.create_date)) as days , count(payments.id) as total FROM payments inner join payment_infos on payments.id = payment_infos.payments_table_id where payment_infos.product_description not in (\"1 Month\", \"1_Month\", \"1_Year\", \"6_Months\", \"6 Months\", \"1%20Month\", \"Lifetime\", \"1Y\", \"1 Year\", \"1 Month Subscription\", \"6M\", \"1M\", \"6 Month\", \"6 Month Subscription\") and payments.create_date > '$date_from' AND payments.create_date < '$date_to'  ";
       //echo $sql_sales_free_premium_trial;
       //exit();
        $sales_free_premium_trial=  \DB::connection("mysql")->select($sql_sales_free_premium_trial);
        $sales_free_premium_trial= $sales_free_premium_trial[0];
        $sales_free_premium_trial_days = $sales_free_premium_trial->days;
        $sales_free_premium_trial= $sales_free_premium_trial->total;


        $sales_free_premium_trial_total= ($sales_free_premium_trial);
        $remainder=$sales_free_premium_trial_days/30;

        $sql_sales_free_premium_trial="SELECT CONCAT(CONCAT(MAX(payments.create_date),' - '),MIN(payments.create_date)) DT,count(payments.id) as total FROM payments inner join payment_infos on payments.id = payment_infos.payments_table_id where payment_infos.product_description not in (\"1 Month\", \"1_Month\", \"1_Year\", \"6_Months\", \"6 Months\", \"1%20Month\", \"Lifetime\", \"1Y\", \"1 Year\", \"1 Month Subscription\", \"6M\", \"1M\", \"6 Month\", \"6 Month Subscription\") and payments.create_date > '$date_from' AND payments.create_date < '$date_to' group by floor(datediff(now(), payments.create_date) / $remainder )  ORDER BY MIN(payments.create_date) ASC";
       // echo $sql_sales_free_premium_trial . "\n";
        $sales_free_premium_trial_chart=  \DB::connection("mysql")->select($sql_sales_free_premium_trial);
        $sales_free_premium_trial_chart = \GuzzleHttp\json_encode($sales_free_premium_trial_chart);

//        echo $sql_in_app_purchases;
//
//        exit();


        // Emails Delivered


        $sql_emails_delivered="SELECT  CONCAT(CONCAT(MAX(DATE(email_tx)),' - '),MIN(DATE(email_tx))) DT,count(id) as total FROM `s_m_t_p2_g_o_email_datas` where status ='delivered'  and subject like '%Confirmation%'  and email_tx < '$date_to' and email_tx > '$date_from' group by DATE(email_tx)";
       // echo $sql_emails_delivered .'\n';
        $emails_delivered=  \DB::connection("mysql")->select($sql_emails_delivered);
        $emails_delivered_total= count($emails_delivered);
        $remainder=$emails_delivered_total/30;
        $sql_emails_delivered="SELECT  CONCAT(CONCAT(MAX(DATE(email_tx)),' - '),MIN(DATE(email_tx))) DT,count(id) as total FROM `s_m_t_p2_g_o_email_datas` where status ='delivered' and   subject like '%Confirmation%' and email_tx < '$date_to' and email_tx > '$date_from'  group by floor(datediff(now(), DATE(email_tx)) / $remainder )  ORDER BY MIN(`email_tx`) ASC  ";

        $emails_delivered_chart=  \DB::connection("mysql")->select($sql_emails_delivered);
        $emails_delivered_total=0;
        foreach ($emails_delivered_chart as $a){
            $emails_delivered_total= $emails_delivered_total+$a->total;
        }
        $emails_delivered_chart = \GuzzleHttp\json_encode($emails_delivered_chart);





        // Emails Not Delivered


        $sql_emails_not_delivered="SELECT  CONCAT(CONCAT(MAX(DATE(email_tx)),' - '),MIN(DATE(email_tx))) DT,count(id) as total FROM `s_m_t_p2_g_o_email_datas` where status !='delivered' and email_tx < '$date_to' and email_tx > '$date_from' group by DATE(email_tx)";
        //echo $sql_emails_not_delivered ."<br>";
        $emails_not_delivered=  \DB::connection("mysql")->select($sql_emails_not_delivered);
        $emails_not_delivered_total= count($emails_not_delivered);
        $remainder=$emails_not_delivered_total/30;
        $sql_emails_not_delivered="SELECT  CONCAT(CONCAT(MAX(DATE(email_tx)),' - '),MIN(DATE(email_tx))) DT,count(id) as total FROM `s_m_t_p2_g_o_email_datas` where status !='delivered' and email_tx < '$date_to' and email_tx > '$date_from'  group by floor(datediff(now(), DATE(email_tx)) / $remainder )  ORDER BY MIN(`email_tx`) ASC  ";
        $emails_not_delivered_chart=  \DB::connection("mysql")->select($sql_emails_not_delivered);
        //echo $sql_emails_not_delivered. "\n";
        $emails_not_delivered_total=0;
        foreach ($emails_not_delivered_chart as $a){
            $emails_not_delivered_total= $emails_delivered_total+$a->total;
        }
        $emails_not_delivered_chart = \GuzzleHttp\json_encode($emails_not_delivered_chart);

       //exit();





        //Subscriptions

        $sql_subscriptions = "SELECT DATE(subscription_start_date) as DT, count(id) as total FROM `user_subscriptions` WHERE subscription_start_date >'$date_from' AND subscription_start_date < '$date_to'  GROUP by date(subscription_start_date) ORDER BY `DT` ASC";
        $subscriptions = \DB::connection("mysql")->select($sql_subscriptions);
        $subscriptions_total= count($subscriptions);
        $remainder=$subscriptions_total/30;
        $sql_subscriptions = "SELECT CONCAT(CONCAT(MAX(DATE(subscription_start_date)),' - '),MIN(DATE(subscription_start_date))) DT,count(id) as total FROM `user_subscriptions` WHERE subscription_start_date >'$date_from' AND subscription_start_date < '$date_to'  group by floor(datediff(now(), DATE(subscription_start_date)) / $remainder )  ORDER BY MIN(`subscription_start_date`) ASC";
        $subscriptions_chart =\DB::connection("mysql")->select($sql_subscriptions);
        $subscriptions_chart = \GuzzleHttp\json_encode($subscriptions_chart);




        //dd($users);

        return view('backend.dashboard', compact('servers_total','servers_active','users_active','users_total','filter_info'
                                                        ,'app_signups','app_signups_chart'
                                                        ,'app_signedins','app_signedins_chart'
                                                        ,'app_first_time_connections_total','app_first_time_connections_chart'
                                                        ,'in_app_purchases_total','in_app_purchases_chart'
                                                        ,'sales_total','sales_chart'
                                                        ,'sales_free_premium_trial_total','sales_free_premium_trial_chart'
                                                        ,'emails_delivered_total','emails_delivered_chart'
                                                        ,'emails_not_delivered_total','emails_not_delivered_chart'
                                                        , 'subscriptions_total','subscriptions_chart'));

    }

    function reducedata($a){

        $count = count($a);
        echo "<br>" . $count;
        $remainder = 1;
            if ($count > 30 ){

                $remainder = $count/30;
            }

            //echo var_dump($a);
        $total = 0;
        $rtotal = 0;
        foreach ($a as $ar){
                //echo $key;
                //echo "\n" . var_dump($ar);

                $total=$total+$ar->total;
                $rtotal=$rtotal+ (($ar->total/$remainder));
                echo "<br>".$ar->DT. " -- " .$ar->total;
        }

        $t = $count/$remainder;



        echo "<br> $t   ---    $remainder  -- $total   -----   $rtotal";

        for($i=1; $i<=$t; $i++){
            echo "The number is " . $i . "<br>";
        }







        exit();


    }
}
