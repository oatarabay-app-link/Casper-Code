<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\VPNServer;
use Illuminate\Http\Request;

class VPNServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 100;

        if (!empty($keyword)) {
            $vpnservers = VPNServer::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('create_date', 'LIKE', "%$keyword%")
                ->orWhere('is_deleted', 'LIKE', "%$keyword%")
                ->orWhere('is_disabled', 'LIKE', "%$keyword%")
                ->orWhere('ip', 'LIKE', "%$keyword%")
                ->orWhere('latitude', 'LIKE', "%$keyword%")
                ->orWhere('longitude', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('country', 'LIKE', "%$keyword%")
                ->orWhere('parameters', 'LIKE', "%$keyword%")
                ->orWhere('server_provider', 'LIKE', "%$keyword%")
                ->orWhere('notes', 'LIKE', "%$keyword%")
                ->orWhere('service_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $vpnservers = VPNServer::latest()->paginate($perPage);
        }

        return view('backend.v-p-n-servers.index', compact('vpnservers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.v-p-n-servers.create');
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

        VPNServer::create($requestData);

        return redirect('admin/v-p-n-servers')->with('flash_message', 'VPNServer added!');
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
        $vpnserver = VPNServer::findOrFail($id);

        return view('backend.v-p-n-servers.show', compact('vpnserver'));
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
        $vpnserver = VPNServer::findOrFail($id);

        return view('backend.v-p-n-servers.edit', compact('vpnserver'));
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

        $vpnserver = VPNServer::findOrFail($id);
        $vpnserver->update($requestData);

        return redirect('admin/v-p-n-servers')->with('flash_message', 'VPNServer updated!');
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
        VPNServer::destroy($id);

        return redirect('admin/v-p-n-servers')->with('flash_message', 'VPNServer deleted!');
    }
}
