<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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


class UserHistoryController extends Controller
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
            $userhistory = UserHistory::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('event', 'LIKE', "%$keyword%")
                ->orWhere('operation', 'LIKE', "%$keyword%")
                ->orWhere('result', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $userhistory = UserHistory::where('user_id', '=', $user->id)->orderby("id","desc")->latest()->paginate($perPage);
        }

        return view('backend.auth.user.user-history.index', compact('userhistory','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.user-history.create', compact('user','user'));
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
        
        UserHistory::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-history')->with('flash_message', 'User History added!');
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
        $userhistory = UserHistory::findOrFail($id);

        return view('backend.auth.user.user-history.show', compact('userhistory','user'));
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
        $userhistory = UserHistory::findOrFail($id);

        return view('backend.auth.user.user-history.edit', compact('userhistory','user'));
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
        
        $userhistory = UserHistory::findOrFail($id);
        $userhistory->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-history')->with('flash_message', 'User History updated!');
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
        UserHistory::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/user-history')->with('flash_message', 'User History deleted!');
    }
}
