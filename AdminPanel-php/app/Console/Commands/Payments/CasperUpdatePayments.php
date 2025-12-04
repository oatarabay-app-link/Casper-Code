<?php

namespace App\Console\Commands\Payments;

use App\Payment;
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

        //Update Payment Check Code and User ID
        $payments = Payment::whereNull("user_id")->get();
        echo count($payments);
        foreach ($payments as $payment) {

        }
    }
}
