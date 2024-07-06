<!-- <div class="profile clearfix">
  <div class="profile_pic">
      @if ( Auth::user()->employee()->aemp_picn=='')
          <img src="{{ asset("theme/production/images/img.jpg")}}" alt="..."
               class="img-circle profile_img">
      @else
          <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}" alt="..."
               class="img-circle profile_img">
      @endif

  </div>
  <div class="profile_info">
    @if (Auth::guest())
      <li><a href="{{ route('login') }}">Login</a></li>
    @else
      <span>Welcome,</span>
      <h2>{{ Auth::user()->cont_name }}</h2>
    @endif

  </div>
</div> -->