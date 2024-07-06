@extends('theme.app')
@section('content')
<style type="text/css">
  #map {
    height: 400px;
    width: 100%;
  }
</style>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="">
                            <a href="{{ URL::to('/market_open')}}">All Market</a>
                        </li>
                        <li class="">Create Market</li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong>Success!</strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1 >Market Open</h1>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="{{route('market_open.store')}}"
                                  method="post">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">District
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="district_id" id="district_id" required onchange="getThanaList()">
                                            <option value="">Select</option>
                                            @foreach($districts as $district)
                                            <option value="{{$district->id }}">{{$district->dsct_code}}-{{$district->dsct_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thana
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="thana_id" id="thana_id" required onchange="getMarketList()">
                                            <option value="">Select</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="user_id" id="user_id">
                                            <option value="">Select</option>
                                            @foreach($users as $user)
                                              <option value="{{$user->aemp_iusr}}">{{$user->aemp_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-12">
                                        <input type="button" class="btn btn-info" value="View" id="viewInMap" onclick="loadMapData()">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });

        function initMap() {

          const uluru = { lat: -25.344, lng: 131.036 };
          const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 4,
            center: uluru,
          });

          const marker = new google.maps.Marker({
            position: uluru,
            map: map,
          });

        }

        function getThanaList(){

            var district_id = $("#district_id").val();
            var _token = $("#_token").val();
            $.ajax({
                type: "GET",
                url: "{{ URL::to('/')}}/json/get/market_open/thana_list",
                data: {
                    district_id: district_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#thana_id');
                    if(!data){

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');

                    }else{

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function(key,value) {
                           $el.append($("<option></option>").attr("value", value['id']).text(value['than_code'] + '-' +value['than_name']));
                        });

                    }

                }
            });

        }

        function loadMapData(){
             

            var district_id = $("#district_id").val();
            var thana_id = $("#thana_id").val();
            var user_id = $("#user_id").val();
            var _token = $("#_token").val();
            $.ajax({
                type: "GET",
                url: "{{ URL::to('/')}}/json/load/market",
                data: {
                    district_id: district_id,
                    thana_id: thana_id,
                    user_id: user_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                   //console.log(data);
                   initMap(data);

                }
            });

        } 

        let map;
        function initMap(data){

            var dhaka = {lat:23.8103, lng:90.4125};
            map = new google.maps.Map(document.getElementById("map"), {
              zoom: 12,
              center: dhaka
            });
            
            var marker = new google.maps.Marker({
              position: dhaka,
              map: map
            });
            plotMap(data); 

        }

        function plotMap(data) {

             var infoWind = new google.maps.InfoWindow;
             $.each(data, function (key, value) {

                  var content = document.createElement('div');
                  var strong = document.createElement('strong');
                  strong.textContent = value.site_code+'-'+value.name;
                  content.appendChild(strong);
                  var marker = new google.maps.Marker({

                    position: new google.maps.LatLng(value.lat,value.lng),
                    map: map

                  });

                  marker.addListener('mouseover', function(){
                    infoWind.setContent(content);
                    infoWind.open(map, marker);
                  })
                 
              });

        };

        function getMarketList(){

           var thana_id = $("#ward_id").val();
           var _token = $("#_token").val();
           $.ajax({
                type: "GET",
                url: "{{ URL::to('/')}}/json/get/market_open/word_list",
                data: {
                    thana_id: thana_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#ward_id');
                    if(!data){
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    }else{

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function(key,value) {

                          $el.append($("<option></option>").attr("value", value['id']).text(value['ward_code'] + '-' +value['ward_name']));


                        });
                        
                    }

                }
            });

        }

</script>
@endsection 
    