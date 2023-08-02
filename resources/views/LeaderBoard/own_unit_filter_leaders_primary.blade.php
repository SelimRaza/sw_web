 
<!-- super leader -->
<div class="row">

<!-- best contributed super leader -->
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="col-md-2 col-sm-2 col-xs-12 float-right" style="float:right;margin-bottom:8px;">
        <input type="text" id="staff_id" class="form-control" placeholder="Search By Staff ID" staff_id>
    </div>
</div>
 @if($s_mngr)
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel tile">
      <div class="x_title">
            <h3 style="text-align:center!important;">Super Manager</h3>
      </div>
      <div class="x_content">
           <!-- top 3 start -->
           @php
            $i=1;
           @endphp
           <div class="col-sm-3 col-md-3 col-lg-3"></div>
           @foreach($s_mngr as $sld)
           @if($i<4)
            <div class="col-sm-2 col-md-2 col-xs-12 leader" style="margin-bottom:5px;">
                <div class="card h-100 shadow-sm">
                    @if($sld->SPRO_IMG=='N'||$sld->SPRO_IMG=='')
                    <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="card-img-top">
                    @else
                    <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="card-img-top">
                    @endif
                     <!-- <img src="{{ asset("theme/production/images/img.jpg")}}" class="card-img-top" alt="..."> -->
                    <div class="label-top shadow-sm text-center">Position: {{$i}}</div>
                    <div class="card-body text-center" style="padding:10px;">
                        <div class="clearfix mb-3"><h5><p>{{$sld->EMPLOYEENAME}}</p></h5></div>
                        <h5 class="card-title">
                            <p>Staff ID: {{$sld->STAFFID}}</p>
                            <p>Group:{{$sld->SLGP_NAME}}</p>
                            <p>Zone:{{$sld->ZONE_NAME}}</p>
                        </h5> 
                    </div>
                </div>
            </div>
            @endif
            @if($i==4)
            <div class="col-sm-3 col-md-3 col-lg-3"></div>
            <div class="clearfix"></div>  
            <div class="" style="height:20px;"></div> 
  
            <!-- top 3 End -->
            <div class="col-md-12 col-sm-12 col-xs-12 single_btm_usr_div card h-100 shadow-sm leader" style="margin-bottom:5px;">
                <div class="card-body" style="padding:10px;">
                    <div class="col-sm-1 col-md-1 col-xs-1"><h5 class="rank_position">{{$i}}th</h5></div>
                    <div class="col-sm-2 col-md-2 col-xs-10">
                        <img src="{{ asset("theme/production/images/img.jpg")}}"  alt="..." style="object-fit:contain;" class="img img-circle" height="60" width="100">
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10 ">
                       <h5> <p class="float-start price-hp card-title">Name:{{$sld->EMPLOYEENAME}}</p>
                        <p class="card-title">Staff ID: {{$sld->STAFFID}}</p>
                        </h5>
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10" style="float:right;">
                    <h5>Group:{{$sld->SLGP_NAME}}</h5>
                    <h5>Zone:{{$sld->ZONE_NAME}}</h5>
                </div>  
                </div>
            </div>
            @elseif($i>4)
            <div class="col-md-12 col-sm-12 col-xs-12 single_btm_usr_div card h-100 shadow-sm leader" style="margin-bottom:5px;">
                <div class="card-body" style="padding:10px;">
                    <div class="col-sm-1 col-md-1 col-xs-1"><h5 class="rank_position">{{$i}}th</h5></div>
                    <div class="col-sm-2 col-md-2 col-xs-10">
                        @if($sld->SPRO_IMG=='N'||$sld->SPRO_IMG=='')
                            <img src="{{ asset("theme/production/images/img.jpg")}}"  alt="..." style="object-fit:contain;" class="img img-circle" height="60" width="100">
                        @else
                        <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" style="object-fit:contain;" class="img img-circle" height="60" width="100">
                        @endif
                        
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10 ">
                       <h5> <p class="float-start price-hp card-title">Name: {{$sld->EMPLOYEENAME}}</p>
                        <p class="card-title">Staff ID: {{$sld->STAFFID}}</p>
                        </h5>
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10" style="float:right;"><h5>Group: {{$sld->SLGP_NAME}}</h5><h5>Zone:{{$sld->ZONE_NAME}}</h5></div>  
                </div>
            </div>
            @endif 
            <?php $i++;
            ?>
            @endforeach       
      </div>
    </div>
 </div>
 @endif

 @if($s_hero)
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel tile">
      <div class="x_title">
            <h3 style="text-align:center!important;">Super Hero</h3>
      </div>
      <div class="x_content">
           <!-- top 3 start -->
           @php
            $i=1;
           @endphp
           <div class="col-sm-3 col-md-3 col-lg-3"></div>
           @foreach($s_hero as $sld)
           @if($i<4)
                <div class="col-sm-2 col-md-2 col-xs-12 leader" style="margin-bottom:5px;">
                <div class="card h-100 shadow-sm">
                    @if($sld->SPRO_IMG=='N'||$sld->SPRO_IMG=='')
                    <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="card-img-top">
                    @else
                    <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="card-img-top">
                    @endif
                     <!-- <img src="{{ asset("theme/production/images/img.jpg")}}" class="card-img-top" alt="..."> -->
                    <div class="label-top shadow-sm text-center">Position: {{$i}}</div>
                    <div class="card-body text-center" style="padding:10px;">
                        <div class="clearfix mb-3"><h5>{{$sld->EMPLOYEENAME}}</h5></div>
                        <h5 class="card-title">
                            <p>Staff ID: {{$sld->STAFFID}}</p>
                            <p>Group:{{$sld->SLGP_NAME}}</p>
                            <p>Zone:{{$sld->ZONE_NAME}}</p>
                        </h5> 
                    </div>
                </div>
            </div>
            @endif
            @if($i==4)
            <div class="col-sm-3 col-md-3 col-lg-3"></div>
            <div class="clearfix"></div>  
            <div class="" style="height:20px;"></div> 
  
            <!-- top 3 End -->
            <div class="col-md-12 col-sm-12 col-xs-12 single_btm_usr_div card h-100 shadow-sm leader" style="margin-bottom:5px;">
                <div class="card-body" style="padding:10px;">
                    <div class="col-sm-1 col-md-1 col-xs-1"><h5 class="rank_position">{{$i}}th</h5></div>
                    <div class="col-sm-2 col-md-2 col-xs-10">
                        <img src="{{ asset("theme/production/images/img.jpg")}}"  alt="..." style="object-fit:contain;" class="img img-circle" height="60" width="100">
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10 ">
                       <h5> <p class="float-start price-hp card-title">Name:{{$sld->EMPLOYEENAME}}</p>
                        <p class="card-title">Staff ID: {{$sld->STAFFID}}</p>
                        </h5>
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10" style="float:right;"><h5>Group:{{$sld->SLGP_NAME}}</h5><h5>Zone:{{$sld->ZONE_NAME}}</h5></div>  
                </div>
            </div>
            @elseif($i>4)
            <div class="col-md-12 col-sm-12 col-xs-12 single_btm_usr_div card h-100 shadow-sm leader" style="margin-bottom:5px;">
                <div class="card-body" style="padding:10px;">
                    <div class="col-sm-1 col-md-1 col-xs-1"><h5 class="rank_position">{{$i}}th</h5></div>
                    <div class="col-sm-2 col-md-2 col-xs-10">
                        @if($sld->SPRO_IMG=='N'||$sld->SPRO_IMG=='')
                            <img src="{{ asset("theme/production/images/img.jpg")}}"  alt="..." style="object-fit:contain;" class="img img-circle" height="60" width="100">
                        @else
                        <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" style="object-fit:contain;" class="img img-circle" height="60" width="100">
                        @endif
                        
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10 ">
                       <h5> <p class="float-start price-hp card-title">Name: {{$sld->EMPLOYEENAME}}</p>
                        <p class="card-title">Staff ID: {{$sld->STAFFID}}</p>
                        </h5>
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10" style="float:right;"><h5>Group: {{$sld->SLGP_NAME}}</h5><h5>Zone:{{$sld->ZONE_NAME}}</h5></div>  
                </div>
            </div>
            @endif 
            <?php $i++;
            ?>
            @endforeach       
      </div>
    </div>
 </div>
 @endif
 
 @if($hero)
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel tile">
      <div class="x_title">
            <h3 style="text-align:center!important;">Hero</h3>
      </div>
      <div class="x_content">
           <!-- top 3 start -->
           @php
            $i=1;
           @endphp
           <div class="col-sm-3 col-md-3 col-lg-3"></div>
           @foreach($hero as $sld)
           @if($i<4)
                <div class="col-sm-2 col-md-2 col-xs-12 leader" style="margin-bottom:5px;">
                <div class="card h-100 shadow-sm">
                    @if($sld->SPRO_IMG=='N'||$sld->SPRO_IMG=='')
                    <img src="{{ asset('theme/production/images/img.jpg')}}" alt="" class="card-img-top">
                    @else
                    <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" class="card-img-top">
                    @endif
                     <!-- <img src="{{ asset("theme/production/images/img.jpg")}}" class="card-img-top" alt="..."> -->
                    <div class="label-top shadow-sm text-center">Position: {{$i}}</div>
                    <div class="card-body text-center" style="padding:10px;">
                        <div class="clearfix mb-3"><h5>{{$sld->EMPLOYEENAME}}</h5></div>
                        <h5 class="card-title">
                            <p>Staff ID: {{$sld->STAFFID}}</p>
                            <p>Group:{{$sld->SLGP_NAME}}</p>
                            <p>Zone:{{$sld->ZONE_NAME}}</p>
                        </h5> 
                    </div>
                </div>
            </div>
            @endif
            @if($i==4)
            <div class="col-sm-3 col-md-3 col-lg-3"></div>
            <div class="clearfix"></div>  
            <div class="" style="height:20px;"></div> 
  
            <!-- top 3 End -->
            <div class="col-md-12 col-sm-12 col-xs-12 single_btm_usr_div card h-100 shadow-sm leader" style="margin-bottom:5px;">
                <div class="card-body" style="padding:10px;">
                    <div class="col-sm-1 col-md-1 col-xs-1"><h5 class="rank_position">{{$i}}th</h5></div>
                    <div class="col-sm-2 col-md-2 col-xs-10">
                        <img src="{{ asset("theme/production/images/img.jpg")}}"  alt="..." style="object-fit:contain;" class="img img-circle" height="60" width="100">
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10 ">
                       <h5> <p class="float-start price-hp card-title">Name:{{$sld->EMPLOYEENAME}}</p>
                        <p class="card-title">Staff ID: {{$sld->STAFFID}}</p>
                        </h5>
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10" style="float:right;"><h5>Group:{{$sld->SLGP_NAME}}</h5><h5>Zone:{{$sld->ZONE_NAME}}</h5></div>  
                </div>
            </div>
            @elseif($i>4)
            <div class="col-md-12 col-sm-12 col-xs-12 single_btm_usr_div card h-100 shadow-sm leader" style="margin-bottom:5px;">
                <div class="card-body" style="padding:10px;">
                    <div class="col-sm-1 col-md-1 col-xs-1"><h5 class="rank_position">{{$i}}th</h5></div>
                    <div class="col-sm-2 col-md-2 col-xs-10">
                        @if($sld->SPRO_IMG=='N'||$sld->SPRO_IMG=='')
                            <img src="{{ asset("theme/production/images/img.jpg")}}"  alt="..." style="object-fit:contain;" class="img img-circle" height="60" width="100">
                        @else
                        <img src="https://images.sihirbox.com/{{$sld->SPRO_IMG}}" style="object-fit:contain;" class="img img-circle" height="60" width="100">
                        @endif
                        
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10 ">
                       <h5> <p class="float-start price-hp card-title">Name: {{$sld->EMPLOYEENAME}}</p>
                        <p class="card-title">Staff ID: {{$sld->STAFFID}}</p>
                        </h5>
                    </div>
                    <div class="col-sm-2 col-md-2 col-xs-10" style="float:right;"><h5>Group: {{$sld->SLGP_NAME}}</h5><h5>Zone:{{$sld->ZONE_NAME}}</h5></div>  
                </div>
            </div>
            @endif 
            <?php $i++;
            ?>
            @endforeach       
      </div>
    </div>
 </div>
 @endif
