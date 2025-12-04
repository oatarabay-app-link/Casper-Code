<?php

namespace App\Console\Commands;

use App\Payment;
use App\PaymentInfo;
use Illuminate\Console\Command;

class CasperUpdatePaymentsInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperUpdate:PaymentsInfo';

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
        //Load all Payments
        $product_description_1m = [
            '1 Month',
            '1_Month',
            '1%20Month',
            '1 Month Subscription',
            '1M',
        ];

        $product_description_6m = [
            '6_Months',
            '6 Months',
            '6M',
            '6 Month',
            '6 Month Subscription',
        ];

        $product_description_1y = [
            '1_Year',
            '1Y',
            '1 Year',
        ];

        $product_description_lifetime = [
            'Lifetime',
        ];

        // $payment_infos =  PaymentInfo::take(10)->get();
        $payment_infos = Payment::get();
        $count = 0;
        foreach ($payment_infos as $info) {
            // Update Corrected Product Description
            echo "\n ".$info->product_description.' Found Updating'." Count $count";

            if (in_array($info->product_description, $product_description_1m)) {
                $info->product_description_corrected = '1_Month';
                $info->save();
            } elseif (in_array($info->product_description, $product_description_6m)) {
                $info->product_description_corrected = '6_Months';
                $info->save();
            } elseif (in_array($info->product_description, $product_description_1y)) {
                $info->product_description_corrected = '1_Year';
                $info->save();
            } elseif (in_array($info->product_description, $product_description_lifetime)) {
                $info->product_description_corrected = 'Lifetime';
                $info->save();
            } else {
                echo "\n Not Matched for Any Skipping";
            }

            $count = $count + 1;
        }
    }
}
