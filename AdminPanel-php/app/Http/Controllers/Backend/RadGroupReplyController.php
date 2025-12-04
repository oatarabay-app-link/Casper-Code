<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RadGroupReply;
use Illuminate\Http\Request;

class RadGroupReplyController extends Controller
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
            $radgroupreply = RadGroupReply::where('groupname', 'LIKE', "%$keyword%")
                ->orWhere('attribute', 'LIKE', "%$keyword%")
                ->orWhere('op', 'LIKE', "%$keyword%")
                ->orWhere('value', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $radgroupreply = RadGroupReply::latest()->paginate($perPage);
        }

        return view('backend.rad-group-reply.index', compact('radgroupreply'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.rad-group-reply.create');
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
        
        RadGroupReply::create($requestData);

        return redirect('admin/rad-group-reply')->with('flash_message', 'RadGroupReply added!');
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
        $radgroupreply = RadGroupReply::findOrFail($id);

        return view('backend.rad-group-reply.show', compact('radgroupreply'));
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
        $radgroupreply = RadGroupReply::findOrFail($id);

        return view('backend.rad-group-reply.edit', compact('radgroupreply'));
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
        
        $radgroupreply = RadGroupReply::findOrFail($id);
        $radgroupreply->update($requestData);

        return redirect('admin/rad-group-reply')->with('flash_message', 'RadGroupReply updated!');
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
        RadGroupReply::destroy($id);

        return redirect('admin/rad-group-reply')->with('flash_message', 'RadGroupReply deleted!');
    }
}
