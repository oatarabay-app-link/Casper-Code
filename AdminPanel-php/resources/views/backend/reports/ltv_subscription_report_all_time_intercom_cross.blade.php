@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.users.management'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8">
                    <h4 class="card-title mb-0">
                        Payment and Reccurring Users -- With Intercom Cross Check   and LTV Per User

                    </h4>
                </div><!--col-->

{{--                <div class="col-sm-4">--}}
{{--                    <div class="btn-toolbar float-right" role="toolbar"--}}
{{--                         aria-label="@lang('labels.general.toolbar_btn_groups')">--}}
{{--                        <a href="#form_filters" class="btn btn-success ml-1" data-toggle="collapse" title="Filter"><i--}}
{{--                                class="fas fa-filter"></i></a>--}}
{{--                    </div><!--btn-toolbar-->--}}

{{--                </div><!--col-->--}}
            </div><!--row-->

{{--            <div class="row mt-2">--}}
{{--                <div class="col">--}}
{{--                <div class="collapse" id="form_filters" style="">--}}
{{--                    <div class="card card-body">--}}
{{--                        <strong>Filters</strong>--}}
{{--                        <form class="form-inline" action="" method="get">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="mr-1" for="exampleInputName2">From Date</label>--}}
{{--                                <input class="form-control" id="exampleInputName2" type="date" name="date_from">--}}
{{--                            </div>--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="mx-1" for="exampleInputEmail2">To date</label>--}}
{{--                                <input class="form-control" id="exampleInputEmail2" type="date" name="date_to">--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="mx-1" for="exampleInputEmail2">Email</label>--}}
{{--                                <input class="form-control" id="exampleInputEmail2" type="text"   name="email">--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="mx-1" for="exampleInputEmail2">Country</label>--}}
{{--                                <input class="form-control" id="exampleInputEmail2" type="text"  name="country">--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="mx-1" for="exampleInputEmail2">OS</label>--}}
{{--                                <input class="form-control" id="exampleInputEmail2" type="text"  name="os">--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="mx-1" for="exampleInputEmail2" >Stage</label>--}}
{{--                                <select class="form-control" name="stage">--}}
{{--                                    <option value="0">All</option>--}}
{{--                                    <option value="App Sign up">App Sign up</option>--}}
{{--                                    <option value="App Signed In" >App Signed In</option>--}}
{{--                                    <option value="First Time Connection" >First Time Connection</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}

{{--                            <div class="form-group ml-2">--}}
{{--                                <button class="btn btn-sm btn-primary ml-2" type="submit"> Submit</button>--}}
{{--                                <button class="btn btn-sm btn-danger ml-2 " type="reset"> Reset</button>--}}
{{--                            </div>--}}

{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Email</th>

                                <th>First Time Seen</th>
                                <th>Last Time Seen</th>
                                <th>Months</th>
                                <th>Payments Found</th>
                                <th>Last Payment Date</th>
                                <th>Last Subscribed Package</th>
                                <th>LTV</th>
                                <th>LTV Package</th>
                                <th>Country</th>
                                <th>IOS (First Device)</th>
                                <th>Android (First Device)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_attempted= 0;
                            $total_success= 0;
                            $total_amount= 0;
                            ?>

                                                        @foreach($data as $user)
                                                            <?php
                                                            //$total_attempted= $total_attempted+ $user->total;
                                                            //$total_success= $total_success+$user->completed;
                                                            //$total_amount=$total_amount+$user->total_value;

                                                            ?>

                                                            <tr>
                                                                <td><a href="#" >{{ $user->email }}</a></td>

                                                                <td>{{ $user->radius_first_seen }}</td>
                                                                <td>{{ $user->radius_last_seen }}</td>
                                                                <td>{{ $user->radius_months }}</td>
                                                                <td>{{ $user->payments_found }}</td>
                                                                <td>{{ $user->last_payment_date}}</td>
                                                                <td>{{ $user->last_subscribed_package}}</td>
                                                                <td>{{ $user->ltv}}</td>
                                                                <td>{{ $user->ltv_package}}</td>
                                                                <td>{{ $user->country}}</td>
                                                                <td>{{ $user->first_device_ios}}</td>
                                                                <td>{{ $user->first_device_android}}</td>
{{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                                                            </tr>

                                                        @endforeach


                            </tbody>
                        </table>



                    </div>
                </div><!--col-->
            </div><!--row-->
            <div class="row">
                <div class="col-7">
                    <div class="float-left">
{{--                                                {!! $data->total() !!} {{ trans_choice('labels.backend.access.users.table.total', $users->total()) }}--}}
                    </div>
                </div><!--col-->

                <div class="col-5">
                    <div class="float-right">
{{--                                                {!! $data->render() !!}--}}
                    </div>
                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->
    </div><!--card-->
@endsection
