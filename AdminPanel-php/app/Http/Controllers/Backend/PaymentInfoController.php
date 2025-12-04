<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\PaymentInfo;
use Illuminate\Http\Request;

class PaymentInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $paymentinfo = PaymentInfo::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_id', 'LIKE', "%$keyword%")
                ->orWhere('payments_table_id', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->orWhere('check_code_id', 'LIKE', "%$keyword%")
                ->orWhere('period_in_months', 'LIKE', "%$keyword%")
                ->orWhere('payment_id', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhere('payment_sum', 'LIKE', "%$keyword%")
                ->orWhere('details', 'LIKE', "%$keyword%")
                ->orWhere('check_code', 'LIKE', "%$keyword%")
                ->orWhere('sid', 'LIKE', "%$keyword%")
                ->orWhere('key', 'LIKE', "%$keyword%")
                ->orWhere('demo', 'LIKE', "%$keyword%")
                ->orWhere('total', 'LIKE', "%$keyword%")
                ->orWhere('quantity', 'LIKE', "%$keyword%")
                ->orWhere('fixed', 'LIKE', "%$keyword%")
                ->orWhere('submit', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%")
                ->orWhere('expiryDateFormat', 'LIKE', "%$keyword%")
                ->orWhere('country', 'LIKE', "%$keyword%")
                ->orWhere('city', 'LIKE', "%$keyword%")
                ->orWhere('state', 'LIKE', "%$keyword%")
                ->orWhere('zip', 'LIKE', "%$keyword%")
                ->orWhere('phone', 'LIKE', "%$keyword%")
                ->orWhere('lang', 'LIKE', "%$keyword%")
                ->orWhere('currency_code', 'LIKE', "%$keyword%")
                ->orWhere('isautorenew', 'LIKE', "%$keyword%")
                ->orWhere('originalTransactionId', 'LIKE', "%$keyword%")
                ->orWhere('notificationType', 'LIKE', "%$keyword%")
                ->orWhere('appleAPI', 'LIKE', "%$keyword%")
                ->orWhere('custom_check_code_uuid', 'LIKE', "%$keyword%")
                ->orWhere('custom_is_landing', 'LIKE', "%$keyword%")
                ->orWhere('order_number', 'LIKE', "%$keyword%")
                ->orWhere('product_description', 'LIKE', "%$keyword%")
                ->orWhere('invoice_id', 'LIKE', "%$keyword%")
                ->orWhere('product_id', 'LIKE', "%$keyword%")
                ->orWhere('credit_card_processed', 'LIKE', "%$keyword%")
                ->orWhere('pay_method', 'LIKE', "%$keyword%")
                ->orWhere('cart_tangible', 'LIKE', "%$keyword%")
                ->orWhere('merchant_product_id', 'LIKE', "%$keyword%")
                ->orWhere('merchant_order_id', 'LIKE', "%$keyword%")
                ->orWhere('card_holder_name', 'LIKE', "%$keyword%")
                ->orWhere('middle_initial', 'LIKE', "%$keyword%")
                ->orWhere('cart_weight', 'LIKE', "%$keyword%")
                ->orWhere('first_name', 'LIKE', "%$keyword%")
                ->orWhere('last_name', 'LIKE', "%$keyword%")
                ->orWhere('street_address', 'LIKE', "%$keyword%")
                ->orWhere('street_address2', 'LIKE', "%$keyword%")
                ->orWhere('expiry_date', 'LIKE', "%$keyword%")
                ->orWhere('ip_country', 'LIKE', "%$keyword%")
                ->orWhere('environment', 'LIKE', "%$keyword%")
                ->orWhere('adam_id', 'LIKE', "%$keyword%")
                ->orWhere('app_item_id', 'LIKE', "%$keyword%")
                ->orWhere('application_version', 'LIKE', "%$keyword%")
                ->orWhere('bundle_id', 'LIKE', "%$keyword%")
                ->orWhere('download_id', 'LIKE', "%$keyword%")
                ->orWhere('in_app', 'LIKE', "%$keyword%")
                ->orWhere('original_application_version', 'LIKE', "%$keyword%")
                ->orWhere('original_purchase_date', 'LIKE', "%$keyword%")
                ->orWhere('original_purchase_date_ms', 'LIKE', "%$keyword%")
                ->orWhere('original_purchase_date_pst', 'LIKE', "%$keyword%")
                ->orWhere('receipt_creation_date', 'LIKE', "%$keyword%")
                ->orWhere('receipt_creation_date_ms', 'LIKE', "%$keyword%")
                ->orWhere('receipt_creation_date_pst', 'LIKE', "%$keyword%")
                ->orWhere('receipt_type', 'LIKE', "%$keyword%")
                ->orWhere('request_date', 'LIKE', "%$keyword%")
                ->orWhere('request_date_ms', 'LIKE', "%$keyword%")
                ->orWhere('request_date_pst', 'LIKE', "%$keyword%")
                ->orWhere('version_external_identifier', 'LIKE', "%$keyword%")
                ->orWhere('receipt_data_base64', 'LIKE', "%$keyword%")
                ->orWhere('receipt_data', 'LIKE', "%$keyword%")
                ->orWhere('isrecurring', 'LIKE', "%$keyword%")
                ->orWhere('app_version', 'LIKE', "%$keyword%")
                ->orWhere('google_subscription_token', 'LIKE', "%$keyword%")
                ->orWhere('apple_check', 'LIKE', "%$keyword%")
                ->orWhere('raw_json', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $paymentinfo = PaymentInfo::latest()->paginate($perPage);
        }

        return view('backend.payment-info.index', compact('paymentinfo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.payment-info.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        
        $requestData = $request->all();
        
        PaymentInfo::create($requestData);

        return redirect('admin/payment-info')->with('flash_message', 'PaymentInfo added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $paymentinfo = PaymentInfo::findOrFail($id);

        return view('backend.payment-info.show', compact('paymentinfo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $paymentinfo = PaymentInfo::findOrFail($id);

        return view('backend.payment-info.edit', compact('paymentinfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        
        $requestData = $request->all();
        
        $paymentinfo = PaymentInfo::findOrFail($id);
        $paymentinfo->update($requestData);

        return redirect('admin/payment-info')->with('flash_message', 'PaymentInfo updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        PaymentInfo::destroy($id);

        return redirect('admin/payment-info')->with('flash_message', 'PaymentInfo deleted!');
    }
}
