<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use GuzzleHttp;
use App\SMTP2GOEmailDatum;
use Illuminate\Console\Command;

class CasperSyncSMTP2Go extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caspersync:SMTP2GO';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SMTP2GO Emails From SMTP2Go to Database';

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
        $client = new GuzzleHttp\Client();
        $start_date = Date('Y-m-d', strtotime("-32 days"));

        //echo $start_date;
        ///exit();
        $options = array(
            "api_key" => "api-A0AE0ABCC65111E981BEF23C91C88F4E",
            "start_date" => $start_date,
            "filter_query" => "sender:support@caspervpn.com"
        );
        $options=GuzzleHttp\json_encode($options);

        //$client->request("POST","https://api.smtp2go.com/v3/email/search",$options);
        //$response = $client->post("https://api.smtp2go.com/v3/email/search",$options);
        //dd($options);
        $response =($client->post("https://api.smtp2go.com/v3/email/search",array("body"=>$options)));
        if ($response->getStatusCode()==200){

            $data = json_decode($response->getBody());
            //var_dump($data);
            //var_dump($data->data->count);
            echo "\n" . "Total Results : " . $data->data->count;
            $count=0;
            foreach ($data->data->emails  as $email){

                $SMTP2GOData = new SMTP2GOEmailDatum();
                $SMTP2GOData = $SMTP2GOData::where('email_id',"=",$email->email_id)
                                            ->where('subject',"=",$email->subject)
                                            ->where('delivered_at',"=",$email->delivered_at)
                                            ->first();
                if ($SMTP2GOData==null){
                    $SMTP2GOData = new SMTP2GOEmailDatum();
                    $SMTP2GOData->subject = $email->subject;
                    $SMTP2GOData->email_id = $email->email_id;
                    $SMTP2GOData->delivered_at = $email->delivered_at;
                    $SMTP2GOData->process_status = $email->process_status;
                    $SMTP2GOData->status = $email->status;
                    $SMTP2GOData->response = $email->response;
                    $SMTP2GOData->email_tx = $email->email_ts;
                    $SMTP2GOData->host = $email->host;
                    $SMTP2GOData->smtpcode = $email->smtpcode;
                    $SMTP2GOData->sender = $email->sender;
                    $SMTP2GOData->recipient = $email->recipient;
                    $SMTP2GOData->stmp2gousername = $email->username;
                    $SMTP2GOData->opens = GuzzleHttp\json_encode($email->opens);
                    $SMTP2GOData->save();
                    echo "\n" . "Added New Entry for the Email $count";

                }else{

                    $SMTP2GOData->subject = $email->subject;
                    $SMTP2GOData->email_id = $email->email_id;
                    $SMTP2GOData->delivered_at = $email->delivered_at;
                    $SMTP2GOData->process_status = $email->process_status;
                    $SMTP2GOData->status = $email->status;
                    $SMTP2GOData->response = $email->response;
                    $SMTP2GOData->email_tx = $email->email_ts;
                    $SMTP2GOData->host = $email->host;
                    $SMTP2GOData->smtpcode = $email->smtpcode;
                    $SMTP2GOData->sender = $email->sender;
                    $SMTP2GOData->recipient = $email->recipient;
                    $SMTP2GOData->stmp2gousername = $email->username;
                    //$SMTP2GOData->headers = $email->headers;
                    $SMTP2GOData->total_opens = $email->total_opens;
                    $SMTP2GOData->opens = GuzzleHttp\json_encode($email->opens);

                    $SMTP2GOData->save();

                    echo "\n" . "Updated Entry for the Email $count";

                }
                $count=$count+1;

            }

        }







    }

}
