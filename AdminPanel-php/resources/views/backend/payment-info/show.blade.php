@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">PaymentInfo {{ $paymentinfo->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/payment-info') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/payment-info/' . $paymentinfo->id . '/edit') }}" title="Edit PaymentInfo"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/paymentinfo' . '/' . $paymentinfo->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete PaymentInfo" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $paymentinfo->id }}</td>
                                    </tr>
                                    <tr><th> Uuid </th><td> {{ $paymentinfo->uuid }} </td></tr><tr><th> Subscription Uuid </th><td> {{ $paymentinfo->subscription_uuid }} </td></tr><tr><th> Subscription Id </th><td> {{ $paymentinfo->subscription_id }} </td></tr><tr><th> Payments Table Id </th><td> {{ $paymentinfo->payments_table_id }} </td></tr><tr><th> User Id </th><td> {{ $paymentinfo->user_id }} </td></tr><tr><th> Check Code Id </th><td> {{ $paymentinfo->check_code_id }} </td></tr><tr><th> Period In Months </th><td> {{ $paymentinfo->period_in_months }} </td></tr><tr><th> Payment Id </th><td> {{ $paymentinfo->payment_id }} </td></tr><tr><th> Status </th><td> {{ $paymentinfo->status }} </td></tr><tr><th> Payment Sum </th><td> {{ $paymentinfo->payment_sum }} </td></tr><tr><th> Details </th><td> {{ $paymentinfo->details }} </td></tr><tr><th> Check Code </th><td> {{ $paymentinfo->check_code }} </td></tr><tr><th> Sid </th><td> {{ $paymentinfo->sid }} </td></tr><tr><th> Key </th><td> {{ $paymentinfo->key }} </td></tr><tr><th> Demo </th><td> {{ $paymentinfo->demo }} </td></tr><tr><th> Total </th><td> {{ $paymentinfo->total }} </td></tr><tr><th> Quantity </th><td> {{ $paymentinfo->quantity }} </td></tr><tr><th> Fixed </th><td> {{ $paymentinfo->fixed }} </td></tr><tr><th> Submit </th><td> {{ $paymentinfo->submit }} </td></tr><tr><th> Email </th><td> {{ $paymentinfo->email }} </td></tr><tr><th> ExpiryDateFormat </th><td> {{ $paymentinfo->expiryDateFormat }} </td></tr><tr><th> Country </th><td> {{ $paymentinfo->country }} </td></tr><tr><th> City </th><td> {{ $paymentinfo->city }} </td></tr><tr><th> State </th><td> {{ $paymentinfo->state }} </td></tr><tr><th> Zip </th><td> {{ $paymentinfo->zip }} </td></tr><tr><th> Phone </th><td> {{ $paymentinfo->phone }} </td></tr><tr><th> Lang </th><td> {{ $paymentinfo->lang }} </td></tr><tr><th> Currency Code </th><td> {{ $paymentinfo->currency_code }} </td></tr><tr><th> Isautorenew </th><td> {{ $paymentinfo->isautorenew }} </td></tr><tr><th> OriginalTransactionId </th><td> {{ $paymentinfo->originalTransactionId }} </td></tr><tr><th> NotificationType </th><td> {{ $paymentinfo->notificationType }} </td></tr><tr><th> AppleAPI </th><td> {{ $paymentinfo->appleAPI }} </td></tr><tr><th> Custom Check Code Uuid </th><td> {{ $paymentinfo->custom_check_code_uuid }} </td></tr><tr><th> Custom Is Landing </th><td> {{ $paymentinfo->custom_is_landing }} </td></tr><tr><th> Order Number </th><td> {{ $paymentinfo->order_number }} </td></tr><tr><th> Product Description </th><td> {{ $paymentinfo->product_description }} </td></tr><tr><th> Invoice Id </th><td> {{ $paymentinfo->invoice_id }} </td></tr><tr><th> Product Id </th><td> {{ $paymentinfo->product_id }} </td></tr><tr><th> Credit Card Processed </th><td> {{ $paymentinfo->credit_card_processed }} </td></tr><tr><th> Pay Method </th><td> {{ $paymentinfo->pay_method }} </td></tr><tr><th> Cart Tangible </th><td> {{ $paymentinfo->cart_tangible }} </td></tr><tr><th> Merchant Product Id </th><td> {{ $paymentinfo->merchant_product_id }} </td></tr><tr><th> Merchant Order Id </th><td> {{ $paymentinfo->merchant_order_id }} </td></tr><tr><th> Card Holder Name </th><td> {{ $paymentinfo->card_holder_name }} </td></tr><tr><th> Middle Initial </th><td> {{ $paymentinfo->middle_initial }} </td></tr><tr><th> Cart Weight </th><td> {{ $paymentinfo->cart_weight }} </td></tr><tr><th> First Name </th><td> {{ $paymentinfo->first_name }} </td></tr><tr><th> Last Name </th><td> {{ $paymentinfo->last_name }} </td></tr><tr><th> Street Address </th><td> {{ $paymentinfo->street_address }} </td></tr><tr><th> Street Address2 </th><td> {{ $paymentinfo->street_address2 }} </td></tr><tr><th> Expiry Date </th><td> {{ $paymentinfo->expiry_date }} </td></tr><tr><th> Ip Country </th><td> {{ $paymentinfo->ip_country }} </td></tr><tr><th> Environment </th><td> {{ $paymentinfo->environment }} </td></tr><tr><th> Adam Id </th><td> {{ $paymentinfo->adam_id }} </td></tr><tr><th> App Item Id </th><td> {{ $paymentinfo->app_item_id }} </td></tr><tr><th> Application Version </th><td> {{ $paymentinfo->application_version }} </td></tr><tr><th> Bundle Id </th><td> {{ $paymentinfo->bundle_id }} </td></tr><tr><th> Download Id </th><td> {{ $paymentinfo->download_id }} </td></tr><tr><th> In App </th><td> {{ $paymentinfo->in_app }} </td></tr><tr><th> Original Application Version </th><td> {{ $paymentinfo->original_application_version }} </td></tr><tr><th> Original Purchase Date </th><td> {{ $paymentinfo->original_purchase_date }} </td></tr><tr><th> Original Purchase Date Ms </th><td> {{ $paymentinfo->original_purchase_date_ms }} </td></tr><tr><th> Original Purchase Date Pst </th><td> {{ $paymentinfo->original_purchase_date_pst }} </td></tr><tr><th> Receipt Creation Date </th><td> {{ $paymentinfo->receipt_creation_date }} </td></tr><tr><th> Receipt Creation Date Ms </th><td> {{ $paymentinfo->receipt_creation_date_ms }} </td></tr><tr><th> Receipt Creation Date Pst </th><td> {{ $paymentinfo->receipt_creation_date_pst }} </td></tr><tr><th> Receipt Type </th><td> {{ $paymentinfo->receipt_type }} </td></tr><tr><th> Request Date </th><td> {{ $paymentinfo->request_date }} </td></tr><tr><th> Request Date Ms </th><td> {{ $paymentinfo->request_date_ms }} </td></tr><tr><th> Request Date Pst </th><td> {{ $paymentinfo->request_date_pst }} </td></tr><tr><th> Version External Identifier </th><td> {{ $paymentinfo->version_external_identifier }} </td></tr><tr><th> Receipt Data Base64 </th><td> {{ $paymentinfo->receipt_data_base64 }} </td></tr><tr><th> Receipt Data </th><td> {{ $paymentinfo->receipt_data }} </td></tr><tr><th> Isrecurring </th><td> {{ $paymentinfo->isrecurring }} </td></tr><tr><th> App Version </th><td> {{ $paymentinfo->app_version }} </td></tr><tr><th> Google Subscription Token </th><td> {{ $paymentinfo->google_subscription_token }} </td></tr><tr><th> Apple Check </th><td> {{ $paymentinfo->apple_check }} </td></tr><tr><th> Raw Json </th><td> {{ $paymentinfo->raw_json }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
