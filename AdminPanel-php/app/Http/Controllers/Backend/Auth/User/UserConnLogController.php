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

class UserConnLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request,User $user)
    {
        $output="";
        $commands='tail -n 300 /var/log/syslog';
        \SSH::into('USServer')->run($commands, function($line) use (&$output)
            {

                $output.=$line."</br>";
            });



        return view('backend.auth.user.conn-log.index',compact('output' ,'user'));
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
