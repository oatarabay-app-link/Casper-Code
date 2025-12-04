@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">NA {{ $na->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/n-a-s') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/n-a-s/' . $na->id . '/edit') }}" title="Edit NA"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/nas' . '/' . $na->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete NA" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $na->id }}</td>
                                    </tr>
                                    <tr><th> Nasname </th><td> {{ $na->nasname }} </td></tr><tr><th> Shortname </th><td> {{ $na->shortname }} </td></tr><tr><th> Type </th><td> {{ $na->type }} </td></tr><tr><th> Ports </th><td> {{ $na->ports }} </td></tr><tr><th> Secret </th><td> {{ $na->secret }} </td></tr><tr><th> Server </th><td> {{ $na->server }} </td></tr><tr><th> Community </th><td> {{ $na->community }} </td></tr><tr><th> Description </th><td> {{ $na->description }} </td></tr><tr><th> Details </th><td> {{ $na->details }} </td></tr><tr><th> Check Code </th><td> {{ $na->check_code }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
