<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Payments_Check;


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

class UserPaymentsCheckController extends Controller
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
            $payments_check = Payments_Check::where('user_id', '=', $user->id)
                ->orderby("create_date","desc")
                ->orderby("id","desc")
                ->latest()->paginate($perPage);
        }

        return view('backend.auth.user.payments-check.index', compact('payments_check','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.payments-check.create', compact('user'));
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
        
        Payments_Check::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-payments-check')->with('flash_message', 'Payments_Check added!');
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
        $payments_check = Payments_Check::findOrFail($id);

        return view('backend.auth.user.payments-check.show', compact('payments_check','user'));
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
        $payments_check = Payments_Check::findOrFail($id);

        return view('backend.auth.user.payments-check.edit', compact('payments_check','user'));
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
        
        $payments_check = Payments_Check::findOrFail($id);
        $payments_check->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-payments-check')->with('flash_message', 'Payments_Check updated!');
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
        Payments_Check::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/user-payments-check')->with('flash_message', 'Payments_Check deleted!');
    }
}
