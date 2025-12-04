@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">VPNServer {{ $vpnserver->id }} {{ $vpnserver->name }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/v-p-n-servers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/v-p-n-servers/' . $vpnserver->id . '/edit') }}" title="Edit VPNServer"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/vpnservers' . '/' . $vpnserver->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete VPNServer" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="row">
                            <div class="col-sm-6">

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th>
                                        <td>{{ $vpnserver->id }}</td>
                                    </tr>
                                    <tr>
                                        <th> UUID </th>
                                        <td> {{ $vpnserver->uuid }} </td>
                                    </tr>
                                    <tr>
                                        <th> Create Date </th>
                                        <td> {{ $vpnserver->create_date }} </td>
                                    </tr>
                                    <tr>
                                        <th> Is Deleted </th>
                                        <td> {{ $vpnserver->is_deleted }} </td>
                                    </tr>
                                    <tr>
                                        <th> Is Disabled </th>
                                        <td> {{ $vpnserver->is_disabled }} </td>
                                    </tr>
                                    <tr>
                                        <th> Ip </th>
                                        <td> {{ $vpnserver->ip }} </td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="col-sm-6">

                       <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th> Latitude </th>
                                        <td> {{ $vpnserver->latitude }} </td>
                                    </tr>

                                    <tr>
                                        <th> Longitude </th>
                                        <td> {{ $vpnserver->longitude }} </td>
                                    </tr>
                                    <tr>
                                        <th> Name </th>
                                        <td> {{ $vpnserver->name }} </td>
                                    </tr>
                                    <tr>
                                        <th> Country </th>
                                        <td> {{ $vpnserver->country }} </td>
                                    </tr>

                                    <tr>
                                        <th> Server Provider </th>
                                        <td> {{ $vpnserver->server_provider }} </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>




                    </div>

                </div>


                 <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th> Notes </th>
                                        <td> {{ $vpnserver->notes }} </td>
                                    </tr>
                                    <tr>
{{--                                        <th> Parameters </th>--}}
{{--                                        <td> {{ $vpnserver->parameters }} </td>--}}
                                        <?php
                                            //$parameters=  json_decode($vpnserver->parameters);

                                            //print_r($parameters);
                                        ?>


                                    </tr>


                                     </tbody>
                            </table>
                        </div>




                    </div>
                </div>

@endsection
