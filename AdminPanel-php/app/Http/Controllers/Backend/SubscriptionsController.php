<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
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
            $subscriptions = Subscription::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_name', 'LIKE', "%$keyword%")
                ->orWhere('monthly_price', 'LIKE', "%$keyword%")
                ->orWhere('period_price', 'LIKE', "%$keyword%")
                ->orWhere('currency_type', 'LIKE', "%$keyword%")
                ->orWhere('traffic_size', 'LIKE', "%$keyword%")
                ->orWhere('rate_limit', 'LIKE', "%$keyword%")
                ->orWhere('max_connections', 'LIKE', "%$keyword%")
                ->orWhere('available_for_android', 'LIKE', "%$keyword%")
                ->orWhere('available_for_ios', 'LIKE', "%$keyword%")
                ->orWhere('create_time', 'LIKE', "%$keyword%")
                ->orWhere('is_default', 'LIKE', "%$keyword%")
                ->orWhere('period_length', 'LIKE', "%$keyword%")
                ->orWhere('order_num', 'LIKE', "%$keyword%")
                ->orWhere('product_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $subscriptions = Subscription::latest()->paginate($perPage);
        }

        return view('backend.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.subscriptions.create');
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
        
        Subscription::create($requestData);

        return redirect('admin/subscriptions')->with('flash_message', 'Subscription added!');
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
        $subscription = Subscription::findOrFail($id);

        return view('backend.subscriptions.show', compact('subscription'));
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
        $subscription = Subscription::findOrFail($id);

        return view('backend.subscriptions.edit', compact('subscription'));
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
        
        $subscription = Subscription::findOrFail($id);
        $subscription->update($requestData);

        return redirect('admin/subscriptions')->with('flash_message', 'Subscription updated!');
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
        Subscription::destroy($id);

        return redirect('admin/subscriptions')->with('flash_message', 'Subscription deleted!');
    }
}
