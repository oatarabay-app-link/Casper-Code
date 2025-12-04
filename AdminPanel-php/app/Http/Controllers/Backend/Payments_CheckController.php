<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Payments_Check;
use Illuminate\Http\Request;

class Payments_CheckController extends Controller
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
            $payments_check = Payments_Check::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('create_date', 'LIKE', "%$keyword%")
                ->orWhere('subscription_uuid', 'LIKE', "%$keyword%")
                ->orWhere('user_uuid', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->orWhere('user_email', 'LIKE', "%$keyword%")
                ->orWhere('subscription_id', 'LIKE', "%$keyword%")
                ->orWhere('token', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $payments_check = Payments_Check::latest()->paginate($perPage);
        }

        return view('backend.payments_-check.index', compact('payments_check'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('backend.payments_-check.create');
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
        
        Payments_Check::create($requestData);

        return redirect('admin/payments_-check')->with('flash_message', 'Payments_Check added!');
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
        $payments_check = Payments_Check::findOrFail($id);

        return view('backend.payments_-check.show', compact('payments_check'));
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
        $payments_check = Payments_Check::findOrFail($id);

        return view('backend.payments_-check.edit', compact('payments_check'));
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
        
        $payments_check = Payments_Check::findOrFail($id);
        $payments_check->update($requestData);

        return redirect('admin/payments_-check')->with('flash_message', 'Payments_Check updated!');
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
        Payments_Check::destroy($id);

        return redirect('admin/payments_-check')->with('flash_message', 'Payments_Check deleted!');
    }
}
