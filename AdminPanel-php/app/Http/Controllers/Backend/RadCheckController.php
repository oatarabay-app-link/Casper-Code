<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RadCheck;
use Illuminate\Http\Request;

class RadCheckController extends Controller
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
            $radcheck = RadCheck::where('username', 'LIKE', "%$keyword%")
                ->orWhere('attribute', 'LIKE', "%$keyword%")
                ->orWhere('op', 'LIKE', "%$keyword%")
                ->orWhere('value', 'LIKE', "%$keyword%")
                ->orWhere('protocol', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $radcheck = RadCheck::latest()->paginate($perPage);
        }

        return view('backend.rad-check.index', compact('radcheck'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.rad-check.create');
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
        
        RadCheck::create($requestData);

        return redirect('admin/rad-check')->with('flash_message', 'RadCheck added!');
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
        $radcheck = RadCheck::findOrFail($id);

        return view('backend.rad-check.show', compact('radcheck'));
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
        $radcheck = RadCheck::findOrFail($id);

        return view('backend.rad-check.edit', compact('radcheck'));
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
        
        $radcheck = RadCheck::findOrFail($id);
        $radcheck->update($requestData);

        return redirect('admin/rad-check')->with('flash_message', 'RadCheck updated!');
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
        RadCheck::destroy($id);

        return redirect('admin/rad-check')->with('flash_message', 'RadCheck deleted!');
    }
}
