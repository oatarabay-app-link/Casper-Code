@extends('backend.layouts.app')

@section('content')
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">ServiceProvider {{ $serviceprovider->id }} Name:{{ $serviceprovider->name }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/service-providers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/service-providers/' . $serviceprovider->id . '/edit') }}" title="Edit ServiceProvider"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/serviceproviders' . '/' . $serviceprovider->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete ServiceProvider" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $serviceprovider->id }}</td>
                                    </tr>
                                    <tr><th> UUID </th>
                                        <td> {{ $serviceprovider->uuid }} </td>
                                    </tr>
                                    <tr>
                                        <th> Name </th>
                                        <td> {{ $serviceprovider->name }} </td>
                                    </tr>
                                    <tr>
                                        <th> URL </th><td> {{ $serviceprovider->url }} </td></tr><tr><th> Username </th><td> {{ $serviceprovider->username }} </td></tr><tr><th> Password </th><td> {{ $serviceprovider->password }} </td></tr><tr><th> Provider Type </th><td> {{ $serviceprovider->provider_type }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

@endsection
