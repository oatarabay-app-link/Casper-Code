<?php

namespace App\Console\Commands\Reports;

use App\Models\AppSignupReport;
use Illuminate\Console\Command;

class CasperReportSyncGenAppSignups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperReport:AppSignups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sql = 'Select id,email from users';
        $users = \DB::connection('mysql')->select($sql);
        foreach ($users as $user) {
            echo "\n Processing User $user->email ID $user->id";
            $email = $user->email;
            //var_dump($user);
            $sql = "SELECT users.email,
                            IFNULL((SELECT 'First Time Connection' FROM `rad_accts` where username = users.email limit 1),IF(users.is_confirmed = 1, 'App Signed In','App Sign up')) as status,
                            create_time as signup_date,
                            IF(users.is_confirmed = 1, create_time,NULL) as signedin_date,
                            IFNULL((select is_active FROM `user_subscriptions` where `user_subscriptions`.`user_id` = users.id limit 1),'NA / Trial') as subscription,
                            IFNULL((select count(*) FROM `s_m_t_p2_g_o_email_datas` where `s_m_t_p2_g_o_email_datas`.`recipient` = users.email and  `s_m_t_p2_g_o_email_datas`.status ='delivered'),'0' ) as emails_sent,
                            IFNULL((select count(*) FROM `s_m_t_p2_g_o_email_datas` where `s_m_t_p2_g_o_email_datas`.`recipient` = users.email and  `s_m_t_p2_g_o_email_datas`.status !='delivered'),'0' ) as emails_problems,
                            IFNULL(IFNULL((select `intercom_marketing_datas`.`android_device` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),(select `intercom_marketing_datas`.`iOS_device` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1)),'N/A') as device,
                            IFNULL((select `intercom_marketing_datas`.`country` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),'N/A') as country,
                            IFNULL((select `intercom_marketing_datas`.os FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),'N/A') as OS,
                            IFNULL((select `intercom_marketing_datas`.`last_seen_date` FROM `intercom_marketing_datas` where `intercom_marketing_datas`.`email` = users.email limit 1),'2000-01-01') as last_seen
                            FROM `users` where users.email = '$email'";
            $data = \DB::connection('mysql')->selectOne($sql);
            //var_dump($data);
            $appsignup = AppSignupReport::where('email', '=', $user->email)->first();
            //var_dump($appsignup);

            if ($appsignup) {
                echo "\n".'Entry Found for the Email';
                $appsignup->email = $email;
                $appsignup->user_id = $user->id;
                $appsignup->status = $data->status;
                $appsignup->signup_date = $data->signup_date;
                $appsignup->signedin_date = $data->signedin_date;
                $appsignup->subscription = $data->subscription;
                $appsignup->emails_sent = $data->emails_sent;
                $appsignup->emails_problems = $data->emails_problems;
                $appsignup->device = $data->device;
                $appsignup->Country = $data->country;
                $appsignup->OS = $data->OS;
                $appsignup->last_seen = date('Y-m-d', strtotime($data->last_seen));
            } else {
                $appsignup = new AppSignupReport();
                $appsignup->email = $email;
                $appsignup->user_id = $user->id;
                $appsignup->status = $data->status;
                $appsignup->signup_date = $data->signup_date;
                $appsignup->signedin_date = $data->signedin_date;
                $appsignup->subscription = $data->subscription;
                $appsignup->emails_sent = $data->emails_sent;
                $appsignup->emails_problems = $data->emails_problems;
                $appsignup->device = $data->device;
                $appsignup->Country = $data->country;
                $appsignup->OS = $data->OS;
                $appsignup->last_seen = date('Y-m-d H:i:s', strtotime($data->last_seen));
                echo "\n".'Added New Entry for the Email';
            }

            $appsignup->save();
        }
    }
}
