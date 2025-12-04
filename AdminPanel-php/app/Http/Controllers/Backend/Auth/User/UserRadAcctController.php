<?php

namespace App\Http\Controllers\Backend\Auth\User;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\RadAcct;
use App\Payment;
use App\UserHistory;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Events\Backend\Auth\User\UserDeleted;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;

class UserRadAcctController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request,User $user)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $radacct = RadAcct::where('groupname', 'LIKE', "%$keyword%")
                ->orWhere('realm', 'LIKE', "%$keyword%")
                ->orWhere('nasipaddress', 'LIKE', "%$keyword%")
                ->orWhere('nasidentifier', 'LIKE', "%$keyword%")
                ->orWhere('nasportid', 'LIKE', "%$keyword%")
                ->orWhere('nasporttype', 'LIKE', "%$keyword%")
                ->orWhere('acctstarttime', 'LIKE', "%$keyword%")
                ->orWhere('acctstoptime', 'LIKE', "%$keyword%")
                ->orWhere('acctsessiontime', 'LIKE', "%$keyword%")
                ->orWhere('acctauthentic', 'LIKE', "%$keyword%")
                ->orWhere('connectinfo_start', 'LIKE', "%$keyword%")
                ->orWhere('connectinfo_stop', 'LIKE', "%$keyword%")
                ->orWhere('acctinputoctest', 'LIKE', "%$keyword%")
                ->orWhere('acctoutputoctest', 'LIKE', "%$keyword%")
                ->orWhere('calledstationid', 'LIKE', "%$keyword%")
                ->orWhere('callingstationid', 'LIKE', "%$keyword%")
                ->orWhere('acctterminatecause', 'LIKE', "%$keyword%")
                ->orWhere('servicetype', 'LIKE', "%$keyword%")
                ->orWhere('framedprotocol', 'LIKE', "%$keyword%")
                ->orWhere('framedipaddress', 'LIKE', "%$keyword%")
                ->orWhere('acctstartdelay', 'LIKE', "%$keyword%")
                ->orWhere('acctstopdelay', 'LIKE', "%$keyword%")
                ->orWhere('xascendsessionsvrkey', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $radacct = RadAcct::where('user_id', '=', $user->id)->orderby("radacctid","desc")->latest()->paginate($perPage);
        }

        return view('backend.auth.user.rad-acct.index', compact('radacct','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.rad-acct.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(User $user,Request $request)
    {
        
        $requestData = $request->all();
        
        RadAcct::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/rad-acct')->with('flash_message', 'RadAcct added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show(User $user,$id)
    {
        $radacct = RadAcct::findOrFail($id);

        return view('backend.auth.user.rad-acct.show', compact('radacct','user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user,$id)
    {
        $radacct = RadAcct::findOrFail($id);

        return view('backend.auth.user.rad-acct.edit', compact('radacct','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(User $user,Request $request, $id)
    {
        
        $requestData = $request->all();
        
        $radacct = RadAcct::findOrFail($id);
        $radacct->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/rad-acct')->with('flash_message', 'RadAcct updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(User $user,$id)
    {
        RadAcct::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/rad-acct')->with('flash_message', 'RadAcct deleted!');
    }
}
