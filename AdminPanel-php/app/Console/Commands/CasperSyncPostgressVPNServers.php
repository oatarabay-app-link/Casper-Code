<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\VPNServer;

class CasperSyncPostgressVPNServers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:vpn_servers';

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
                      $sql= "select * from vpn_servers";
                $vpn_servers =\DB::connection("vpn_servers")->select($sql);
                foreach ($vpn_servers as $vpn_server) {
                    $this->info("Looking for UUID -> ". $vpn_server->id);
                    $local_vpn_servers = VPNServer::where('uuid',$vpn_server->id)->first();
                    if ($local_vpn_servers == null) {
                        $this->info("Not Found UUID " . $vpn_server->id . " Creating....");
                        $vpn = array(
                            'service_id' => 1,
                            'create_date' => $vpn_server->create_date,
                            'is_deleted' => $vpn_server->is_deleted,
                            'is_disabled' => $vpn_server->is_disabled,
                            'uuid' => $vpn_server->id,
                            'ip' => $vpn_server->ip,
                            'latitude' => $vpn_server->latitude,
                            'longitude' => $vpn_server->longitude,
                            'name' => $vpn_server->name,
                            'country' => $vpn_server->country,
                            'parameters' => $vpn_server->parameters,

                        );

                        VPNServer::create($vpn);
                    } else{

                        $this->info("Found UUID " . $vpn_server->id . " Updating....");
                        $vpn = array(
                            'service_id' => 1,
                            'create_date' => $vpn_server->create_date,
                            'is_deleted' => $vpn_server->is_deleted,
                            'is_disabled' => $vpn_server->is_disabled,
                            'uuid' => $vpn_server->id,
                            'ip' => $vpn_server->ip,
                            'latitude' => $vpn_server->latitude,
                            'longitude' => $vpn_server->longitude,
                            'name' => $vpn_server->name,
                            'country' => $vpn_server->country,
                            'parameters' => $vpn_server->parameters,

                        );



                    }

        }
    }
}