</div>
</div>
<style>
/* .rank_position{
    border-radius:60%;
    background-color:red;
    color:white;
} */
 h2,h3,h1 {
    font-weight: 600
}
.container-fluid {
    max-width: 1200px
}
.card {
    background: #fff;
    box-shadow: 0 6px 10px rgba(0, 0, 0, .08), 0 0 6px rgba(0, 0, 0, .05);
    transition: .3s transform cubic-bezier(.155, 1.105, .295, 1.12), .3s box-shadow, .3s -webkit-transform cubic-bezier(.155, 1.105, .295, 1.12);
    border: 0;
    border-radius: 1rem
}
.card-img,
.card-img-top {
    border-top-left-radius: calc(1rem - 1px);
    border-top-right-radius: calc(1rem - 1px)
}
.card h5 {
    overflow: hidden;
    /* height: 40px; */
    font-weight: 900;
    font-size: 1rem
}
.card-img-top {
    width: 100%;
    max-height: 180px;
    object-fit:contain;
    padding:10px
}
.card h2 {
    font-size: 1rem
}

/* .card:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0, 0, 0, .12), 0 4px 8px rgba(0, 0, 0, .06)
} */

.label-top {
    /* position: absolute;
    background-color: #8bc34a;
    color: #fff;
    top: 8px;
    right: 8px;
    padding: 5px 10px 5px 10px;
    font-size: .7rem;
    font-weight: 600;
    border-radius: 3px;
    text-transform: uppercase;
 */
    
    /* position: absolute; */
  top: -10px;
  right: -10px;
  padding: 5px 10px;
  border-radius: 50%;
  background:#92A9BD;
  color:white;
  font-family: 'Brush Script MT', cursive;
}

