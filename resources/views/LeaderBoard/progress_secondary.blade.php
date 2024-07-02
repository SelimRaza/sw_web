@extends('theme.app')
@section('content')
<div class="right_col" role="main">
<div class="col-md-12 col-xs-12 col-sm-12">
  <div class="col-md-9">
      <h1 style="font-family:New Century Schoolbook, TeX Gyre Schola, serif;" id="sales_header">Secondary Sales LeaderBoard</h1>
  </div>
  <div class="col-md-2">
      <p id="instruction" style="margin-top:18px;float:right;">Switch Off To get Primary Sales</p>
  </div>
  <div class="checkbox col-md-1" style="float:left;">
      <label>
      <input type="checkbox" data-toggle="toggle"  id="toggleBtn" onchange="getData()" checked>
      <span class="switch-label" data-on="SS" data-off="PS"></span> 
      </label>
  </div>
</div>
  <!-- progessive super leader -->
@if($ps_ldr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Progressive Super Leaders(Top 10)</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($ps_ldr as $sld)
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
                          <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;100</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">S.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->ACHV_PERCENTAGE}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->ACHV_PERCENTAGE}}%</p>
                      </div>
                    </div>
                    <div id="{{'prop'.$sld->STAFFID}}" class="tab-pane fade pro">
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
<!-- progressive super leader percentage -->
@if($ps_ldr_percnt)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Progressive Super Leader(Top 10 in %)</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($ps_ldr_percnt as $sld)
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
                        <p class="pg-content">Achv</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->ACHV_PERCENTAGE}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->ACHV_PERCENTAGE}}%</p>
                      </div>
                     

                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade pro">
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
<!-- progressive super future leader -->
@if($psf_ldr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Progressive Super Future Leaders(Top 10)</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($psf_ldr as $sld)
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
                      <p class="pg-content_amnt">&nbsp;&nbsp;100</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">S.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->ACHV_PERCENTAGE}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->ACHV_PERCENTAGE}}%</p>
                      </div>
                      
                    </div>
                    <div id="{{'prop'.$sld->STAFFID}}" class="tab-pane fade pro">
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
<!-- progressive super future leader percentage-->
@if($psf_ldr_percnt)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Progressive Super Future Leaders(Top 10 in %)</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($psf_ldr_percnt as $sld)
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
                        <p class="pg-content">Achv</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->ACHV_PERCENTAGE}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->ACHV_PERCENTAGE}}%</p>
                      </div>
                     
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade pro">
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
<!-- progressive manager-->
@if($ps_mngr)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Progressive Super Manager(Top 10)</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($ps_mngr as $sld)
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
                      <p class="pg-content_amnt">&nbsp;&nbsp;100</p>
                      </div>
                      <div class="pg-block">
                        <p class="pg-content">S.S</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped" role="progressbar" style="width:{{$sld->ACHV_PERCENTAGE}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                        <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->ACHV_PERCENTAGE}}%</p>
                      </div>
                      
                    </div>
                    <div id="{{'prop'.$sld->STAFFID}}" class="tab-pane fade pro">
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
<!-- progressive super manager  percentage-->
@if($ps_mngr_percnt)
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="x_panel tile">
      <div class="x_title">
        <h2>Progressive Super Manager(Top 10 in %)</h2>
        <ul class="nav navbar-right panel_toolbox">
          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
          </li>
          <li><a class="close-link"><i class="fa fa-close"></i></a>
          </li>
        </ul>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        @foreach($ps_mngr_percnt as $sld)
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
                        <p class="pg-content">Achv</p>
                        <div class="progress pg-content-pg">
                          <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width:{{$sld->ACHV_PERCENTAGE}}%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                       </div>
                      <p class="pg-content_amnt">&nbsp;&nbsp;{{$sld->ACHV_PERCENTAGE}}%</p>
                      </div>
                     
                    </div>
                    <div id="{{'prop'.$sld->STAFFID.$sld->oid}}" class="tab-pane fade pro">
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
<script>
  function getData(){
  var result= jQuery('#toggleBtn').is(':checked')?1:0;
  if(result==1){
    window.location.href="{{URL::to('/')}}/progressive_leader_board/secondary";
  }else{
    window.location.href="{{URL::to('/')}}/progressive_leader_board";
   
  }
 }
</script>
@endsection