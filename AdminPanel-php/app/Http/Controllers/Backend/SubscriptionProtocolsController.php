<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SubscriptionProtocol;
use Illuminate\Http\Request;

class SubscriptionProtocolsController extends Controller
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
            $subscriptionprotocols = SubscriptionProtocol::where('subscription_uuid', 'LIKE', "%$keyword%")
                ->orWhere('protocol_uuid', 'LIKE', "%$keyword%")
                ->orWhere('protocol_id', 'LIKE', "%$keyword%")
                ->orWhere('subscription_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $subscriptionprotocols = SubscriptionProtocol::latest()->paginate($perPage);
        }

        return view('backend.subscription-protocols.index', compact('subscriptionprotocols'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.subscription-protocols.create');
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
        
        SubscriptionProtocol::create($requestData);

        return redirect('admin/subscription-protocols')->with('flash_message', 'SubscriptionProtocol added!');
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
        $subscriptionprotocol = SubscriptionProtocol::findOrFail($id);

        return view('backend.subscription-protocols.show', compact('subscriptionprotocol'));
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
        $subscriptionprotocol = SubscriptionProtocol::findOrFail($id);

        return view('backend.subscription-protocols.edit', compact('subscriptionprotocol'));
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
        
        $subscriptionprotocol = SubscriptionProtocol::findOrFail($id);
        $subscriptionprotocol->update($requestData);

        return redirect('admin/subscription-protocols')->with('flash_message', 'SubscriptionProtocol updated!');
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
        SubscriptionProtocol::destroy($id);

        return redirect('admin/subscription-protocols')->with('flash_message', 'SubscriptionProtocol deleted!');
    }
}
