<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Protocol;


class CasperSyncPostgressProtocols extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:protocols';

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
        $sql= "select * from protocols";
        $protocols =\DB::connection("vpn_servers")->select($sql);
        foreach ($protocols as $protocol) {
            $this->info("Looking for UUID -> ". $protocol->id);
            $local_protocols = Protocol::where('uuid',$protocol->id)->first();
            if ($local_protocols == null) {
                $this->info("Not Found UUID " . $protocol->id . " Creating....");
                $prot = array(
                    'uuid' => $protocol->id,
                    'name' => $protocol->title,
                );
                Protocol::create($prot);
            } else{
                $this->info("Found UUID " . $protocol->id . " Updating....");
                $prot = array(
                    'uuid' => $protocol->id,
                    'name' => $protocol->title,
                );
                Protocol::update($prot);


            }

        }
    }
}
