<?php

namespace App\Console\Commands;

use App\Protocol;
use App\Subscription;
use App\UserSubscription;
use App\VPNServer;
use App\VPNServerProtocol;
use App\Models\Auth\User;
use Illuminate\Console\Command;

class CasperSyncPostgressUsersSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:UserSubscriptions';

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
        $sql_user_subscriptions = "select * from users_subscriptions";
        $user_subscriptions = \DB::connection("system_users")->select($sql_user_subscriptions);
        $this->info("Counting Users " . count($user_subscriptions) . " Loading....");
        $count = 1;
        foreach ($user_subscriptions as $puser) {
            $this->info("Looking for UUID -> " . $puser->id);
            $local_user_susbcriptions = UserSubscription::where('uuid', $puser->id)->first();
            $sub = Subscription::where('uuid', $puser->subscription_id)->first();
            if ($local_user_susbcriptions == null) {

                $user_subscription = array(
                    'uuid' => $puser->id,
                    'subscription_uuid' => $puser->subscription_id,
                    'subscription_start_date' => date('Y-m-d H:i:s', strtotime($puser->subscription_start_date)),
                    'subscription_end_date' => date('Y-m-d H:i:s', strtotime($puser->subscription_end_date)),
                    'vpn_pass' => $puser->vpn_pass,
                    'is_active' => $puser->is_active,
                    'subscription_id' => $sub->id,
                    'user_id' => 1,
                );

                UserSubscription::create($user_subscription);
                $this->info("User Subscription  " . $puser->id . " Created.");


            } else {

                $this->info("User Subscription  " . $puser->id . " Found...");

                if (
                    ($local_user_susbcriptions->uuid == $puser->id)&&
                    ($local_user_susbcriptions->subscription_uuid == $puser->subscription_id)&&
                    ($local_user_susbcriptions->subscription_start_date == date('Y-m-d H:i:s', strtotime($puser->subscription_start_date)))&&
                    ($local_user_susbcriptions->subscription_end_date == date('Y-m-d H:i:s', strtotime($puser->subscription_end_date)))&&
                    ($local_user_susbcriptions->vpn_pass == $puser->vpn_pass)&&
                    ($local_user_susbcriptions->is_active == $puser->is_active)&&
                    ($local_user_susbcriptions->subscription_id == $sub->id)
                ){
                    $this->info("User Subscription  " . $puser->id . " Matched...");

                }else{

                    $local_user_susbcriptions->uuid = $puser->id;
                    $local_user_susbcriptions->subscription_uuid = $puser->subscription_id;
                    $local_user_susbcriptions->subscription_start_date = date('Y-m-d H:i:s', strtotime($puser->subscription_start_date));
                    $local_user_susbcriptions->subscription_end_date = date('Y-m-d H:i:s', strtotime($puser->subscription_end_date));
                    $local_user_susbcriptions->vpn_pass = $puser->vpn_pass;
                    $local_user_susbcriptions->is_active = $puser->is_active;
                    $local_user_susbcriptions->subscription_id = $sub->id;
                    $local_user_susbcriptions->save();
                    $this->info("User Subscription  " . $puser->id . " Updated...");


                }

            }

            $count = $count + 1;
            $this->info("Counting Processed Users " . $count . "");

        }
    }

}
