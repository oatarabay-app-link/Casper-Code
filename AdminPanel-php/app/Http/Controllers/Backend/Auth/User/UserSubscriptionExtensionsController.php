<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\UserSubscriptionExtension;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use App\Events\Backend\Auth\User\UserDeleted;
use App\Repositories\Backend\Auth\RoleRepository;
use App\Repositories\Backend\Auth\UserRepository;
use App\Repositories\Backend\Auth\PermissionRepository;
use App\Http\Requests\Backend\Auth\User\StoreUserRequest;
use App\Http\Requests\Backend\Auth\User\ManageUserRequest;
use App\Http\Requests\Backend\Auth\User\UpdateUserRequest;
use Carbon\Carbon;
class UserSubscriptionExtensionsController extends Controller
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
            $usersubscriptionextensions = UserSubscriptionExtension::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('subscription_id', 'LIKE', "%$keyword%")
                ->orWhere('days', 'LIKE', "%$keyword%")
                ->orWhere('exipry_date', 'LIKE', "%$keyword%")
                ->orWhere('note', 'LIKE', "%$keyword%")
                ->orWhere('added_by', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $usersubscriptionextensions = UserSubscriptionExtension::where('user_id', '=', $user->id)->latest()->orderby("id","desc")->paginate($perPage);
        }

        return view('backend.auth.user.user-subscription-extensions.index', compact('usersubscriptionextensions','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.user-subscription-extensions.create', compact('user'));
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
        $days=$requestData['days'];
        $expiry_date = $user->subscription->subscription_end_date;
        $exp= Carbon::parse($expiry_date);
        $exp = $exp->addDays($days);
        $requestData['exipry_date'] =$exp;




        UserSubscriptionExtension::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-subscription-extensions')->with('flash_message', 'User Subscription Extension added!');
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
        $usersubscriptionextension = UserSubscriptionExtension::findOrFail($id);

        return view('backend.auth.user.user-subscription-extensions.show', compact('usersubscriptionextension','user'));
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
        $usersubscriptionextension = UserSubscriptionExtension::findOrFail($id);

        return view('backend.auth.user.user-subscription-extensions.edit', compact('usersubscriptionextension','user'));
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



        $usersubscriptionextension = UserSubscriptionExtension::findOrFail($id);
        $usersubscriptionextension->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-subscription-extensions')->with('flash_message', 'User Subscription Extension updated!');
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
        UserSubscriptionExtension::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/user-subscription-extensions')->with('flash_message', 'User Subscription Extension deleted!');
    }
}
