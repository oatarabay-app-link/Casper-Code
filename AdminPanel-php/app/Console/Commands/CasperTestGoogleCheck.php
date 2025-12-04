<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Payment;
use App\Payments_Check;
use App\Subscription;
use App\UserSubscription;
use Illuminate\Console\Command;
use App\PaymentInfo;

class CasperTestGoogleCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperTest:GoogleToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A Test Google to Test For Token is it is working for us';

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
        $client = new \Google_Client();
        $service_account_config = ("D:\\Work\\CasperVPN\\AdminPanel\\admin_panel\\api-5954894522699629267-424023-8598f41da41b.json");
        $client->setAuthConfig($service_account_config);
        $client->setApplicationName("Google Play Android Developer");
        $client->addScope("https://www.googleapis.com/auth/androidpublisher");
        $service = new \Google_Service_AndroidPublisher($client);


        $payment_infos = new PaymentInfo();
        $payment_infos = PaymentInfo::where("pay_method", "android")
            ->whereNotNull("corrrected_product_description")
            ->take(10)
            ->get();

        foreach ($payment_infos as $info) {

            if ($info->google_subscription_token){

                echo "\n" . $info->google_subscription_token;

            }
        }


//        try{
//            $purchase = $service->purchases_subscriptions->get("caspervpn.com","caspervpn.1month.subscription","jbkcbbnkoflljcnokfanohpe.AO-J1Ozgb5l-UNpDDNpY9CEf5hD7Fab4unCazab56Hgeas9eTbiZN0XdEKaGfXjuOEmQxH9Gr7LFtOi2i3NCrdcGfLbKJ9TVpMcfdM-eHWqY8DM7sonLbCqgHomp-FRCgv48wf5cTuO3");
//            //dd($purchase);
//        }catch (\Exception $ex){
//            $verify_result = \GuzzleHttp\json_encode($ex);
//            echo $verify_result;
//            echo  "Not  Found Token";
//        }


        //dd($purchase);


    }

}
