<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Subscription;
use App\SubscriptionRadiusAttribute;
use Illuminate\Http\Request;

class SubscriptionRadiusAttributesController extends Controller
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
            $subscriptionradiusattributes = SubscriptionRadiusAttribute::where('subscription_id', 'LIKE', "%$keyword%")
                ->orWhere('attribute', 'LIKE', "%$keyword%")
                ->orWhere('op', 'LIKE', "%$keyword%")
                ->orWhere('value', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $subscriptionradiusattributes = SubscriptionRadiusAttribute::latest()->paginate($perPage);
        }

        return view('backend.subscription-radius-attributes.index', compact('subscriptionradiusattributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $subs= Subscription::all();

        return view('backend.subscription-radius-attributes.create', compact("subs"));
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
        
        SubscriptionRadiusAttribute::create($requestData);

        return redirect('admin/subscription-radius-attributes')->with('flash_message', 'SubscriptionRadiusAttribute added!');
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
        $subscriptionradiusattribute = SubscriptionRadiusAttribute::findOrFail($id);

        return view('backend.subscription-radius-attributes.show', compact('subscriptionradiusattribute'));
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
        $subscriptionradiusattribute = SubscriptionRadiusAttribute::findOrFail($id);

        $subs= Subscription::all();
        return view('backend.subscription-radius-attributes.edit', compact('subscriptionradiusattribute','subs'));
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
        
        $subscriptionradiusattribute = SubscriptionRadiusAttribute::findOrFail($id);
        $subscriptionradiusattribute->update($requestData);

        return redirect('admin/subscription-radius-attributes')->with('flash_message', 'SubscriptionRadiusAttribute updated!');
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
        SubscriptionRadiusAttribute::destroy($id);

        return redirect('admin/subscription-radius-attributes')->with('flash_message', 'SubscriptionRadiusAttribute deleted!');
    }
}
