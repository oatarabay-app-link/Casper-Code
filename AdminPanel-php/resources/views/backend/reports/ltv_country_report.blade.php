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
                        LTV report per Country

                    </h4>
                </div><!--col-->

                <div class="col-sm-4">
                    <div class="btn-toolbar float-right" role="toolbar"
                         aria-label="@lang('labels.general.toolbar_btn_groups')">
                        <a href="#form_filters" class="btn btn-success ml-1" data-toggle="collapse" title="Filter"><i
                                class="fas fa-filter"></i></a>
                    </div><!--btn-toolbar-->

                </div><!--col-->
            </div><!--row-->

            <div class="row mt-2">
                <div class="col">
                <div class="collapse" id="form_filters" style="">
                    <div class="card card-body">
                        <strong>Filters</strong>
                        <form class="form-inline" action="" method="get">
                            <div class="form-group">
                                <label class="mr-1" for="exampleInputName2">From Date</label>
                                <input class="form-control" id="exampleInputName2" type="date" name="date_from">
                            </div>
                            <div class="form-group">
                                <label class="mx-1" for="exampleInputEmail2">To date</label>
                                <input class="form-control" id="exampleInputEmail2" type="date" name="date_to">
                            </div>

                            <div class="form-group">
                                <label class="mx-1" for="exampleInputEmail2">Email</label>
                                <input class="form-control" id="exampleInputEmail2" type="text"   name="email">
                            </div>

                            <div class="form-group">
                                <label class="mx-1" for="exampleInputEmail2">Country</label>
                                <input class="form-control" id="exampleInputEmail2" type="text"  name="country">
                            </div>

                            <div class="form-group">
                                <label class="mx-1" for="exampleInputEmail2">OS</label>
                                <input class="form-control" id="exampleInputEmail2" type="text"  name="os">
                            </div>

{{--                            <div class="form-group">--}}
{{--                                <label class="mx-1" for="exampleInputEmail2" >Stage</label>--}}
{{--                                <select class="form-control" name="stage">--}}
{{--                                    <option value="0">All</option>--}}
{{--                                    <option value="App Sign up">App Sign up</option>--}}
{{--                                    <option value="App Signed In" >App Signed In</option>--}}
{{--                                    <option value="First Time Connection" >First Time Connection</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}

                            <div class="form-group ml-2">
                                <button class="btn btn-sm btn-primary ml-2" type="submit"> Submit</button>
                                <button class="btn btn-sm btn-danger ml-2 " type="reset"> Reset</button>
                            </div>

                        </form>
                    </div>
                </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Country</th>
                                <th>No Of Users</th>
                                <th>No Of Subscriptions</th>
                                <th>Total Value</th>
                            </tr>
                            </thead>
                            <tbody>
                                                        @foreach($users as $user)
                                                            <tr>
                                                                <td><a href="#" >{{ $user->email }}</a></td>
                                                                <td>{{ $user->subscription }}</td>
                                                                <td>{{ $user->device }}</td>
                                                                <td>{{ $user->country }}</td>

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
                                                {!! $users->total() !!} {{ trans_choice('labels.backend.access.users.table.total', $users->total()) }}
                    </div>
                </div><!--col-->

                <div class="col-5">
                    <div class="float-right">
                                                {!! $users->render() !!}
                    </div>
                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->
    </div><!--card-->
@endsection
