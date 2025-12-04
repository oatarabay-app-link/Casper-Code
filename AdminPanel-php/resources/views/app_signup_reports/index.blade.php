@extends('backend.layouts.app')
@section('content')
{{--    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" rel="stylesheet">--}}
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.bootstrap.min.css">--}}

    <ol class="breadcrumb">
        <li class="breadcrumb-item">App Signup Reports</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
             @include('flash::message')
             <div class="row">
                 <div class="col-lg-12">
                     <div class="card">
                         <div class="card-header">
                             <i class="fa fa-align-justify"></i>
                             AppSignupReports

                         </div>
                         <div class="card-body">
                             @include('app_signup_reports.table')

                              <div class="pull-right mr-3">

                              </div>
                         </div>
                     </div>
                  </div>
             </div>
         </div>
    </div>
@endsection

