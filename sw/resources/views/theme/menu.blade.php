<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">

        <ul class="nav side-menu">
            @auth
            @foreach($menus as $item)
                <?php $status=false?>
                @foreach ($item->get_user_submenu() as $subitem)
                    @if($subitem->wmnu_id == $item->id)
                            <?php $status=true?>
                    @endif
                @endforeach
                @if($status)
                    <li>
                        @if($item->id==9 || $item->id==35)
                            @if($item->id==9)
                                <a href="{{ URL::to('/report')}}"><i class="{{$item->wmnu_icon}}"></i> {{ $item->wmnu_name}} <span
                                    ></span></a>
                            @else
                                <!-- <a href="{{ URL::to('/e_report')}}"><i class="{{$item->wmnu_icon}}"></i> {{ $item->wmnu_name}} <span
                                    ></span></a> -->
                            @endif
                        
                        @else

                        <a><i class="{{$item->wmnu_icon}}"></i> {{ $item->wmnu_name}} <span
                                    class="fa fa-chevron-down"></span></a>
                        @if ($item->get_user_submenu()->count())
                            <ul class="nav child_menu">
                                @foreach ($item->get_user_submenu() as $subitem)
                                    @if($subitem->wmnu_id == $item->id)
                                        <li><a href="{{ URL::to('/')}}/{{ $subitem->wsmn_wurl}}">{{$subitem->wsmn_name}}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        @endif
                    </li>
                @endif
            @endforeach
            @endauth

        </ul>
    </div>

</div>