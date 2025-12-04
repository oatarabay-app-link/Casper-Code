<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Payment;
use App\Payments_Check;
use App\Subscription;
use App\UserSubscription;
use Illuminate\Console\Command;

class CasperSyncPostgressPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:Payments';

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
        $sql = "select  * from payments order by random()";
        $postgree_payments = \DB::connection("payments")->select($sql);
        $this->info("Counting Payment Information " . count($postgree_payments) . " Loading....");
        $count = 1;
        foreach ($postgree_payments as $p) {
            $this->info("Loading Payment Information " . $p->id . " Loading....");
            $this->info("Looking for UUID -> " . $p->id);
            $payments = Payment::where('uuid', $p->id)->first(); // Check if the payment exists alerady
            $subs = Subscription::where('uuid', $p->subscription_id)->first();
            if ($payments == null) {
                $this->info("Not Found UUID " . $p->id . " Creating....");
                $pmt = array(
                    'uuid' => $p->id,
                    'subscription_uuid' => $p->subscription_id,
                    'subscription_id' => $subs->id,
                    'period_in_months' => $p->period_in_months,
                    'payment_id' => $p->payment_id,
                    'status' => $p->status,
                    'payment_sum' => $p->payment_sum,
                    'details' => $p->details,
                    'check_code' => $p->check_code,
                    'create_date' => date('Y-m-d H:i:s', strtotime($p->create_date)),
                );
                $pmt = Payment::create($pmt);
                $this->info("Payment  " . $p->id . " Created.");
            } else {

                $this->info("Found UUID " . $p->id . " Checking Records ....");
                $update = false;
                if (
                    ($payments->uuid == $p->id) &&
                    ($payments->subscription_uuid == $p->subscription_id) &&
                    ($payments->subscription_id == $subs->id) &&
                    ($payments->period_in_months == $p->period_in_months) &&
                    ($payments->payment_id == $p->payment_id) &&
                    ($payments->status == $p->status) &&
                    ($payments->payment_sum == $p->payment_sum) &&
                    ($payments->details == $p->details) &&
                    ($payments->check_code == $p->check_code)
                ) {
                    $this->info("UUID " . $p->id . " Record Matched ....");
                }else{

                    $payments->uuid == $p->id;
                    $payments->subscription_uuid == $p->subscription_id;
                    $payments->subscription_id == $subs->id;
                    $payments->period_in_months == $p->period_in_months;
                    $payments->payment_id == $p->payment_id;
                    $payments->status == $p->status;
                    $payments->payment_sum == $p->payment_sum;
                    $payments->details == $p->details;
                    $payments->check_code == $p->check_code;

                    $this->info("UUID " . $p->id . " Record Updated ....");
                    $payments->save();
                }
            }
            $count = $count + 1;
            $this->info("Counting Payment Information " . $count . "");
        }

        $sql = "select  * from payment_checks order by random()";
        $postgree_payments = \DB::connection("payments")->select($sql);
        $this->info("Counting Payment Information " . count($postgree_payments) . " Loading....");
        $count = 1;
        foreach ($postgree_payments as $p) {
            $this->info("Loading Payment Check  Information " . $p->id . " Loading....");
            $this->info("Looking for UUID -> " . $p->id);
            $payment_check = Payments_Check::where('uuid', $p->id)->first();
            if ($payment_check == null) {
                $this->info("Not Found UUID " . $p->id . " Creating....");
                //$subs = Subscription::where('uuid',$p->subscription_id)->first();
                //$usr = User::where('user_uuid',$p->user_id)->first();
                $pmt = array(
                    'uuid' => $p->id,
                    'subscription_uuid' => $p->subscription_id,
                    'user_uuid' => $p->user_id,
                    'user_email' => $p->user_email,
                    'token' => $p->token,
                    'status' => $p->status,
                    //'user_id'              =>$usr->id,
                    //'subscription_id'      =>$subs->id,
                    'create_date' => date('Y-m-d H:i:s', strtotime($p->create_date)),
                );
                $pmt = Payments_Check::create($pmt);
                $this->info("Payment Check " . $p->id . " Created.");
            } else {

                if (

                    ($payment_check->uuid == $p->id ) &&
                    ($payment_check->subscription_uuid == $p->subscription_id) &&
                    ($payment_check->user_uuid == $p->user_id) &&
                    ($payment_check->user_email == $p->user_email) &&
                    ($payment_check->token == $p->token) &&
                    ($payment_check->status == $p->status)
                ){
                    $this->info("UUID " . $p->id . " Record Matched ....");
                }else{

                    $payment_check->uuid = $p->id;
                    $payment_check->subscription_uuid = $p->subscription_id;
                    $payment_check->user_uuid = $p->user_id;
                    $payment_check->user_email = $p->user_email;
                    $payment_check->token = $p->token;
                    $payment_check->status = $p->status;
                    $payment_check->save();
                    $this->info("UUID " . $p->id . " Record Updated ....");

                }

            }

            $count = $count + 1;
            $this->info("Counting Payment Information " . $count . "");
        }

    }

}
