<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('uuid')->nullable();
            $table->string('subscription_uuid')->nullable();
            $table->integer('subscription_id')->unsigned();
            $table->integer('payments_table_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('check_code_id')->unsigned();
            $table->integer('period_in_months')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('status')->nullable();
            $table->float('payment_sum')->nullable();
            $table->longText('details')->nullable();
            $table->longText('check_code')->nullable();
            $table->string('sid')->nullable();
            $table->string('key')->nullable();
            $table->string('demo')->nullable();
            $table->string('total')->nullable();
            $table->string('quantity')->nullable();
            $table->string('fixed')->nullable();
            $table->string('submit')->nullable();
            $table->string('email')->nullable();
            $table->longText('expiryDateFormat')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone')->nullable();
            $table->string('lang')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('isautorenew')->nullable();
            $table->string('originalTransactionId')->nullable();
            $table->string('notificationType')->nullable();
            $table->longText('appleAPI')->nullable();
            $table->longText('custom_check_code_uuid')->nullable();
            $table->string('custom_is_landing')->nullable();
            $table->string('order_number')->nullable();
            $table->string('product_description')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('credit_card_processed')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('cart_tangible')->nullable();
            $table->string('merchant_product_id')->nullable();
            $table->string('merchant_order_id')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('middle_initial')->nullable();
            $table->string('cart_weight')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street_address')->nullable();
            $table->string('street_address2')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('ip_country')->nullable();
            $table->string('environment')->nullable();
            $table->string('adam_id')->nullable();
            $table->string('app_item_id')->nullable();
            $table->string('application_version')->nullable();
            $table->string('bundle_id')->nullable();
            $table->string('download_id')->nullable();
            $table->string('in_app')->nullable();
            $table->string('original_application_version')->nullable();
            $table->string('original_purchase_date')->nullable();
            $table->string('original_purchase_date_ms')->nullable();
            $table->string('original_purchase_date_pst')->nullable();
            $table->string('receipt_creation_date')->nullable();
            $table->string('receipt_creation_date_ms')->nullable();
            $table->string('receipt_creation_date_pst')->nullable();
            $table->string('receipt_type')->nullable();
            $table->string('request_date')->nullable();
            $table->string('request_date_ms')->nullable();
            $table->string('request_date_pst')->nullable();
            $table->string('version_external_identifier')->nullable();
            $table->longText('receipt_data_base64')->nullable();
            $table->longText('receipt_data')->nullable();
            $table->string('isrecurring')->nullable();
            $table->string('app_version')->nullable();
            $table->string('google_subscription_token')->nullable();
            $table->string('apple_check')->nullable();
            $table->longText('raw_json')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('check_code_id')->references('id')->on('payments__checks');
            $table->foreign('user_id')->references('id')->on('users');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payment_infos');
    }
}
