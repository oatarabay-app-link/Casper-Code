<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\NA;
use Illuminate\Http\Request;

class NASController extends Controller
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
            $nas = NA::where('nasname', 'LIKE', "%$keyword%")
                ->orWhere('shortname', 'LIKE', "%$keyword%")
                ->orWhere('type', 'LIKE', "%$keyword%")
                ->orWhere('ports', 'LIKE', "%$keyword%")
                ->orWhere('secret', 'LIKE', "%$keyword%")
                ->orWhere('server', 'LIKE', "%$keyword%")
                ->orWhere('community', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('details', 'LIKE', "%$keyword%")
                ->orWhere('check_code', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $nas = NA::latest()->paginate($perPage);
        }

        return view('backend.n-a-s.index', compact('nas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.n-a-s.create');
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
        
        NA::create($requestData);

        return redirect('admin/n-a-s')->with('flash_message', 'NA added!');
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
        $na = NA::findOrFail($id);

        return view('backend.n-a-s.show', compact('na'));
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
        $na = NA::findOrFail($id);

        return view('backend.n-a-s.edit', compact('na'));
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
        
        $na = NA::findOrFail($id);
        $na->update($requestData);

        return redirect('admin/n-a-s')->with('flash_message', 'NA updated!');
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
        NA::destroy($id);

        return redirect('admin/n-a-s')->with('flash_message', 'NA deleted!');
    }
}