.top-right {
    position: absolute;
    top: 24px;
    left: 24px;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    font-size: 1rem;
    font-weight: 900;
    background: #ff5722;
    line-height: 90px;
    text-align: center;
    color: white
}

.top-right span {
    display: inline-block;
    vertical-align: middle
}

@media (max-width: 768px) {
    .card-img-top {
        max-height: 250px
    }
}
.hover-bg {
    background: rgba(53, 53, 53, 0.85);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    backdrop-filter: blur(0.0px);
    -webkit-backdrop-filter: blur(0.0px);
    border-radius: 10px
}

.box {
    border-radius: 1rem;
    background: #fff;
    box-shadow: 0 6px 10px rgb(0 0 0 / 8%), 0 0 6px rgb(0 0 0 / 5%);
    transition: .3s transform cubic-bezier(.155, 1.105, .295, 1.12), .3s box-shadow, .3s -webkit-transform cubic-bezier(.155, 1.105, .295, 1.12)
}
.box-img {
    max-width: 300px
}
.thumb-sec {
    max-width: 300px
}

@media (max-width: 576px) {
    .box-img {
        max-width: 200px
    }

    .thumb-sec {
        max-width: 200px
    }
}
.inner-gallery {
    width: 60px;
    height: 60px;
    border: 1px solid #ddd;
    border-radius: 3px;
    margin: 1px;
    display: inline-block;
    overflow: hidden;
    -o-object-fit: cover;
    object-fit: cover
}
@media (max-width: 370px) {
    .box .btn {
        padding: 5px 40px 5px 40px;
        font-size: 1rem
    }
}

.disclaimer {
    font-size: .9rem;
    color: darkgray
}

.related h3 {
    font-weight: 900
}
</style>

<script>
$('#staff_id').on('keyup', function() {
	var searchVal = $(this).val();
	var filterItems = $('.leader');

	if ( searchVal != '' ) {
		filterItems.addClass('hidden');
       // $('.leader:contains("'+searchVal+'")').show();
       $('.leader p:contains("'+searchVal+'")').closest('.leader').css('background-color','#acc6aa');
       $('.leader p:contains("'+searchVal+'")').closest('.leader').removeClass('hidden');
	} else {
		filterItems.removeClass('hidden').css('background','');
	}
});
</script>
