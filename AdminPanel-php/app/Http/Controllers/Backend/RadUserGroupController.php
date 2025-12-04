<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RadUserGroup;
use Illuminate\Http\Request;

class RadUserGroupController extends Controller
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
            $radusergroup = RadUserGroup::where('username', 'LIKE', "%$keyword%")
                ->orWhere('groupname', 'LIKE', "%$keyword%")
                ->orWhere('priority', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $radusergroup = RadUserGroup::latest()->paginate($perPage);
        }

        return view('backend.rad-user-group.index', compact('radusergroup'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.rad-user-group.create');
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
        
        RadUserGroup::create($requestData);

        return redirect('admin/rad-user-group')->with('flash_message', 'RadUserGroup added!');
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
        $radusergroup = RadUserGroup::findOrFail($id);

        return view('backend.rad-user-group.show', compact('radusergroup'));
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
        $radusergroup = RadUserGroup::findOrFail($id);

        return view('backend.rad-user-group.edit', compact('radusergroup'));
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
        
        $radusergroup = RadUserGroup::findOrFail($id);
        $radusergroup->update($requestData);

        return redirect('admin/rad-user-group')->with('flash_message', 'RadUserGroup updated!');
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
        RadUserGroup::destroy($id);

        return redirect('admin/rad-user-group')->with('flash_message', 'RadUserGroup deleted!');
    }
}
