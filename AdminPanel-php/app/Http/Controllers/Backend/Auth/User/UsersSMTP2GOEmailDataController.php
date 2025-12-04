<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Models\Auth\User;
use App\SMTP2GOEmailDatum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersSMTP2GOEmailDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request, User $user)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (! empty($keyword)) {
            $smtp2goemaildata = SMTP2GOEmailDatum::where('subject', 'LIKE', "%$keyword%")
                ->orWhere('delivered_at', 'LIKE', "%$keyword%")
                ->orWhere('process_status', 'LIKE', "%$keyword%")
                ->orWhere('email_id', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhere('response', 'LIKE', "%$keyword%")
                ->orWhere('email_tx', 'LIKE', "%$keyword%")
                ->orWhere('host', 'LIKE', "%$keyword%")
                ->orWhere('smtpcode', 'LIKE', "%$keyword%")
                ->orWhere('sender', 'LIKE', "%$keyword%")
                ->orWhere('recipient', 'LIKE', "%$keyword%")
                ->orWhere('stmp2gousername', 'LIKE', "%$keyword%")
                ->orWhere('headers', 'LIKE', "%$keyword%")
                ->orWhere('total_opens', 'LIKE', "%$keyword%")
                ->orWhere('opens', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $smtp2goemaildata = SMTP2GOEmailDatum::where('recipient', '=', $user->email)->latest()->paginate($perPage);
        }

        return view('backend.auth.user.user-email-logs.index', compact('smtp2goemaildata',"user"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(User $user)
    {
        return view('backend.s-m-t-p2-g-o-email-data.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(User $user, Request $request)
    {
        $requestData = $request->all();

        SMTP2GOEmailDatum::create($requestData);

        return redirect('admin/s-m-t-p2-g-o-email-data')->with('flash_message', 'SMTP2GOEmailDatum added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show(User $user, $id)
    {
        $smtp2goemaildatum = SMTP2GOEmailDatum::findOrFail($id);

        return view('backend.s-m-t-p2-g-o-email-data.show', compact('smtp2goemaildatum'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user, $id)
    {
        $smtp2goemaildatum = SMTP2GOEmailDatum::findOrFail($id);

        return view('backend.s-m-t-p2-g-o-email-data.edit', compact('smtp2goemaildatum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(User $user, Request $request, $id)
    {
        $requestData = $request->all();

        $smtp2goemaildatum = SMTP2GOEmailDatum::findOrFail($id);
        $smtp2goemaildatum->update($requestData);

        return redirect('admin/s-m-t-p2-g-o-email-data')->with('flash_message', 'SMTP2GOEmailDatum updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(User $user, $id)
    {
        SMTP2GOEmailDatum::destroy($id);

        return redirect('admin/s-m-t-p2-g-o-email-data')->with('flash_message', 'SMTP2GOEmailDatum deleted!');
    }
}
