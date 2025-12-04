@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">RadiusDefaultAttribute {{ $radiusdefaultattribute->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/radius-default-attributes') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/radius-default-attributes/' . $radiusdefaultattribute->id . '/edit') }}" title="Edit RadiusDefaultAttribute"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/radiusdefaultattributes' . '/' . $radiusdefaultattribute->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete RadiusDefaultAttribute" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $radiusdefaultattribute->id }}</td>
                                    </tr>
                                    <tr><th> Attribute </th><td> {{ $radiusdefaultattribute->attribute }} </td></tr><tr><th> Op </th><td> {{ $radiusdefaultattribute->op }} </td></tr><tr><th> Value </th><td> {{ $radiusdefaultattribute->value }} </td></tr><tr><th> Description </th><td> {{ $radiusdefaultattribute->description }} </td></tr><tr><th> Status </th><td> {{ $radiusdefaultattribute->status }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
