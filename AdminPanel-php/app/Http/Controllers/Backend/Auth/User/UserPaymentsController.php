<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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

class UserPaymentsController extends Controller
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
            $payments = Payment::where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_uuid', 'LIKE', "%$keyword%")
                ->orWhere('subscription_id', 'LIKE', "%$keyword%")
                ->orWhere('user_id', 'LIKE', "%$keyword%")
                ->orWhere('period_in_months', 'LIKE', "%$keyword%")
                ->orWhere('payment_id', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhere('payment_sum', 'LIKE', "%$keyword%")
                ->orWhere('details', 'LIKE', "%$keyword%")
                ->orWhere('check_code', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $payments = Payment::where('user_id', '=', $user->id)
                ->orderby("create_date","desc")
                ->latest()
                ->paginate($perPage);
        }

        return view('backend.auth.user.payments.index', compact('payments','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.auth.user.payments.create', compact('user'));
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
        
        Payment::create($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-payments-logs')->with('flash_message', 'Payment added!');
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
        $payment = Payment::findOrFail($id);

        return view('backend.auth.user.payments.show', compact('payment','user'));
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
        $payment = Payment::findOrFail($id);

        return view('backend.auth.user.payments.edit', compact('payment','user'));
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
        
        $payment = Payment::findOrFail($id);
        $payment->update($requestData);

        return redirect('admin/auth/user/'. $user->id .'/user-payments-logs')->with('flash_message', 'Payment updated!');
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
        Payment::destroy($id);

        return redirect('admin/auth/user/'. $user->id .'/user-payments-logs')->with('flash_message', 'Payment deleted!');
    }
}
