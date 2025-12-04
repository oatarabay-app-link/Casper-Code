@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">RadPostAuth {{ $radpostauth->id }}</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/rad-post-auth') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/admin/rad-post-auth/' . $radpostauth->id . '/edit') }}" title="Edit RadPostAuth"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('admin/radpostauth' . '/' . $radpostauth->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-sm" title="Delete RadPostAuth" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $radpostauth->id }}</td>
                                    </tr>
                                    <tr><th> Username </th><td> {{ $radpostauth->username }} </td></tr><tr><th> Pass </th><td> {{ $radpostauth->pass }} </td></tr><tr><th> Reply </th><td> {{ $radpostauth->reply }} </td></tr><tr><th> Priority </th><td> {{ $radpostauth->priority }} </td></tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
