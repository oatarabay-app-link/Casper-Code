<?php

namespace App\Console\Commands;

use App\Protocol;
use App\VPNServer;
use App\VPNServerProtocol;
use Illuminate\Console\Command;

class CasperSyncPostgressVPNServerProtocols extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:vpnserver_protocols';

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
        $sql= "select * from vpnservers_to_protocols";
        $vpn_servers =\DB::connection("vpn_servers")->select($sql);
        foreach ($vpn_servers as $vpn_server) {
            $this->info("Looking for UUID -> ". $vpn_server->vpnserver_id);
            $server   = VPNServer::where('uuid',$vpn_server->vpnserver_id)->first();
            $protocol = Protocol::where('uuid',$vpn_server->protocol_id)->first();

            $local_vpn_servers = VPNServerProtocol::where('vpnserver_uuid',$vpn_server->vpnserver_id)->first();

            if ($local_vpn_servers == null) {
                $this->info("Not Found UUID " . $vpn_server->vpnserver_id . " Creating....");
                $vpn = array(
                    'vpnserver_uuid' => $vpn_server->vpnserver_id,
                    'protocol_uuid' => $vpn_server->protocol_id,
                    'vpnserver_id' =>$server->id,
                    'protocol_id' =>$protocol->id,
                );

                VPNServerProtocol::create($vpn);
            } else{

                $this->info("Found UUID " . $vpn_server->vpnserver_id . " Updating....");
                $vpn = array(
                    'vpnserver_uuid' => $vpn_server->vpnserver_id,
                    'protocol_uuid' => $vpn_server->protocol_id,
                    'vpnserver_id' =>$server->id,
                    'protocol_id' =>$protocol->id,
                );
                VPNServerProtocol::update($vpn);



            }

        }
    }
}
