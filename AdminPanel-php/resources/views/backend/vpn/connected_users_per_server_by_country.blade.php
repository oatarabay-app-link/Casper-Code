@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.users.management'))

@section('breadcrumb-links')
    @include('backend.auth.user.includes.breadcrumb-links')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h4 class="card-title mb-0">
                        Connected VPN Users Per Server By Country
                    </h4>
                </div><!--col-->


            </div><!--row-->

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Country</th>
                                <th>Server IP</th>
                                <th>Users</th>
                                <th>% Connected Users</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php $tp=0 ?>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->Country}}</td>
                                    <td>{{ $user->Server_IP }}</td>
                                    <td>{{ $user->No_of_Users }}</td>


                                    <td>
                                        {{  $per =round(($user->No_of_Users/$users_total)*100)  }}%
                                        <?php $tp= $tp+$per ?>
                                    </td>


                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>TOTAL</strong></td>
                                <td><strong></strong></td>
                                <td><strong>{{ $users_total }}</strong></td>
                                <td><strong>{{ $tp }}%</strong></td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div><!--col-->
            </div><!--row-->
            <div class="row">
                <div class="col-7">
                    <div class="float-left">
                        {{--{!! $users->total() !!} {{ trans_choice('labels.backend.access.users.table.total', $users->total()) }}--}}
                    </div>
                </div><!--col-->

                <div class="col-5">
                    <div class="float-right">
                        {{--{!! $users->render() !!}--}}
                    </div>
                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->
    </div><!--card-->
@endsection
