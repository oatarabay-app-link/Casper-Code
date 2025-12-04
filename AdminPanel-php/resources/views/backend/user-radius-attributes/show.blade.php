@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">UserRadiusAttribute {{ $userradiusattribute->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/user-radius-attributes') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/user-radius-attributes/' . $userradiusattribute->id . '/edit') }}" title="Edit UserRadiusAttribute"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/userradiusattributes' . '/' . $userradiusattribute->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete UserRadiusAttribute" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $userradiusattribute->id }}</td>
                                    </tr>
                                    <tr><th> User Id </th><td> {{ $userradiusattribute->user_id }} </td></tr><tr><th> Attribute </th><td> {{ $userradiusattribute->attribute }} </td></tr><tr><th> Op </th><td> {{ $userradiusattribute->op }} </td></tr><tr><th> Value </th><td> {{ $userradiusattribute->value }} </td></tr><tr><th> Description </th><td> {{ $userradiusattribute->description }} </td></tr><tr><th> Status </th><td> {{ $userradiusattribute->status }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
