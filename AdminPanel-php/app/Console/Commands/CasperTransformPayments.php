<?php

namespace App\Console\Commands;

use App\Payment;
use App\PaymentInfo;
use Illuminate\Console\Command;

class CasperTransformPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CasperTransform:Payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transform Payments from Payments Checks and Payments into Payment Info';

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
        $payments = Payment::get();
        //$payments = Payment::all();
        $count = count($payments);
        $this->info('Total Records  '.$count.' Processing....');
        foreach ($payments as $payment) {
            $details = \GuzzleHttp\json_decode($payment->details);
            // dd($details);
            $PmtInfo = PaymentInfo::where('payments_table_id', '=', $payment->id)->first();

            if ($PmtInfo == null) {
                $this->info('Payment Information Not found for Payment Table ID  '.$payment->id.' Creating New....');
                $pinfo = new PaymentInfo();
                $pinfo->uuid = $payment->uuid;
                $pinfo->subscription_uuid = $payment->subscription_uuid;
                $pinfo->subscription_id = $payment->subscription_id;
                $pinfo->payments_table_id = $payment->id;
                $pinfo->user_id = isset($payment->user_id) ? $payment->user_id : 0;
                $pinfo->check_code_id = 0; //  Set to Zero Some time
                $pinfo->period_in_months = $payment->period_in_months;
                $pinfo->payment_id = $payment->payment_id;
                $pinfo->status = $payment->status;
                $pinfo->payment_sum = $payment->payment_sum;
                $pinfo->details = $payment->details;
                $pinfo->check_code = $payment->check_code;
                $pinfo->sid = isset($details->payment->sid) ? $details->payment->sid : null;
                $pinfo->key = isset($details->payment->key) ? $details->payment->key : null;
                $pinfo->demo = isset($details->payment->demo) ? $details->payment->demo : null;
                $pinfo->total = isset($details->payment->total) ? $details->payment->total : null;
                $pinfo->quantity = isset($details->payment->quantity) ? $details->payment->quantity : null;
                $pinfo->fixed = isset($details->payment->fixed) ? $details->payment->fixed : null;
                $pinfo->submit = isset($details->payment->submit) ? $details->payment->submit : null;
                $pinfo->email = isset($details->payment->email) ? $details->payment->email : null;
                $pinfo->expiryDateFormat = isset($details->payment->expiryDateFormat) ? $details->payment->expiryDateFormat : null;
                $pinfo->country = isset($details->payment->country) ? $details->payment->country : null;
                $pinfo->city = isset($details->payment->city) ? $details->payment->city : null;
                $pinfo->state = isset($details->payment->state) ? $details->payment->state : null;
                $pinfo->zip = isset($details->payment->zip) ? $details->payment->zip : null;
                $pinfo->phone = isset($details->payment->phone) ? $details->payment->phone : null;
                $pinfo->lang = isset($details->payment->lang) ? $details->payment->lang : null;
                $pinfo->currency_code = isset($details->payment->currency_code) ? $details->payment->currency_code : null;
                $pinfo->isautorenew = isset($details->payment->isautorenew) ? $details->payment->isautorenew : null;
                $pinfo->originalTransactionId = isset($details->payment->originalTransactionId) ? $details->payment->originalTransactionId : null;
                $pinfo->notificationType = isset($details->payment->notificationType) ? $details->payment->notificationType : null;
                $pinfo->appleAPI = isset($details->payment->appleAPI) ? $details->payment->appleAPI : null;
                $pinfo->custom_check_code_uuid = isset($payment->check_code) ? $payment->check_code : null;
                $pinfo->custom_is_landing = isset($details->payment->custom_is_landing) ? $details->payment->custom_is_landing : null;
                $pinfo->order_number = isset($details->payment->order_number) ? $details->payment->order_number : null;
                $pinfo->product_description = isset($details->payment->product_description) ? $details->payment->product_description : null;
                $pinfo->invoice_id = isset($details->payment->invoice_id) ? $details->payment->invoice_id : null;
                $pinfo->product_id = isset($details->payment->product_id) ? $details->payment->product_id : null;
                $pinfo->credit_card_processed = isset($details->payment->credit_card_processed) ? $details->payment->credit_card_processed : null;
                $pinfo->pay_method = isset($details->payment->pay_method) ? $details->payment->pay_method : null;
                $pinfo->cart_tangible = isset($details->payment->cart_tangible) ? $details->payment->cart_tangible : null;
                $pinfo->merchant_product_id = isset($details->payment->merchant_product_id) ? $details->payment->merchant_product_id : null;
                $pinfo->merchant_order_id = isset($details->payment->merchant_order_id) ? $details->payment->merchant_order_id : null;
                $pinfo->card_holder_name = isset($details->payment->card_holder_name) ? $details->payment->card_holder_name : null;
                $pinfo->middle_initial = isset($details->payment->middle_initial) ? $details->payment->middle_initial : null;
                $pinfo->cart_weight = isset($details->payment->cart_weight) ? $details->payment->cart_weight : null;
                $pinfo->first_name = isset($details->payment->first_name) ? $details->payment->first_name : null;
                $pinfo->last_name = isset($details->payment->last_name) ? $details->payment->last_name : null;
                $pinfo->street_address = isset($details->payment->street_address) ? $details->payment->street_address : null;
                $pinfo->street_address2 = isset($details->payment->street_address2) ? $details->payment->street_address2 : null;
                $pinfo->expiry_date = isset($details->payment->expiry_date) ? $details->payment->expiry_date : null;
                $pinfo->ip_country = isset($details->payment->ip_country) ? $details->payment->ip_country : null;
                $pinfo->environment = isset($details->payment->environment) ? $details->payment->environment : null;
                $pinfo->adam_id = isset($details->payment->adam_id) ? $details->payment->adam_id : null;
                $pinfo->app_item_id = isset($details->payment->app_item_id) ? $details->payment->app_item_id : null;
                $pinfo->application_version = isset($details->payment->application_version) ? $details->payment->application_version : null;
                $pinfo->bundle_id = isset($details->payment->bundle_id) ? $details->payment->bundle_id : null;
                $pinfo->download_id = isset($details->payment->download_id) ? $details->payment->download_id : null;
                $pinfo->in_app = isset($details->payment->in_app) ? $details->payment->in_app : null;
                $pinfo->original_application_version = isset($details->payment->original_application_version) ? $details->payment->original_application_version : null;
                $pinfo->original_purchase_date = isset($details->payment->original_purchase_date) ? $details->payment->original_purchase_date : null;
                $pinfo->original_purchase_date_ms = isset($details->payment->original_purchase_date_ms) ? $details->payment->original_purchase_date_ms : null;
                $pinfo->original_purchase_date_pst = isset($details->payment->original_purchase_date_pst) ? $details->payment->original_purchase_date_pst : null;
                $pinfo->receipt_creation_date = isset($details->payment->receipt_creation_date) ? $details->payment->receipt_creation_date : null;
                $pinfo->receipt_creation_date_ms = isset($details->payment->receipt_creation_date_ms) ? $details->payment->receipt_creation_date_ms : null;
                $pinfo->receipt_creation_date_pst = isset($details->payment->receipt_creation_date_pst) ? $details->payment->receipt_creation_date_pst : null;
                $pinfo->receipt_type = isset($details->payment->receipt_type) ? $details->payment->receipt_type : null;
                $pinfo->request_date = isset($details->payment->request_date) ? $details->payment->request_date : null;
                $pinfo->request_date_ms = isset($details->payment->request_date_ms) ? $details->payment->request_date_ms : null;
                $pinfo->request_date_pst = isset($details->payment->request_date_pst) ? $details->payment->request_date_pst : null;
                $pinfo->version_external_identifier = isset($details->payment->version_external_identifier) ? $details->payment->version_external_identifier : null;
                $pinfo->receipt_data_base64 = isset($details->payment->{'receipt-data-base64'}) ? $details->payment->{'receipt-data-base64'} : null;
                $pinfo->receipt_data = isset($details->payment->{'receipt-data'}) ? $details->payment->{'receipt-data'} : null;
                $pinfo->isrecurring = isset($details->payment->isrecurring) ? $details->payment->isrecurring : null;
                $pinfo->app_version = isset($details->payment->app_version) ? $details->payment->app_version : null;
                $pinfo->google_subscription_token = isset($details->payment->google_subscription_token) ? $details->payment->google_subscription_token : null;
                $pinfo->apple_check = isset($details->payment->apple_check) ? $details->payment->apple_check : null;
                $pinfo->raw_json = $payment->details;
                $pinfo->save();
            } else {
                $this->info('Payment Information found for Payment Table ID  '.$payment->id.' Updating....');
                $pinfo = $PmtInfo;
                $pinfo->uuid = $payment->uuid;
                $pinfo->subscription_uuid = $payment->subscription_uuid;
                $pinfo->subscription_id = $payment->subscription_id;
                $pinfo->payments_table_id = $payment->id;
                $pinfo->user_id = isset($payment->user_id) ? $payment->user_id : 0;
                $pinfo->check_code_id = 0; //  Set to Zero Some time
                $pinfo->period_in_months = $payment->period_in_months;
                $pinfo->payment_id = $payment->payment_id;
                $pinfo->status = $payment->status;
                $pinfo->payment_sum = $payment->payment_sum;
                $pinfo->details = $payment->details;
                $pinfo->check_code = $payment->check_code;
                $pinfo->sid = isset($details->payment->sid) ? $details->payment->sid : null;
                $pinfo->key = isset($details->payment->key) ? $details->payment->key : null;
                $pinfo->demo = isset($details->payment->demo) ? $details->payment->demo : null;
                $pinfo->total = isset($details->payment->total) ? $details->payment->total : null;
                $pinfo->quantity = isset($details->payment->quantity) ? $details->payment->quantity : null;
                $pinfo->fixed = isset($details->payment->fixed) ? $details->payment->fixed : null;
                $pinfo->submit = isset($details->payment->submit) ? $details->payment->submit : null;
                $pinfo->email = isset($details->payment->email) ? $details->payment->email : null;
                $pinfo->expiryDateFormat = isset($details->payment->expiryDateFormat) ? $details->payment->expiryDateFormat : null;
                $pinfo->country = isset($details->payment->country) ? $details->payment->country : null;
                $pinfo->city = isset($details->payment->city) ? $details->payment->city : null;
                $pinfo->state = isset($details->payment->state) ? $details->payment->state : null;
                $pinfo->zip = isset($details->payment->zip) ? $details->payment->zip : null;
                $pinfo->phone = isset($details->payment->phone) ? $details->payment->phone : null;
                $pinfo->lang = isset($details->payment->lang) ? $details->payment->lang : null;
                $pinfo->currency_code = isset($details->payment->currency_code) ? $details->payment->currency_code : null;
                $pinfo->isautorenew = isset($details->payment->isautorenew) ? $details->payment->isautorenew : null;
                $pinfo->originalTransactionId = isset($details->payment->originalTransactionId) ? $details->payment->originalTransactionId : null;
                $pinfo->notificationType = isset($details->payment->notificationType) ? $details->payment->notificationType : null;
                $pinfo->appleAPI = isset($details->payment->appleAPI) ? $details->payment->appleAPI : null;
                $pinfo->custom_check_code_uuid = isset($payment->check_code) ? $payment->check_code : null;
                $pinfo->custom_is_landing = isset($details->payment->custom_is_landing) ? $details->payment->custom_is_landing : null;
                $pinfo->order_number = isset($details->payment->order_number) ? $details->payment->order_number : null;
                $pinfo->product_description = isset($details->payment->product_description) ? $details->payment->product_description : null;
                $pinfo->invoice_id = isset($details->payment->invoice_id) ? $details->payment->invoice_id : null;
                $pinfo->product_id = isset($details->payment->product_id) ? $details->payment->product_id : null;
                $pinfo->credit_card_processed = isset($details->payment->credit_card_processed) ? $details->payment->credit_card_processed : null;
                $pinfo->pay_method = isset($details->payment->pay_method) ? $details->payment->pay_method : null;
                $pinfo->cart_tangible = isset($details->payment->cart_tangible) ? $details->payment->cart_tangible : null;
                $pinfo->merchant_product_id = isset($details->payment->merchant_product_id) ? $details->payment->merchant_product_id : null;
                $pinfo->merchant_order_id = isset($details->payment->merchant_order_id) ? $details->payment->merchant_order_id : null;
                $pinfo->card_holder_name = isset($details->payment->card_holder_name) ? $details->payment->card_holder_name : null;
                $pinfo->middle_initial = isset($details->payment->middle_initial) ? $details->payment->middle_initial : null;
                $pinfo->cart_weight = isset($details->payment->cart_weight) ? $details->payment->cart_weight : null;
                $pinfo->first_name = isset($details->payment->first_name) ? $details->payment->first_name : null;
                $pinfo->last_name = isset($details->payment->last_name) ? $details->payment->last_name : null;
                $pinfo->street_address = isset($details->payment->street_address) ? $details->payment->street_address : null;
                $pinfo->street_address2 = isset($details->payment->street_address2) ? $details->payment->street_address2 : null;
                $pinfo->expiry_date = isset($details->payment->expiry_date) ? $details->payment->expiry_date : null;
                $pinfo->ip_country = isset($details->payment->ip_country) ? $details->payment->ip_country : null;
                $pinfo->environment = isset($details->payment->environment) ? $details->payment->environment : null;
                $pinfo->adam_id = isset($details->payment->adam_id) ? $details->payment->adam_id : null;
                $pinfo->app_item_id = isset($details->payment->app_item_id) ? $details->payment->app_item_id : null;
                $pinfo->application_version = isset($details->payment->application_version) ? $details->payment->application_version : null;
                $pinfo->bundle_id = isset($details->payment->bundle_id) ? $details->payment->bundle_id : null;
                $pinfo->download_id = isset($details->payment->download_id) ? $details->payment->download_id : null;
                $pinfo->in_app = isset($details->payment->in_app) ? $details->payment->in_app : null;
                $pinfo->original_application_version = isset($details->payment->original_application_version) ? $details->payment->original_application_version : null;
                $pinfo->original_purchase_date = isset($details->payment->original_purchase_date) ? $details->payment->original_purchase_date : null;
                $pinfo->original_purchase_date_ms = isset($details->payment->original_purchase_date_ms) ? $details->payment->original_purchase_date_ms : null;
                $pinfo->original_purchase_date_pst = isset($details->payment->original_purchase_date_pst) ? $details->payment->original_purchase_date_pst : null;
                $pinfo->receipt_creation_date = isset($details->payment->receipt_creation_date) ? $details->payment->receipt_creation_date : null;
                $pinfo->receipt_creation_date_ms = isset($details->payment->receipt_creation_date_ms) ? $details->payment->receipt_creation_date_ms : null;
                $pinfo->receipt_creation_date_pst = isset($details->payment->receipt_creation_date_pst) ? $details->payment->receipt_creation_date_pst : null;
                $pinfo->receipt_type = isset($details->payment->receipt_type) ? $details->payment->receipt_type : null;
                $pinfo->request_date = isset($details->payment->request_date) ? $details->payment->request_date : null;
                $pinfo->request_date_ms = isset($details->payment->request_date_ms) ? $details->payment->request_date_ms : null;
                $pinfo->request_date_pst = isset($details->payment->request_date_pst) ? $details->payment->request_date_pst : null;
                $pinfo->version_external_identifier = isset($details->payment->version_external_identifier) ? $details->payment->version_external_identifier : null;
                $pinfo->receipt_data_base64 = isset($details->payment->{'receipt-data-base64'}) ? $details->payment->{'receipt-data-base64'} : null;
                $pinfo->receipt_data = isset($details->payment->{'receipt-data'}) ? $details->payment->{'receipt-data'} : null;
                $pinfo->isrecurring = isset($details->payment->isrecurring) ? $details->payment->isrecurring : null;
                $pinfo->app_version = isset($details->payment->app_version) ? $details->payment->app_version : null;
                $pinfo->google_subscription_token = isset($details->payment->google_subscription_token) ? $details->payment->google_subscription_token : null;
                $pinfo->apple_check = isset($details->payment->apple_check) ? $details->payment->apple_check : null;
                $pinfo->raw_json = $payment->details;
            }

            $this->info('Payment Information For Payment Table  '.$payment->id.' Saved');
            $count = $count - 1;
            $this->info('Count  '.$count.' Next....');
        }
    }
}
