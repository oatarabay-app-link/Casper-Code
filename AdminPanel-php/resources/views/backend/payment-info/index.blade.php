@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Paymentinfo</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/payment-info/create') }}" class="btn btn-success btn-sm" title="Add New PaymentInfo">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/payment-info') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                        <br/>
                        <br/>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Uuid</th><th>Subscription Uuid</th><th>Subscription Id</th><th>Payments Table Id</th><th>User Id</th><th>Check Code Id</th><th>Period In Months</th><th>Payment Id</th><th>Status</th><th>Payment Sum</th><th>Details</th><th>Check Code</th><th>Sid</th><th>Key</th><th>Demo</th><th>Total</th><th>Quantity</th><th>Fixed</th><th>Submit</th><th>Email</th><th>ExpiryDateFormat</th><th>Country</th><th>City</th><th>State</th><th>Zip</th><th>Phone</th><th>Lang</th><th>Currency Code</th><th>Isautorenew</th><th>OriginalTransactionId</th><th>NotificationType</th><th>AppleAPI</th><th>Custom Check Code Uuid</th><th>Custom Is Landing</th><th>Order Number</th><th>Product Description</th><th>Invoice Id</th><th>Product Id</th><th>Credit Card Processed</th><th>Pay Method</th><th>Cart Tangible</th><th>Merchant Product Id</th><th>Merchant Order Id</th><th>Card Holder Name</th><th>Middle Initial</th><th>Cart Weight</th><th>First Name</th><th>Last Name</th><th>Street Address</th><th>Street Address2</th><th>Expiry Date</th><th>Ip Country</th><th>Environment</th><th>Adam Id</th><th>App Item Id</th><th>Application Version</th><th>Bundle Id</th><th>Download Id</th><th>In App</th><th>Original Application Version</th><th>Original Purchase Date</th><th>Original Purchase Date Ms</th><th>Original Purchase Date Pst</th><th>Receipt Creation Date</th><th>Receipt Creation Date Ms</th><th>Receipt Creation Date Pst</th><th>Receipt Type</th><th>Request Date</th><th>Request Date Ms</th><th>Request Date Pst</th><th>Version External Identifier</th><th>Receipt Data Base64</th><th>Receipt Data</th><th>Isrecurring</th><th>App Version</th><th>Google Subscription Token</th><th>Apple Check</th><th>Raw Json</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($paymentinfo as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->uuid }}</td><td>{{ $item->subscription_uuid }}</td><td>{{ $item->subscription_id }}</td><td>{{ $item->payments_table_id }}</td><td>{{ $item->user_id }}</td><td>{{ $item->check_code_id }}</td><td>{{ $item->period_in_months }}</td><td>{{ $item->payment_id }}</td><td>{{ $item->status }}</td><td>{{ $item->payment_sum }}</td><td>{{ $item->details }}</td><td>{{ $item->check_code }}</td><td>{{ $item->sid }}</td><td>{{ $item->key }}</td><td>{{ $item->demo }}</td><td>{{ $item->total }}</td><td>{{ $item->quantity }}</td><td>{{ $item->fixed }}</td><td>{{ $item->submit }}</td><td>{{ $item->email }}</td><td>{{ $item->expiryDateFormat }}</td><td>{{ $item->country }}</td><td>{{ $item->city }}</td><td>{{ $item->state }}</td><td>{{ $item->zip }}</td><td>{{ $item->phone }}</td><td>{{ $item->lang }}</td><td>{{ $item->currency_code }}</td><td>{{ $item->isautorenew }}</td><td>{{ $item->originalTransactionId }}</td><td>{{ $item->notificationType }}</td><td>{{ $item->appleAPI }}</td><td>{{ $item->custom_check_code_uuid }}</td><td>{{ $item->custom_is_landing }}</td><td>{{ $item->order_number }}</td><td>{{ $item->product_description }}</td><td>{{ $item->invoice_id }}</td><td>{{ $item->product_id }}</td><td>{{ $item->credit_card_processed }}</td><td>{{ $item->pay_method }}</td><td>{{ $item->cart_tangible }}</td><td>{{ $item->merchant_product_id }}</td><td>{{ $item->merchant_order_id }}</td><td>{{ $item->card_holder_name }}</td><td>{{ $item->middle_initial }}</td><td>{{ $item->cart_weight }}</td><td>{{ $item->first_name }}</td><td>{{ $item->last_name }}</td><td>{{ $item->street_address }}</td><td>{{ $item->street_address2 }}</td><td>{{ $item->expiry_date }}</td><td>{{ $item->ip_country }}</td><td>{{ $item->environment }}</td><td>{{ $item->adam_id }}</td><td>{{ $item->app_item_id }}</td><td>{{ $item->application_version }}</td><td>{{ $item->bundle_id }}</td><td>{{ $item->download_id }}</td><td>{{ $item->in_app }}</td><td>{{ $item->original_application_version }}</td><td>{{ $item->original_purchase_date }}</td><td>{{ $item->original_purchase_date_ms }}</td><td>{{ $item->original_purchase_date_pst }}</td><td>{{ $item->receipt_creation_date }}</td><td>{{ $item->receipt_creation_date_ms }}</td><td>{{ $item->receipt_creation_date_pst }}</td><td>{{ $item->receipt_type }}</td><td>{{ $item->request_date }}</td><td>{{ $item->request_date_ms }}</td><td>{{ $item->request_date_pst }}</td><td>{{ $item->version_external_identifier }}</td><td>{{ $item->receipt_data_base64 }}</td><td>{{ $item->receipt_data }}</td><td>{{ $item->isrecurring }}</td><td>{{ $item->app_version }}</td><td>{{ $item->google_subscription_token }}</td><td>{{ $item->apple_check }}</td><td>{{ $item->raw_json }}</td>
                                        <td>
                                            <a href="{{ url('/admin/payment-info/' . $item->id) }}" title="View PaymentInfo"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/admin/payment-info/' . $item->id . '/edit') }}" title="Edit PaymentInfo"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/admin/payment-info' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete PaymentInfo" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $paymentinfo->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
