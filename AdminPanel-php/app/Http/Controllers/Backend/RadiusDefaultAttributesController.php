<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RadiusDefaultAttribute;
use Illuminate\Http\Request;

class RadiusDefaultAttributesController extends Controller
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
            $radiusdefaultattributes = RadiusDefaultAttribute::where('attribute', 'LIKE', "%$keyword%")
                ->orWhere('op', 'LIKE', "%$keyword%")
                ->orWhere('value', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $radiusdefaultattributes = RadiusDefaultAttribute::latest()->paginate($perPage);
        }

        return view('backend.radius-default-attributes.index', compact('radiusdefaultattributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.radius-default-attributes.create');
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
        
        RadiusDefaultAttribute::create($requestData);

        return redirect('admin/radius-default-attributes')->with('flash_message', 'RadiusDefaultAttribute added!');
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
        $radiusdefaultattribute = RadiusDefaultAttribute::findOrFail($id);

        return view('backend.radius-default-attributes.show', compact('radiusdefaultattribute'));
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
        $radiusdefaultattribute = RadiusDefaultAttribute::findOrFail($id);

        return view('backend.radius-default-attributes.edit', compact('radiusdefaultattribute'));
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
        
        $radiusdefaultattribute = RadiusDefaultAttribute::findOrFail($id);
        $radiusdefaultattribute->update($requestData);

        return redirect('admin/radius-default-attributes')->with('flash_message', 'RadiusDefaultAttribute updated!');
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
        RadiusDefaultAttribute::destroy($id);

        return redirect('admin/radius-default-attributes')->with('flash_message', 'RadiusDefaultAttribute deleted!');
    }
}
