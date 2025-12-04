<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Payment;
use App\Payments_Check;
use App\Subscription;
use App\UserSubscription;
use Illuminate\Console\Command;
use App\Repositories\Backend\Auth\UserRepository;

class CasperTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperTest:User';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A Test Google to Test For Token is it is working for us';
    protected $userRepository;


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

        $user = User::where("id",34609)->first();
        if ($user) echo "User Found";
        $user->getExpiryDate();









    }

}
