<?php

namespace App\Http\Controllers;

use App\DataTables\AppSignupReportDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateAppSignupReportRequest;
use App\Http\Requests\UpdateAppSignupReportRequest;
use App\Models\AppSignupReport;
use Flash;
use App\Http\Controllers\AppBaseController;
use Response;

class AppSignupReportController extends AppBaseController
{
    /**
     * Display a listing of the AppSignupReport.
     *
     * @param AppSignupReportDataTable $appSignupReportDataTable
     * @return Response
     */
    public function index(AppSignupReportDataTable $appSignupReportDataTable)
    {
        return $appSignupReportDataTable->render('app_signup_reports.index');
    }

    /**
     * Show the form for creating a new AppSignupReport.
     *
     * @return Response
     */
    public function create()
    {
        return view('app_signup_reports.create');
    }

    /**
     * Store a newly created AppSignupReport in storage.
     *
     * @param CreateAppSignupReportRequest $request
     *
     * @return Response
     */
    public function store(CreateAppSignupReportRequest $request)
    {
        $input = $request->all();

        /** @var AppSignupReport $appSignupReport */
        $appSignupReport = AppSignupReport::create($input);

        Flash::success('App Signup Report saved successfully.');

        return redirect(route('appSignupReports.index'));
    }

    /**
     * Display the specified AppSignupReport.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AppSignupReport $appSignupReport */
        $appSignupReport = AppSignupReport::find($id);

        if (empty($appSignupReport)) {
            Flash::error('App Signup Report not found');

            return redirect(route('appSignupReports.index'));
        }

        return view('app_signup_reports.show')->with('appSignupReport', $appSignupReport);
    }

    /**
     * Show the form for editing the specified AppSignupReport.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        /** @var AppSignupReport $appSignupReport */
        $appSignupReport = AppSignupReport::find($id);

        if (empty($appSignupReport)) {
            Flash::error('App Signup Report not found');

            return redirect(route('appSignupReports.index'));
        }

        return view('app_signup_reports.edit')->with('appSignupReport', $appSignupReport);
    }

    /**
     * Update the specified AppSignupReport in storage.
     *
     * @param  int              $id
     * @param UpdateAppSignupReportRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAppSignupReportRequest $request)
    {
        /** @var AppSignupReport $appSignupReport */
        $appSignupReport = AppSignupReport::find($id);

        if (empty($appSignupReport)) {
            Flash::error('App Signup Report not found');

            return redirect(route('appSignupReports.index'));
        }

        $appSignupReport->fill($request->all());
        $appSignupReport->save();

        Flash::success('App Signup Report updated successfully.');

        return redirect(route('appSignupReports.index'));
    }

    /**
     * Remove the specified AppSignupReport from storage.
     *
     * @param  int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AppSignupReport $appSignupReport */
        $appSignupReport = AppSignupReport::find($id);

        if (empty($appSignupReport)) {
            Flash::error('App Signup Report not found');

            return redirect(route('appSignupReports.index'));
        }

        $appSignupReport->delete();

        Flash::success('App Signup Report deleted successfully.');

        return redirect(route('appSignupReports.index'));
    }
}
