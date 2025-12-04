<div class="form-group {{ $errors->has('uuid') ? 'has-error' : ''}}">
    <label for="uuid" class="control-label">{{ 'Uuid' }}</label>
    <input class="form-control" name="uuid" type="text" id="uuid" value="{{ isset($paymentinfo->uuid) ? $paymentinfo->uuid : ''}}" >
    {!! $errors->first('uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_uuid') ? 'has-error' : ''}}">
    <label for="subscription_uuid" class="control-label">{{ 'Subscription Uuid' }}</label>
    <input class="form-control" name="subscription_uuid" type="text" id="subscription_uuid" value="{{ isset($paymentinfo->subscription_uuid) ? $paymentinfo->subscription_uuid : ''}}" >
    {!! $errors->first('subscription_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('subscription_id') ? 'has-error' : ''}}">
    <label for="subscription_id" class="control-label">{{ 'Subscription Id' }}</label>
    <input class="form-control" name="subscription_id" type="number" id="subscription_id" value="{{ isset($paymentinfo->subscription_id) ? $paymentinfo->subscription_id : ''}}" >
    {!! $errors->first('subscription_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('payments_table_id') ? 'has-error' : ''}}">
    <label for="payments_table_id" class="control-label">{{ 'Payments Table Id' }}</label>
    <input class="form-control" name="payments_table_id" type="number" id="payments_table_id" value="{{ isset($paymentinfo->payments_table_id) ? $paymentinfo->payments_table_id : ''}}" >
    {!! $errors->first('payments_table_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($paymentinfo->user_id) ? $paymentinfo->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('check_code_id') ? 'has-error' : ''}}">
    <label for="check_code_id" class="control-label">{{ 'Check Code Id' }}</label>
    <input class="form-control" name="check_code_id" type="number" id="check_code_id" value="{{ isset($paymentinfo->check_code_id) ? $paymentinfo->check_code_id : ''}}" >
    {!! $errors->first('check_code_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('period_in_months') ? 'has-error' : ''}}">
    <label for="period_in_months" class="control-label">{{ 'Period In Months' }}</label>
    <input class="form-control" name="period_in_months" type="number" id="period_in_months" value="{{ isset($paymentinfo->period_in_months) ? $paymentinfo->period_in_months : ''}}" >
    {!! $errors->first('period_in_months', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('payment_id') ? 'has-error' : ''}}">
    <label for="payment_id" class="control-label">{{ 'Payment Id' }}</label>
    <input class="form-control" name="payment_id" type="text" id="payment_id" value="{{ isset($paymentinfo->payment_id) ? $paymentinfo->payment_id : ''}}" >
    {!! $errors->first('payment_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
    <label for="status" class="control-label">{{ 'Status' }}</label>
    <input class="form-control" name="status" type="text" id="status" value="{{ isset($paymentinfo->status) ? $paymentinfo->status : ''}}" >
    {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('payment_sum') ? 'has-error' : ''}}">
    <label for="payment_sum" class="control-label">{{ 'Payment Sum' }}</label>
    <input class="form-control" name="payment_sum" type="number" id="payment_sum" value="{{ isset($paymentinfo->payment_sum) ? $paymentinfo->payment_sum : ''}}" >
    {!! $errors->first('payment_sum', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('details') ? 'has-error' : ''}}">
    <label for="details" class="control-label">{{ 'Details' }}</label>
    <textarea class="form-control" rows="5" name="details" type="textarea" id="details" >{{ isset($paymentinfo->details) ? $paymentinfo->details : ''}}</textarea>
    {!! $errors->first('details', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('check_code') ? 'has-error' : ''}}">
    <label for="check_code" class="control-label">{{ 'Check Code' }}</label>
    <textarea class="form-control" rows="5" name="check_code" type="textarea" id="check_code" >{{ isset($paymentinfo->check_code) ? $paymentinfo->check_code : ''}}</textarea>
    {!! $errors->first('check_code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('sid') ? 'has-error' : ''}}">
    <label for="sid" class="control-label">{{ 'Sid' }}</label>
    <input class="form-control" name="sid" type="text" id="sid" value="{{ isset($paymentinfo->sid) ? $paymentinfo->sid : ''}}" >
    {!! $errors->first('sid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('key') ? 'has-error' : ''}}">
    <label for="key" class="control-label">{{ 'Key' }}</label>
    <input class="form-control" name="key" type="text" id="key" value="{{ isset($paymentinfo->key) ? $paymentinfo->key : ''}}" >
    {!! $errors->first('key', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('demo') ? 'has-error' : ''}}">
    <label for="demo" class="control-label">{{ 'Demo' }}</label>
    <input class="form-control" name="demo" type="text" id="demo" value="{{ isset($paymentinfo->demo) ? $paymentinfo->demo : ''}}" >
    {!! $errors->first('demo', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('total') ? 'has-error' : ''}}">
    <label for="total" class="control-label">{{ 'Total' }}</label>
    <input class="form-control" name="total" type="text" id="total" value="{{ isset($paymentinfo->total) ? $paymentinfo->total : ''}}" >
    {!! $errors->first('total', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('quantity') ? 'has-error' : ''}}">
    <label for="quantity" class="control-label">{{ 'Quantity' }}</label>
    <input class="form-control" name="quantity" type="text" id="quantity" value="{{ isset($paymentinfo->quantity) ? $paymentinfo->quantity : ''}}" >
    {!! $errors->first('quantity', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('fixed') ? 'has-error' : ''}}">
    <label for="fixed" class="control-label">{{ 'Fixed' }}</label>
    <input class="form-control" name="fixed" type="text" id="fixed" value="{{ isset($paymentinfo->fixed) ? $paymentinfo->fixed : ''}}" >
    {!! $errors->first('fixed', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('submit') ? 'has-error' : ''}}">
    <label for="submit" class="control-label">{{ 'Submit' }}</label>
    <input class="form-control" name="submit" type="text" id="submit" value="{{ isset($paymentinfo->submit) ? $paymentinfo->submit : ''}}" >
    {!! $errors->first('submit', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
    <label for="email" class="control-label">{{ 'Email' }}</label>
    <input class="form-control" name="email" type="text" id="email" value="{{ isset($paymentinfo->email) ? $paymentinfo->email : ''}}" >
    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('expiryDateFormat') ? 'has-error' : ''}}">
    <label for="expiryDateFormat" class="control-label">{{ 'Expirydateformat' }}</label>
    <textarea class="form-control" rows="5" name="expiryDateFormat" type="textarea" id="expiryDateFormat" >{{ isset($paymentinfo->expiryDateFormat) ? $paymentinfo->expiryDateFormat : ''}}</textarea>
    {!! $errors->first('expiryDateFormat', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
    <label for="country" class="control-label">{{ 'Country' }}</label>
    <input class="form-control" name="country" type="text" id="country" value="{{ isset($paymentinfo->country) ? $paymentinfo->country : ''}}" >
    {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
    <label for="city" class="control-label">{{ 'City' }}</label>
    <input class="form-control" name="city" type="text" id="city" value="{{ isset($paymentinfo->city) ? $paymentinfo->city : ''}}" >
    {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('state') ? 'has-error' : ''}}">
    <label for="state" class="control-label">{{ 'State' }}</label>
    <input class="form-control" name="state" type="text" id="state" value="{{ isset($paymentinfo->state) ? $paymentinfo->state : ''}}" >
    {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('zip') ? 'has-error' : ''}}">
    <label for="zip" class="control-label">{{ 'Zip' }}</label>
    <input class="form-control" name="zip" type="text" id="zip" value="{{ isset($paymentinfo->zip) ? $paymentinfo->zip : ''}}" >
    {!! $errors->first('zip', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
    <label for="phone" class="control-label">{{ 'Phone' }}</label>
    <input class="form-control" name="phone" type="text" id="phone" value="{{ isset($paymentinfo->phone) ? $paymentinfo->phone : ''}}" >
    {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('lang') ? 'has-error' : ''}}">
    <label for="lang" class="control-label">{{ 'Lang' }}</label>
    <input class="form-control" name="lang" type="text" id="lang" value="{{ isset($paymentinfo->lang) ? $paymentinfo->lang : ''}}" >
    {!! $errors->first('lang', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('currency_code') ? 'has-error' : ''}}">
    <label for="currency_code" class="control-label">{{ 'Currency Code' }}</label>
    <input class="form-control" name="currency_code" type="text" id="currency_code" value="{{ isset($paymentinfo->currency_code) ? $paymentinfo->currency_code : ''}}" >
    {!! $errors->first('currency_code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('isautorenew') ? 'has-error' : ''}}">
    <label for="isautorenew" class="control-label">{{ 'Isautorenew' }}</label>
    <input class="form-control" name="isautorenew" type="text" id="isautorenew" value="{{ isset($paymentinfo->isautorenew) ? $paymentinfo->isautorenew : ''}}" >
    {!! $errors->first('isautorenew', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('originalTransactionId') ? 'has-error' : ''}}">
    <label for="originalTransactionId" class="control-label">{{ 'Originaltransactionid' }}</label>
    <input class="form-control" name="originalTransactionId" type="text" id="originalTransactionId" value="{{ isset($paymentinfo->originalTransactionId) ? $paymentinfo->originalTransactionId : ''}}" >
    {!! $errors->first('originalTransactionId', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('notificationType') ? 'has-error' : ''}}">
    <label for="notificationType" class="control-label">{{ 'Notificationtype' }}</label>
    <input class="form-control" name="notificationType" type="text" id="notificationType" value="{{ isset($paymentinfo->notificationType) ? $paymentinfo->notificationType : ''}}" >
    {!! $errors->first('notificationType', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('appleAPI') ? 'has-error' : ''}}">
    <label for="appleAPI" class="control-label">{{ 'Appleapi' }}</label>
    <textarea class="form-control" rows="5" name="appleAPI" type="textarea" id="appleAPI" >{{ isset($paymentinfo->appleAPI) ? $paymentinfo->appleAPI : ''}}</textarea>
    {!! $errors->first('appleAPI', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('custom_check_code_uuid') ? 'has-error' : ''}}">
    <label for="custom_check_code_uuid" class="control-label">{{ 'Custom Check Code Uuid' }}</label>
    <textarea class="form-control" rows="5" name="custom_check_code_uuid" type="textarea" id="custom_check_code_uuid" >{{ isset($paymentinfo->custom_check_code_uuid) ? $paymentinfo->custom_check_code_uuid : ''}}</textarea>
    {!! $errors->first('custom_check_code_uuid', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('custom_is_landing') ? 'has-error' : ''}}">
    <label for="custom_is_landing" class="control-label">{{ 'Custom Is Landing' }}</label>
    <input class="form-control" name="custom_is_landing" type="text" id="custom_is_landing" value="{{ isset($paymentinfo->custom_is_landing) ? $paymentinfo->custom_is_landing : ''}}" >
    {!! $errors->first('custom_is_landing', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('order_number') ? 'has-error' : ''}}">
    <label for="order_number" class="control-label">{{ 'Order Number' }}</label>
    <input class="form-control" name="order_number" type="text" id="order_number" value="{{ isset($paymentinfo->order_number) ? $paymentinfo->order_number : ''}}" >
    {!! $errors->first('order_number', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('product_description') ? 'has-error' : ''}}">
    <label for="product_description" class="control-label">{{ 'Product Description' }}</label>
    <input class="form-control" name="product_description" type="text" id="product_description" value="{{ isset($paymentinfo->product_description) ? $paymentinfo->product_description : ''}}" >
    {!! $errors->first('product_description', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('invoice_id') ? 'has-error' : ''}}">
    <label for="invoice_id" class="control-label">{{ 'Invoice Id' }}</label>
    <input class="form-control" name="invoice_id" type="text" id="invoice_id" value="{{ isset($paymentinfo->invoice_id) ? $paymentinfo->invoice_id : ''}}" >
    {!! $errors->first('invoice_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('product_id') ? 'has-error' : ''}}">
    <label for="product_id" class="control-label">{{ 'Product Id' }}</label>
    <input class="form-control" name="product_id" type="text" id="product_id" value="{{ isset($paymentinfo->product_id) ? $paymentinfo->product_id : ''}}" >
    {!! $errors->first('product_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('credit_card_processed') ? 'has-error' : ''}}">
    <label for="credit_card_processed" class="control-label">{{ 'Credit Card Processed' }}</label>
    <input class="form-control" name="credit_card_processed" type="text" id="credit_card_processed" value="{{ isset($paymentinfo->credit_card_processed) ? $paymentinfo->credit_card_processed : ''}}" >
    {!! $errors->first('credit_card_processed', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('pay_method') ? 'has-error' : ''}}">
    <label for="pay_method" class="control-label">{{ 'Pay Method' }}</label>
    <input class="form-control" name="pay_method" type="text" id="pay_method" value="{{ isset($paymentinfo->pay_method) ? $paymentinfo->pay_method : ''}}" >
    {!! $errors->first('pay_method', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('cart_tangible') ? 'has-error' : ''}}">
    <label for="cart_tangible" class="control-label">{{ 'Cart Tangible' }}</label>
    <input class="form-control" name="cart_tangible" type="text" id="cart_tangible" value="{{ isset($paymentinfo->cart_tangible) ? $paymentinfo->cart_tangible : ''}}" >
    {!! $errors->first('cart_tangible', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('merchant_product_id') ? 'has-error' : ''}}">
    <label for="merchant_product_id" class="control-label">{{ 'Merchant Product Id' }}</label>
    <input class="form-control" name="merchant_product_id" type="text" id="merchant_product_id" value="{{ isset($paymentinfo->merchant_product_id) ? $paymentinfo->merchant_product_id : ''}}" >
    {!! $errors->first('merchant_product_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('merchant_order_id') ? 'has-error' : ''}}">
    <label for="merchant_order_id" class="control-label">{{ 'Merchant Order Id' }}</label>
    <input class="form-control" name="merchant_order_id" type="text" id="merchant_order_id" value="{{ isset($paymentinfo->merchant_order_id) ? $paymentinfo->merchant_order_id : ''}}" >
    {!! $errors->first('merchant_order_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('card_holder_name') ? 'has-error' : ''}}">
    <label for="card_holder_name" class="control-label">{{ 'Card Holder Name' }}</label>
    <input class="form-control" name="card_holder_name" type="text" id="card_holder_name" value="{{ isset($paymentinfo->card_holder_name) ? $paymentinfo->card_holder_name : ''}}" >
    {!! $errors->first('card_holder_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('middle_initial') ? 'has-error' : ''}}">
    <label for="middle_initial" class="control-label">{{ 'Middle Initial' }}</label>
    <input class="form-control" name="middle_initial" type="text" id="middle_initial" value="{{ isset($paymentinfo->middle_initial) ? $paymentinfo->middle_initial : ''}}" >
    {!! $errors->first('middle_initial', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('cart_weight') ? 'has-error' : ''}}">
    <label for="cart_weight" class="control-label">{{ 'Cart Weight' }}</label>
    <input class="form-control" name="cart_weight" type="text" id="cart_weight" value="{{ isset($paymentinfo->cart_weight) ? $paymentinfo->cart_weight : ''}}" >
    {!! $errors->first('cart_weight', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
    <label for="first_name" class="control-label">{{ 'First Name' }}</label>
    <input class="form-control" name="first_name" type="text" id="first_name" value="{{ isset($paymentinfo->first_name) ? $paymentinfo->first_name : ''}}" >
    {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
    <label for="last_name" class="control-label">{{ 'Last Name' }}</label>
    <input class="form-control" name="last_name" type="text" id="last_name" value="{{ isset($paymentinfo->last_name) ? $paymentinfo->last_name : ''}}" >
    {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('street_address') ? 'has-error' : ''}}">
    <label for="street_address" class="control-label">{{ 'Street Address' }}</label>
    <input class="form-control" name="street_address" type="text" id="street_address" value="{{ isset($paymentinfo->street_address) ? $paymentinfo->street_address : ''}}" >
    {!! $errors->first('street_address', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('street_address2') ? 'has-error' : ''}}">
    <label for="street_address2" class="control-label">{{ 'Street Address2' }}</label>
    <input class="form-control" name="street_address2" type="text" id="street_address2" value="{{ isset($paymentinfo->street_address2) ? $paymentinfo->street_address2 : ''}}" >
    {!! $errors->first('street_address2', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('expiry_date') ? 'has-error' : ''}}">
    <label for="expiry_date" class="control-label">{{ 'Expiry Date' }}</label>
    <input class="form-control" name="expiry_date" type="text" id="expiry_date" value="{{ isset($paymentinfo->expiry_date) ? $paymentinfo->expiry_date : ''}}" >
    {!! $errors->first('expiry_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ip_country') ? 'has-error' : ''}}">
    <label for="ip_country" class="control-label">{{ 'Ip Country' }}</label>
    <input class="form-control" name="ip_country" type="text" id="ip_country" value="{{ isset($paymentinfo->ip_country) ? $paymentinfo->ip_country : ''}}" >
    {!! $errors->first('ip_country', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('environment') ? 'has-error' : ''}}">
    <label for="environment" class="control-label">{{ 'Environment' }}</label>
    <input class="form-control" name="environment" type="text" id="environment" value="{{ isset($paymentinfo->environment) ? $paymentinfo->environment : ''}}" >
    {!! $errors->first('environment', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('adam_id') ? 'has-error' : ''}}">
    <label for="adam_id" class="control-label">{{ 'Adam Id' }}</label>
    <input class="form-control" name="adam_id" type="text" id="adam_id" value="{{ isset($paymentinfo->adam_id) ? $paymentinfo->adam_id : ''}}" >
    {!! $errors->first('adam_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('app_item_id') ? 'has-error' : ''}}">
    <label for="app_item_id" class="control-label">{{ 'App Item Id' }}</label>
    <input class="form-control" name="app_item_id" type="text" id="app_item_id" value="{{ isset($paymentinfo->app_item_id) ? $paymentinfo->app_item_id : ''}}" >
    {!! $errors->first('app_item_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('application_version') ? 'has-error' : ''}}">
    <label for="application_version" class="control-label">{{ 'Application Version' }}</label>
    <input class="form-control" name="application_version" type="text" id="application_version" value="{{ isset($paymentinfo->application_version) ? $paymentinfo->application_version : ''}}" >
    {!! $errors->first('application_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('bundle_id') ? 'has-error' : ''}}">
    <label for="bundle_id" class="control-label">{{ 'Bundle Id' }}</label>
    <input class="form-control" name="bundle_id" type="text" id="bundle_id" value="{{ isset($paymentinfo->bundle_id) ? $paymentinfo->bundle_id : ''}}" >
    {!! $errors->first('bundle_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('download_id') ? 'has-error' : ''}}">
    <label for="download_id" class="control-label">{{ 'Download Id' }}</label>
    <input class="form-control" name="download_id" type="text" id="download_id" value="{{ isset($paymentinfo->download_id) ? $paymentinfo->download_id : ''}}" >
    {!! $errors->first('download_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('in_app') ? 'has-error' : ''}}">
    <label for="in_app" class="control-label">{{ 'In App' }}</label>
    <input class="form-control" name="in_app" type="text" id="in_app" value="{{ isset($paymentinfo->in_app) ? $paymentinfo->in_app : ''}}" >
    {!! $errors->first('in_app', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('original_application_version') ? 'has-error' : ''}}">
    <label for="original_application_version" class="control-label">{{ 'Original Application Version' }}</label>
    <input class="form-control" name="original_application_version" type="text" id="original_application_version" value="{{ isset($paymentinfo->original_application_version) ? $paymentinfo->original_application_version : ''}}" >
    {!! $errors->first('original_application_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('original_purchase_date') ? 'has-error' : ''}}">
    <label for="original_purchase_date" class="control-label">{{ 'Original Purchase Date' }}</label>
    <input class="form-control" name="original_purchase_date" type="text" id="original_purchase_date" value="{{ isset($paymentinfo->original_purchase_date) ? $paymentinfo->original_purchase_date : ''}}" >
    {!! $errors->first('original_purchase_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('original_purchase_date_ms') ? 'has-error' : ''}}">
    <label for="original_purchase_date_ms" class="control-label">{{ 'Original Purchase Date Ms' }}</label>
    <input class="form-control" name="original_purchase_date_ms" type="text" id="original_purchase_date_ms" value="{{ isset($paymentinfo->original_purchase_date_ms) ? $paymentinfo->original_purchase_date_ms : ''}}" >
    {!! $errors->first('original_purchase_date_ms', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('original_purchase_date_pst') ? 'has-error' : ''}}">
    <label for="original_purchase_date_pst" class="control-label">{{ 'Original Purchase Date Pst' }}</label>
    <input class="form-control" name="original_purchase_date_pst" type="text" id="original_purchase_date_pst" value="{{ isset($paymentinfo->original_purchase_date_pst) ? $paymentinfo->original_purchase_date_pst : ''}}" >
    {!! $errors->first('original_purchase_date_pst', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('receipt_creation_date') ? 'has-error' : ''}}">
    <label for="receipt_creation_date" class="control-label">{{ 'Receipt Creation Date' }}</label>
    <input class="form-control" name="receipt_creation_date" type="text" id="receipt_creation_date" value="{{ isset($paymentinfo->receipt_creation_date) ? $paymentinfo->receipt_creation_date : ''}}" >
    {!! $errors->first('receipt_creation_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('receipt_creation_date_ms') ? 'has-error' : ''}}">
    <label for="receipt_creation_date_ms" class="control-label">{{ 'Receipt Creation Date Ms' }}</label>
    <input class="form-control" name="receipt_creation_date_ms" type="text" id="receipt_creation_date_ms" value="{{ isset($paymentinfo->receipt_creation_date_ms) ? $paymentinfo->receipt_creation_date_ms : ''}}" >
    {!! $errors->first('receipt_creation_date_ms', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('receipt_creation_date_pst') ? 'has-error' : ''}}">
    <label for="receipt_creation_date_pst" class="control-label">{{ 'Receipt Creation Date Pst' }}</label>
    <input class="form-control" name="receipt_creation_date_pst" type="text" id="receipt_creation_date_pst" value="{{ isset($paymentinfo->receipt_creation_date_pst) ? $paymentinfo->receipt_creation_date_pst : ''}}" >
    {!! $errors->first('receipt_creation_date_pst', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('receipt_type') ? 'has-error' : ''}}">
    <label for="receipt_type" class="control-label">{{ 'Receipt Type' }}</label>
    <input class="form-control" name="receipt_type" type="text" id="receipt_type" value="{{ isset($paymentinfo->receipt_type) ? $paymentinfo->receipt_type : ''}}" >
    {!! $errors->first('receipt_type', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('request_date') ? 'has-error' : ''}}">
    <label for="request_date" class="control-label">{{ 'Request Date' }}</label>
    <input class="form-control" name="request_date" type="text" id="request_date" value="{{ isset($paymentinfo->request_date) ? $paymentinfo->request_date : ''}}" >
    {!! $errors->first('request_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('request_date_ms') ? 'has-error' : ''}}">
    <label for="request_date_ms" class="control-label">{{ 'Request Date Ms' }}</label>
    <input class="form-control" name="request_date_ms" type="text" id="request_date_ms" value="{{ isset($paymentinfo->request_date_ms) ? $paymentinfo->request_date_ms : ''}}" >
    {!! $errors->first('request_date_ms', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('request_date_pst') ? 'has-error' : ''}}">
    <label for="request_date_pst" class="control-label">{{ 'Request Date Pst' }}</label>
    <input class="form-control" name="request_date_pst" type="text" id="request_date_pst" value="{{ isset($paymentinfo->request_date_pst) ? $paymentinfo->request_date_pst : ''}}" >
    {!! $errors->first('request_date_pst', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('version_external_identifier') ? 'has-error' : ''}}">
    <label for="version_external_identifier" class="control-label">{{ 'Version External Identifier' }}</label>
    <input class="form-control" name="version_external_identifier" type="text" id="version_external_identifier" value="{{ isset($paymentinfo->version_external_identifier) ? $paymentinfo->version_external_identifier : ''}}" >
    {!! $errors->first('version_external_identifier', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('receipt_data_base64') ? 'has-error' : ''}}">
    <label for="receipt_data_base64" class="control-label">{{ 'Receipt Data Base64' }}</label>
    <textarea class="form-control" rows="5" name="receipt_data_base64" type="textarea" id="receipt_data_base64" >{{ isset($paymentinfo->receipt_data_base64) ? $paymentinfo->receipt_data_base64 : ''}}</textarea>
    {!! $errors->first('receipt_data_base64', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('receipt_data') ? 'has-error' : ''}}">
    <label for="receipt_data" class="control-label">{{ 'Receipt Data' }}</label>
    <textarea class="form-control" rows="5" name="receipt_data" type="textarea" id="receipt_data" >{{ isset($paymentinfo->receipt_data) ? $paymentinfo->receipt_data : ''}}</textarea>
    {!! $errors->first('receipt_data', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('isrecurring') ? 'has-error' : ''}}">
    <label for="isrecurring" class="control-label">{{ 'Isrecurring' }}</label>
    <input class="form-control" name="isrecurring" type="text" id="isrecurring" value="{{ isset($paymentinfo->isrecurring) ? $paymentinfo->isrecurring : ''}}" >
    {!! $errors->first('isrecurring', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('app_version') ? 'has-error' : ''}}">
    <label for="app_version" class="control-label">{{ 'App Version' }}</label>
    <input class="form-control" name="app_version" type="text" id="app_version" value="{{ isset($paymentinfo->app_version) ? $paymentinfo->app_version : ''}}" >
    {!! $errors->first('app_version', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('google_subscription_token') ? 'has-error' : ''}}">
    <label for="google_subscription_token" class="control-label">{{ 'Google Subscription Token' }}</label>
    <input class="form-control" name="google_subscription_token" type="text" id="google_subscription_token" value="{{ isset($paymentinfo->google_subscription_token) ? $paymentinfo->google_subscription_token : ''}}" >
    {!! $errors->first('google_subscription_token', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('apple_check') ? 'has-error' : ''}}">
    <label for="apple_check" class="control-label">{{ 'Apple Check' }}</label>
    <input class="form-control" name="apple_check" type="text" id="apple_check" value="{{ isset($paymentinfo->apple_check) ? $paymentinfo->apple_check : ''}}" >
    {!! $errors->first('apple_check', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('raw_json') ? 'has-error' : ''}}">
    <label for="raw_json" class="control-label">{{ 'Raw Json' }}</label>
    <textarea class="form-control" rows="5" name="raw_json" type="textarea" id="raw_json" >{{ isset($paymentinfo->raw_json) ? $paymentinfo->raw_json : ''}}</textarea>
    {!! $errors->first('raw_json', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
