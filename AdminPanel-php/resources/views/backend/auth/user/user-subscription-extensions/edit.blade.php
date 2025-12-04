@extends('backend.auth.user.show')

@section("inner_content")
                <div class="card">
                    <div class="card-header">Edit UserSubscriptionExtension #{{ $usersubscriptionextension->id }}</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/auth/user/'. $user->id .'/user-subscription-extensions') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/auth/user/'. $user->id .'/user-subscription-extensions/' . $usersubscriptionextension->id) }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}

                            @include ('backend.auth.user.user-subscription-extensions.form', ['formMode' => 'edit'])

                        </form>

                    </div>
                </div>

@endsection
