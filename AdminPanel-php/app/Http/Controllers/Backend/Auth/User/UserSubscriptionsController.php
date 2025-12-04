<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserSubscription;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Events\Backend\Auth\User\UserDeleted;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;

class UserSubscriptionsController extends Controller
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
            $usersubscriptions = UserSubscription::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_start_date', 'LIKE', "%$keyword%")
                ->orWhere('subscription_end_date', 'LIKE', "%$keyword%")
                ->orWhere('vpn_pass', 'LIKE', "%$keyword%")
                ->orWhere('is_active', 'LIKE', "%$keyword%")
                ->orWhere('subscription_id', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $usersubscriptions = UserSubscription::where('user_id', '=', $user->id)->orderby("id","desc")->latest()->paginate($perPage);
        }

        //dd($usersubscriptions);

        return view('backend.auth.user.user-subscriptions.index', compact('usersubscriptions',"user"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.user-subscriptions.create', compact('user'));
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
        
        UserSubscription::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-subscriptions')->with('flash_message', 'User Subscription added!');
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
        $usersubscription = UserSubscription::findOrFail($id);

        return view('backend.auth.user.user-subscriptions.show', compact('usersubscription','user'));
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
        $usersubscription = UserSubscription::findOrFail($id);

        return view('backend.auth.user.user-subscriptions.edit', compact('usersubscription', 'user'));
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
        $usersubscription = UserSubscription::findOrFail($id);
        $usersubscription->update($requestData);
        return redirect('admin/auth/user/'. $user->id .'/user-subscriptions')->with('flash_message', 'User Subscription updated!');
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
        UserSubscription::destroy($id);

        return redirect('/admin/auth/user/'. $user->id .'/user-subscriptions')->with('flash_message', 'User Subscription deleted!');
    }
}
