<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RadPostAuth;
use Illuminate\Http\Request;

class RadPostAuthController extends Controller
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
            $radpostauth = RadPostAuth::where('username', 'LIKE', "%$keyword%")
                ->orWhere('pass', 'LIKE', "%$keyword%")
                ->orWhere('reply', 'LIKE', "%$keyword%")
                ->orWhere('priority', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $radpostauth = RadPostAuth::latest()->paginate($perPage);
        }

        return view('backend.rad-post-auth.index', compact('radpostauth'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.rad-post-auth.create');
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
        
        RadPostAuth::create($requestData);

        return redirect('admin/rad-post-auth')->with('flash_message', 'RadPostAuth added!');
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
        $radpostauth = RadPostAuth::findOrFail($id);

        return view('backend.rad-post-auth.show', compact('radpostauth'));
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
        $radpostauth = RadPostAuth::findOrFail($id);

        return view('backend.rad-post-auth.edit', compact('radpostauth'));
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
        
        $radpostauth = RadPostAuth::findOrFail($id);
        $radpostauth->update($requestData);

        return redirect('admin/rad-post-auth')->with('flash_message', 'RadPostAuth updated!');
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
        RadPostAuth::destroy($id);

        return redirect('admin/rad-post-auth')->with('flash_message', 'RadPostAuth deleted!');
    }
}
