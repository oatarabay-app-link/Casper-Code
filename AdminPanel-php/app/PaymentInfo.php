<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_infos';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['uuid', 'subscription_uuid', 'subscription_id', 'payments_table_id', 'user_id', 'check_code_id', 'period_in_months', 'payment_id', 'status', 'payment_sum', 'details', 'check_code', 'sid', 'key', 'demo', 'total', 'quantity', 'fixed', 'submit', 'email', 'expiryDateFormat', 'country', 'city', 'state', 'zip', 'phone', 'lang', 'currency_code', 'isautorenew', 'originalTransactionId', 'notificationType', 'appleAPI', 'custom_check_code_uuid', 'custom_is_landing', 'order_number', 'product_description', 'invoice_id', 'product_id', 'credit_card_processed', 'pay_method', 'cart_tangible', 'merchant_product_id', 'merchant_order_id', 'card_holder_name', 'middle_initial', 'cart_weight', 'first_name', 'last_name', 'street_address', 'street_address2', 'expiry_date', 'ip_country', 'environment', 'adam_id', 'app_item_id', 'application_version', 'bundle_id', 'download_id', 'in_app', 'original_application_version', 'original_purchase_date', 'original_purchase_date_ms', 'original_purchase_date_pst', 'receipt_creation_date', 'receipt_creation_date_ms', 'receipt_creation_date_pst', 'receipt_type', 'request_date', 'request_date_ms', 'request_date_pst', 'version_external_identifier', 'receipt_data_base64', 'receipt_data', 'isrecurring', 'app_version', 'google_subscription_token', 'apple_check', 'raw_json','corrrected_product_description','verify_result','verified'];

    public function subscriptions()
    {
        return $this->belongsTo('App\Subscription');
    }
    public function users()
    {
        return $this->belongsTo('App\Models\Auth\User');
    }
    
}
