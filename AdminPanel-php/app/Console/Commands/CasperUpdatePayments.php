<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Payment;
use App\Payments_Check;
use App\Subscription;
use App\UserSubscription;
use Illuminate\Console\Command;

class CasperUpdatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperUpdate:Payments';

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

        //Load all Payments
        $payments = Payment::where('product_description')
          //  ->orWhere('product_description')

            ->get();
        $count = count($payments);
        //sdd($payments);
        $this->info("Total Records  " . $count . " Saving....");
        foreach ($payments as $payment){

            $details = \GuzzleHttp\json_decode($payment->details);
            //dd($details);
            if (isset($details->payment->product_description)){

              $payment->product_description =   $details->payment->product_description;
            }
            $this->info("Payment Information ID  " . $payment->id . " Saving....");
            $payment->save();
            $count = $count-1;

            $this->info("Count  " . $count . " Next....");


        }

    }

}
