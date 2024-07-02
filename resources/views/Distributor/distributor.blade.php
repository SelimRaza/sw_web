@extends('theme.app_out')

@section('content')

    <div class="animate form login_form">
        <section class="login_content">
            <form class="form-horizontal" method="POST" action="{{url('distributor/auth/check')}}">
                {{ csrf_field() }}
                @if(Session::has('message'))
                    <div class="alert alert-danger">
                        <strong>{{ Session::get('message') }}</strong>
                    </div>
                @endif
                <h1>Login</h1>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}"
                           placeholder="Username" required autofocus>
                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control" name="password" placeholder="Password"
                           required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif
                </div>
                <div>
                    <button type="submit" class="btn btn-default submit"> Login</button>

                </div>
                <div class="clearfix"></div>

                <div class="separator">

                    <div class="clearfix"></div>
                    <br/>


                </div>
            </form>
        </section>
    </div>
@endsection