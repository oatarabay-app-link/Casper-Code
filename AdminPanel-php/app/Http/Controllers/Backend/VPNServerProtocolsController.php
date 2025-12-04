<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\VPNServerProtocol;
use Illuminate\Http\Request;

class VPNServerProtocolsController extends Controller
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
            $vpnserverprotocols = VPNServerProtocol::where('vpnserver_uuid', 'LIKE', "%$keyword%")
                ->orWhere('protocol_uuid', 'LIKE', "%$keyword%")
                ->orWhere('vpnserver_id', 'LIKE', "%$keyword%")
                ->orWhere('protocol_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $vpnserverprotocols = VPNServerProtocol::latest()->paginate($perPage);
        }

        return view('backend.v-p-n-server-protocols.index', compact('vpnserverprotocols'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.v-p-n-server-protocols.create');
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
        
        VPNServerProtocol::create($requestData);

        return redirect('admin/v-p-n-server-protocols')->with('flash_message', 'VPNServerProtocol added!');
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
        $vpnserverprotocol = VPNServerProtocol::findOrFail($id);

        return view('backend.v-p-n-server-protocols.show', compact('vpnserverprotocol'));
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
        $vpnserverprotocol = VPNServerProtocol::findOrFail($id);

        return view('backend.v-p-n-server-protocols.edit', compact('vpnserverprotocol'));
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
        
        $vpnserverprotocol = VPNServerProtocol::findOrFail($id);
        $vpnserverprotocol->update($requestData);

        return redirect('admin/v-p-n-server-protocols')->with('flash_message', 'VPNServerProtocol updated!');
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
        VPNServerProtocol::destroy($id);

        return redirect('admin/v-p-n-server-protocols')->with('flash_message', 'VPNServerProtocol deleted!');
    }
}
