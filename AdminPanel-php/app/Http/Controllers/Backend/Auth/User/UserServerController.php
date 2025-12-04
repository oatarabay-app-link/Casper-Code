<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\UserServer;
use App\VPNServer;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Events\Backend\Auth\User\UserDeleted;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;

class UserServerController extends Controller
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
            $userservers = UserServer::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('vpnserver_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $userservers = UserServer::where('user_id', '=', $user->id)->orderby("id","desc")->latest()->paginate($perPage);
        }

        return view('backend.auth.user.user-servers.index', compact('userservers','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        $servers=VPNServer::all();
        return view('backend.auth.user.user-servers.create', compact('user','servers'));
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
        
        UserServer::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-servers')->with('flash_message', 'User Server added!');
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
        $userserver = UserServer::findOrFail($id);

        return view('backend.auth.user.user-servers.show', compact('userserver','user'));
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
        $userserver = UserServer::findOrFail($id);

        return view('backend.auth.user.user-servers.edit', compact('userserver','user'));
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
        
        $userserver = UserServer::findOrFail($id);
        $userserver->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-servers')->with('flash_message', 'User Server updated!');
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
        UserServer::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/user-servers')->with('flash_message', 'User Server deleted!');
    }
}
