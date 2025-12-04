<?php

namespace App\Console\Commands\Reports;

use App\Models\AppSignupReport;
use Illuminate\Console\Command;

class CasperReportSyncGenLTVReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperReport:LTVReport';

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
        $sql = 'SELECT `user_email` FROM `payments__checks` group by user_email';
        $users = \DB::connection('mysql')->select($sql);
        echo "\n Found Users".count($users);
        foreach ($users as $user) {
            echo "\n Processing User $user->user_email";
            $email = $user->user_email;
            $sql = "SELECT count(status) as completed FROM `payments__checks` WHERE status='COMPLETED' and user_email='$email'";
            $data = \DB::connection('mysql')->selectOne($sql);
            $completed = $data->completed;
            $sql = "SELECT count(status) as requested FROM `payments__checks` WHERE status='REQUESTED' and user_email='$email'";
            $data = \DB::connection('mysql')->selectOne($sql);
            $requested = $data->requested;
            echo "\n"." COMPLETED " . $completed;
            echo "\n"." REQUESTED " . $requested;
            $sql="SELECT `payments`.`product_description_corrected` as product ,count(`payments`.`product_description_corrected`) as total FROM `payments` where `check_code` in (SELECT `payments__checks`.uuid FROM `payments__checks` where `payments__checks`.`user_email`='twix.y@hotmail.com') group by `payments`.`product_description_corrected`";
            $data = \DB::connection('mysql')->selectOne($sql);
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
