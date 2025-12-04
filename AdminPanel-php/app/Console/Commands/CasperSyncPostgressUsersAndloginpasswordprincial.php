<?php

namespace App\Console\Commands;

use App\Protocol;
use App\Subscription;
use App\UserSubscription;
use App\VPNServer;
use App\VPNServerProtocol;
use App\Models\Auth\User;
use Illuminate\Console\Command;

class CasperSyncPostgressUsersAndloginpasswordprincial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * The command Sync all the users and
     * update the admin panel database from
     * the postgres database.
     */
    protected $signature = 'caspersync:Users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
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

        $sql= "select * from users  where user_login like '%@%' order by random()";
        $postgree_users =\DB::connection("system_users")->select($sql);
        $this->info("Counting Users " . count($postgree_users) . " Loading....");
        $count=1;
        foreach ($postgree_users as $puser) {
            $this->info("Looking for UUID -> ". $puser->id);
            $local_users = User::where('uuid',$puser->id)
                ->orWhere('email',$puser->user_login)
                ->first();
            if ($local_users == null) {
                $this->info("Not Found UUID " . $puser->id . " Creating....");
                $this->info("Loading Password Information " . $puser->id . " Loading....");
                $this->info("Loading Subscription Information " . $puser->id . " Loading....");
                $sql_password = "select * from login_password_principal where login ='" . strtolower ($puser->user_login) . "' or login ='" . strtolower ($puser->user_login) . "' ";
                $user_password = \DB::connection("system_users")->selectOne($sql_password);
                if (($puser->user_subscription_id != null) or ($puser->user_subscription_id != "")){
                //$sql_user_subscriptions = "select * from users_subscriptions where id='" . $puser->user_subscription_id . "'";
                //$user_subscriptions = \DB::connection("system_users")->selectOne($sql_user_subscriptions);
                if ($user_password != null) {

                $usr = array(
                    'uuid' => $puser->id,
                    'user_uuid' => $puser->id,
                    'first_name' => $puser->first_name,
                    'last_name' => $puser->last_name,
                    'email' => $puser->user_login,
                    'password' => ($user_password->password == null ? "" : $user_password->password),
                    'active' => "1",
                    'confirmed' => "1",
                    'login' => $user_password->login,
                    'old_login' => $user_password->old_login,
                    'login_pass_id' => $user_password->id,
                    'is_confirmed' => $user_password->is_confirmed,
                    'confirm_code' => $user_password->confirm_code,
                    'version' => $user_password->version,
                    'create_time' => date('Y-m-d H:i:s', strtotime($user_password->create_time)),
                    'user_login' => $puser->user_login,
                    'old_user_login' => $puser->old_user_login,
                    'phone' => $puser->phone,
                    'user_subscription_id' => $puser->user_subscription_id,
                    'is_blocked' => $puser->is_blocked,
                    'is_deleted' => $puser->is_deleted,
                    'description' => "",
                    'tsv' => $puser->tsv,
                    'affliate_ref' => $puser->affiliate_ref,
                    'last_active_date' => $puser->last_active_date,
                    'create_date' => date('Y-m-d H:i:s', strtotime($puser->create_date)),
                    //'vpn_pass' => $user_subscriptions->vpn_pass,
                );
                $user = User::create($usr);

            }
                }
            } else{

            }

            $count=$count+1;
            $this->info("Counting Processed Users " . $count . "");

        }
    }
}
