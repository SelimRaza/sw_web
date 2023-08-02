@extends('theme.app_out')

@section('content')

    <div class="animate form login_form">
        <section class="login-container" >
            <form class="form-horizontal form-box" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                @if(Session::has('message'))
                    <div class="alert alert-danger">
                        <strong>{{ Session::get('message') }}</strong>
                    </div>
                @endif
                <h1>Verification</h1>
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
                    <button type="submit" class="btn btn-primary submit" style="font-family:Cursive;"> Login</button></hr>
                    <!-- <a href="{{route('privacy_policy')}}" >Privacy Policy</a> -->

                </div>
                <div class="clearfix"></div>

                <div class="separator">

                    <div class="clearfix"></div>
                    <br/>


                </div>
            </form>
        </section>
    </div>
    <!-- Messenger Chat Plugin Code -->
    <div id="fb-root"></div>

    <!-- Your Chat Plugin code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "103333711879697");
      chatbox.setAttribute("attribution", "biz_inbox");
    </script>

    <!-- Your SDK code -->
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v17.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
@endsection
