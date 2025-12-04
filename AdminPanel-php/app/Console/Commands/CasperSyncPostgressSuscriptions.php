<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Subscription;
use Illuminate\Console\Command;
use App\SubscriptionProtocol;
use App\Protocol;


class CasperSyncPostgressSuscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:subscriptions';

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
        $porto_maps=array(
            'c904a873-2130-4daa-9965-2985054fe8d6' => '3fcb4d21-869b-4e79-9710-3bbbc82972b2', //PPTP
            'dc6bc7d9-45dc-418b-806f-28e7f8734498' => '40913091-972a-4f4d-8cb2-d78baea13f94', //IKEV2
            '831721a9-cec9-4d64-976d-248f02214513' => 'e3f70de0-68c6-415e-b493-9ef7cd88b329', //L2TP
            '36d1f836-0eca-4f4b-becf-30ac852a7183' => '8755b179-de6c-41b7-801d-133666f159fb', //OPENVPN
            'dd2fbab7-0808-4631-95c6-33c40f96e900' => 'd642eb16-734c-44e7-90d4-49f34eae5568', //SSTP
        );


        $sql= "select * from subscriptions";
        $subscriptions =\DB::connection("subscriptions")->select($sql);
        foreach ($subscriptions as $subs) {
            $this->info("Looking for UUID -> ". $subs->id);
            $local_subs = Subscription::where('uuid',$subs->id)->first();

            if ($local_subs == null) {
                $this->info("Not Found UUID " . $subs->id . " Creating....");
                $subs1 = array(
                    'uuid'              => $subs->id,
                    'subscription_name' => $subs->subscription_name,
                    'monthly_price'     => $subs->monthly_price,
                    'period_price'      => $subs->period_price,
                    'currency_type'     => $subs->currency_type,
                    'traffic_size'      => $subs->traffic_size,
                    'rate_limit'        => $subs->rate_limit,
                    'max_connections'   => $subs->max_connections,
                    'available_for_android' => $subs->available_for_android,
                    'available_for_ios' => $subs->available_for_ios,
                    'create_time'=> date('Y-m-d H:i:s', strtotime($subs->create_time)),
                    'is_default'=> $subs->is_default,
                    'period_length'=> $subs->period_length,
                    'order_num'=> $subs->order_num,
                    'product_id'=> $subs->product_id
                );
                Subscription::create($subs1);
                $local_subs = Subscription::where('uuid',$subs->id)->first();
                $this->info(" " . $subs->id . " Creating.... Protocols Subscriptions");
                $sql= "select * from subscription_to_protocols where subscription_id = '".$local_subs->uuid."'";
                $protos =\DB::connection("subscriptions")->select($sql);
                foreach($protos as $proto) {
                    $protocol = Protocol::where('uuid', $porto_maps[$proto->protocol_id])->first();
                    $this->info(" " . $subs->id . " Creating.... Protocols Subscriptions");
                    $subs_proto = array(
                        'subscription_uuid' => $local_subs->uuid,
                        'protocol_uuid' => $porto_maps[$proto->protocol_id],
                        'protocol_id' => $protocol->id,
                        'subscription_id' => $local_subs->id
                    );
                    SubscriptionProtocol::create($subs_proto);
                }



            }else{
                $this->info("Found Record for  UUID -> ". $subs->id. " Updating...");

                    $local_subs->uuid              = $subs->id;
                    $local_subs->subscription_name = $subs->subscription_name;
                    $local_subs->monthly_price     = $subs->monthly_price;
                    $local_subs->period_price      = $subs->period_price;
                    $local_subs->currency_type     = $subs->currency_type;
                    $local_subs->traffic_size      = $subs->traffic_size;
                    $local_subs->rate_limit        = $subs->rate_limit;
                    $local_subs->max_connections   = $subs->max_connections;
                    $local_subs->available_for_android = $subs->available_for_android;
                    $local_subs->available_for_ios = $subs->available_for_ios;
                    $local_subs->create_time= date('Y-m-d H:i:s', strtotime($subs->create_time));
                    $local_subs->is_default= $subs->is_default;
                    $local_subs->period_length= $subs->period_length;
                    $local_subs->order_num= $subs->order_num;
                    $local_subs->product_id= $subs->product_id;
                    $local_subs->update();


            }

        }






    }
}
