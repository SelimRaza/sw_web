@extends('theme.app')
@section('content')
<div class="right_col" role="main">
<div class="col-md-12">
<div class="col-md-9">
    <h1 style="font-family:New Century Schoolbook, TeX Gyre Schola, serif;" id="sales_header">Primary Sales LeaderBoard</h1>
</div>
<div class="col-md-2">
    <p id="instruction" style="margin-top:18px;float:right;">Switch On To get Secondary Sales</p>
</div>
<div class="checkbox col-md-1" style="float:left;">
    <label>
    <input type="checkbox" data-toggle="toggle"  id="toggleBtn" onchange="getData()">
    <span class="switch-label" data-on="SS" data-off="PS"></span> 
    </label>
</div>
</div>
  <!-- super leader -->
<div id="leader_section">
@if($super_ldr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Super Leaders</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($super_ldr as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID.$sld->oid}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID.$sld->oid}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TGT_AMOUNT?$sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TGT_AMOUNT?(number_format($sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT,1)):0}}%</p>
                      </div>
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:{{$sld->SALES_ZONE_ID}}</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- best contributed super leader -->
@if($bst_cnt_s_ldr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Best Contributed Super Leaders</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($bst_cnt_s_ldr as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TOTAL_TGT_AMNT?$sld->TOTAL_P_AMNT*100/$sld->TOTAL_TGT_AMNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TOTAL_TGT_AMNT?(number_format($sld->TOTAL_P_AMNT*100/$sld->TOTAL_TGT_AMNT,1)):0}}%</p>
                      </div>
                    </div>
                    <div id="{{'prop'.$sld->STAFFID}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- super future leader -->
@if($sf_ldr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Super Future Leader</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($sf_ldr as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID.$sld->oid}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID.$sld->oid}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TGT_AMOUNT?$sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TGT_AMOUNT?(number_format($sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT,1)):0}}%</p>
                      </div>
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:{{$sld->SALES_ZONE_ID}}</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- best contributed super future leader -->
@if($bst_cnt_sf_ldr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Best Contributed Super Future Leaders</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($bst_cnt_sf_ldr as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TOTAL_TGT_AMNT?$sld->TOTAL_P_AMNT*100/$sld->TOTAL_TGT_AMNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TOTAL_TGT_AMNT?(number_format($sld->TOTAL_P_AMNT*100/$sld->TOTAL_TGT_AMNT,1)):0}}%</p>
                      </div>
                      
                    </div>
                    <div id="{{'prop'.$sld->STAFFID}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- Hatrick Champion-->
@if($hatrick)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Hat Trick Champion</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($hatrick as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TGT_AMNT?$sld->P_SALES_AMNT*100/$sld->TGT_AMNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TGT_AMNT?(number_format($sld->P_SALES_AMNT*100/$sld->TGT_AMNT,1)):0}}%</p>
                      </div>
                    </div>
                    <div id="{{'prop'.$sld->STAFFID}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:{{$sld->SALES_ZONE_ID}}</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- super manager-->
@if($s_mngr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Super Manager</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($s_mngr as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID.$sld->oid}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID.$sld->oid}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TGT_AMOUNT?$sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TGT_AMOUNT?(number_format($sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT,1)):0}}%</p>
                      </div>
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:{{$sld->SALES_ZONE_ID}}</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- super Hero-->
@if($s_hero)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Super Hero</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($s_hero as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID.$sld->oid}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID.$sld->oid}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TGT_AMOUNT?$sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TGT_AMOUNT?(number_format($sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT,1)):0}}%</p>
                      </div>
                     
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:{{$sld->SALES_ZONE_ID}}</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
<!-- super Hero-->
@if($hero)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Hero</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($hero as $sld)
        <div class="col-md-3 col-sm-3 col-xs-12  animate__animated animate__zoomIn">
              <div class="ld-container leader-card">
                @if($sld->SPRO_IMG=='N')
                <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="leader-img">
                @else
                 <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="leader-img">
                @endif
                 <!-- <img src="https://images.sihirbox.com/{{ Auth::user()->employee()->aemp_picn}}"
                                     alt="" class="leader-img"> -->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="{{'#stat'.$sld->STAFFID.$sld->oid}}">Statistics</a></li>
                    <li><a data-toggle="tab" href="{{'#prop'.$sld->STAFFID.$sld->oid}}">Profile</a></li>
                    
                  </ul>

                  <div class="tab-content">
                    <div id="{{'stat'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade in active">
                      <div class="pg-block">
                        <p class="pg-content">T</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100%</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">P.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->TGT_AMOUNT?$sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT:0}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->TGT_AMOUNT?(number_format($sld->ACHV_AMOUNT*100/$sld->TGT_AMOUNT,1)):0}}%</p>
                      </div>
                      
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade">
                      <p>Name:  {{$sld->EMPLOYEENAME}}</p>
                      <p>Staff ID:  {{$sld->STAFFID}}</p>
                      <p>Company:{{$sld->COMPANY_ID}} ||  Zone:{{$sld->SALES_ZONE_ID}}</p>
                    
                    </div>
                   
                  </div>
              </div>
        </div>
        @endforeach  
      </div>
    </div>
  </div>
  </div>
@endif
</div>
</div>
<script>
 function getData(){
  var result= jQuery('#toggleBtn').is(':checked')?1:0;
  if(result==1){
    $('#ajax_load').css('display','block');
    $.ajax({
    type:"get",
    url:"{{URL::to('/')}}/getSecondarySalesNationalLeaders/"+result,
    cache: false,
    success:function(data){
      $('#ajax_load').css('display','none');
      $('#sales_header').empty();
      $('#sales_header').append("Secondary Sales LeaderBoard");
      $('#leader_section').empty();
      $('#leader_section').append(data);
      $('#instruction').empty();
      $('#instruction').append("Switch Off To get Primary Sales ");
    },error:function(error){
      console.log(error);
    }
  });
  }else{
    $('#instruction').empty();
    $('#instruction').append("Switch On To get Secondary Sales ");
    $('#sales_header').empty();
    $('#sales_header').append("Primary Sales LeaderBoard");
    window.location.href="national_leaders";
  }
 }
</script>
@endsection