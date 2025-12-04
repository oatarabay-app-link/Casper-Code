<?php

namespace App\Http\Controllers\Backend\Auth\User;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\UserRadiusAttribute;
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

class UserRadiusAttributesController extends Controller
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
            $userradiusattributes = UserRadiusAttribute::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('attribute', 'LIKE', "%$keyword%")
                ->orWhere('op', 'LIKE', "%$keyword%")
                ->orWhere('value', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $userradiusattributes = UserRadiusAttribute::where('user_id', '=', $user->id)->orderby("id","desc")->latest()->paginate($perPage);
        }

        return view('backend.auth.user.user-radius-attributes.index', compact('userradiusattributes','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.user-radius-attributes.create', compact('user'));
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
        
        UserRadiusAttribute::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-radius-attributes')->with('flash_message', 'User Radius Attribute added!');
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
        $userradiusattribute = UserRadiusAttribute::findOrFail($id);

        return view('backend.auth.user.user-radius-attributes.show', compact('userradiusattribute','user'));
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
        $userradiusattribute = UserRadiusAttribute::findOrFail($id);

        return view('backend.auth.user.user-radius-attributes.edit', compact('userradiusattribute','user'));
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
        
        $userradiusattribute = UserRadiusAttribute::findOrFail($id);
        $userradiusattribute->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-radius-attributes')->with('flash_message', 'User Radius Attribute updated!');
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
        UserRadiusAttribute::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/user-radius-attributes')->with('flash_message', 'User Radius Attribute deleted!');
    }
}
