<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProvidersController extends Controller
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
            $serviceproviders = ServiceProvider::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('url', 'LIKE', "%$keyword%")
                ->orWhere('username', 'LIKE', "%$keyword%")
                ->orWhere('password', 'LIKE', "%$keyword%")
                ->orWhere('provider_type', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $serviceproviders = ServiceProvider::latest()->paginate($perPage);
        }

        return view('backend.service-providers.index', compact('serviceproviders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.service-providers.create');
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
        
        ServiceProvider::create($requestData);

        return redirect('admin/service-providers')->with('flash_message', 'ServiceProvider added!');
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
        $serviceprovider = ServiceProvider::findOrFail($id);

        return view('backend.service-providers.show', compact('serviceprovider'));
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
        $serviceprovider = ServiceProvider::findOrFail($id);

        return view('backend.service-providers.edit', compact('serviceprovider'));
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
        
        $serviceprovider = ServiceProvider::findOrFail($id);
        $serviceprovider->update($requestData);

        return redirect('admin/service-providers')->with('flash_message', 'ServiceProvider updated!');
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
        ServiceProvider::destroy($id);

        return redirect('admin/service-providers')->with('flash_message', 'ServiceProvider deleted!');
    }
}
