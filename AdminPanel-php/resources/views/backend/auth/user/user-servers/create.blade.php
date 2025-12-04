@extends('backend.auth.user.show')

@section("inner_content")
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-servers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/user-servers') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('backend.auth.user.user-servers.form', ['formMode' => 'create'])


                        </form>

@endsection
