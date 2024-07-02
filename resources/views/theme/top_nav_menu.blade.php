<div class="top_nav" style="margin-left: 19px;
margin-right: 19px;">
    <div class="nav_menu" style=" background-color:#6491EA!important;color:white !important;">

        <nav>
            <div class="nav toggle">
                <!-- <a id="menu_toggle"><i class="fa fa-bars"></i></a> -->
                <a  href="{{url('/home')}}" style="color:white !important;">Home</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="{{ route('login') }}">Login</a></li>
                @else
                    <li class="">
                        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                           aria-expanded="false">
                            @if ( Auth::user()->employee()->aemp_picn=='')
                                <img src="{{ asset("theme/production/images/img.jpg")}}" alt="">{{ Auth::user()->employee()->aemp_name }}
                            @else
                                <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="">{{ Auth::user()->employee()->aemp_name }}
                            @endif
                            <span class=" fa fa-angle-down"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-usermenu pull-right">
                            <li>
                                <a href="{{ URL::to('/employee/profileEdit/')}}">My Profile</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('employee/'.Auth::user()->id.'/passChange')}}">Change Password</a>
                            </li>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i
                                            class="fa fa-sign-out pull-right"></i> Log Out</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                    
                @endif


            </ul>
        </nav>
        <div>
            <!-- <h1 id="txt" class="text-center" style="text-align:center!important;">{{date("h:i:sA")}}</h1> -->
            
            
        </div>
            
          
    </div>
</div>