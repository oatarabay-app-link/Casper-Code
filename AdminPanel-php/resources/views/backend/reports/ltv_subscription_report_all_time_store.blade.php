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
                        LTV report per Subscription - (Manual)
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



            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Subscription</th>
                                <th>Users</th>
                                <th>Subscriptions</th>
                               <th>LTV</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_users= 0;
                            $total_subs= 0;
                            $total_amount= 0;
                            ?>

                                                        @foreach($ltv as $user)
                                                            <?php
                                                           // $total_attempted= $total_attempted + $user->total;
                                                           // $total_success= $total_success+$user->completed;
                                                           // $total_amount=$total_amount+$user->total_value;
                                                            $total_users +=$user->Users ;
                                                            $total_subs += $user->Subscriptions;
                                                            ?>

                                                            <tr>
                                                                <td><a href="#" >{{ $user->Package }}</a></td>
                                                                <td>{{ $user->Users }}</td>
                                                                <td>{{ $user->Subscriptions}}</td>
                                                                <td>{{ $user->LTV }}</td>

{{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                                                            </tr>

                                                        @endforeach

                                                        <tr>
                                                            <td><b>TOTALS</b></td>


                                                            <td><b>{{  $total_users }} Users</b></td>
                                                            <td><b>{{  $total_subs}}  Subscriotions</b></td>
                                                            {{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                                                        </tr>
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
{{-------------------------------------}}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8">
                    <h4 class="card-title mb-0">
                        Subscriptions as Per Store (Manual)
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



            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Subscription</th>
                                <th>Android</th>
                                <th>IOS</th>
                                <th>Total</th>

                                <th>Total (USD)</th>

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
                                $total_attempted= $total_attempted + $user->total;
                                $total_success= $total_success+$user->completed;
                                $total_amount=$total_amount+$user->total_value;

                                ?>

                                <tr>
                                    <td><a href="#" >{{ $user->subscription }}</a></td>
                                    <td>{{ $user->android }}</td>
                                    <td>{{ $user->ios}}</td>
                                    <td>{{ $user->completed }}</td>
                                    <td>{{ $user->total_value }}</td>
                                    {{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                                </tr>

                            @endforeach

                            <tr>
                                <td><b>TOTALS</b></td>
                                <td><b></b></td>
                                <td><b></b></td>


                                <td><b>{{ $total_success }}</b></td>
                                <td><b>{{ $total_amount}} USD</b></td>
                                {{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                            </tr>
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


    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8">
                    <h4 class="card-title mb-0">
                        LTV report per Country - (Manual)
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



            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Country</th>
                                <th>Users</th>
                                <th>Subscriptions</th>
                                <th>LTV</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_users= 0;
                            $total_subs= 0;
                            $total_amount= 0;
                            ?>

                            @foreach($ctv as $user)
                                <?php
                                // $total_attempted= $total_attempted + $user->total;
                                // $total_success= $total_success+$user->completed;
                                // $total_amount=$total_amount+$user->total_value;
                                $total_users +=$user->Users ;
                                $total_subs += $user->Subscriptions;
                                ?>

                                <tr>
                                    <td><a href="#" >{{ $user->Package }}</a></td>
                                    <td>{{ $user->Users }}</td>
                                    <td>{{ $user->Subscriptions}}</td>
                                    <td>{{ $user->LTV }}</td>

                                    {{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                                </tr>

                            @endforeach

                            <tr>
                                <td><b>TOTALS</b></td>


                                <td><b>{{  $total_users }} Users</b></td>
                                <td><b>{{  $total_subs}}  Subscriotions</b></td>
                                {{--                                                                <td class="btn-td">@include('backend.auth.user.includes.actions', ['user' => $user])</td>--}}
                            </tr>
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
