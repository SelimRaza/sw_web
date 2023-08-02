@extends('theme.app')
@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>Activity Summary Heat Map</strong>
                        </li>

                    </ol>
                </div>
                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong></strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong></strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">

                    <div class="x_panel">

                        <div class="x_content">
                        <div id="floating-panel">
                            <button id="toggle-heatmap" onclick="toggleHeatmap()">Toggle Heatmap</button>
                            <button id="change-gradient" onclick="changeGradient()">Change gradient</button>
                            <button id="change-radius" onclick="changeRadius()">Change radius</button>
                            <button id="change-opacity" onclick="changeOpacity()">Change opacity</button>
                        </div>
                            <div id="map"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        #map {
        height:650px;
        }
        html,
        body {
        height: 100%;
        margin: 0;
        padding: 0;
        }

        #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: "Roboto", "sans-serif";
        line-height: 30px;
        padding-left: 10px;
        }

        #floating-panel {
        background-color: #fff;
        border: 1px solid #999;
        left: 25%;
        padding: 5px;
        position: absolute;
        top: 10px;
        z-index: 5;
        }

    </style>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAUz9b1JjhtFMPkg4scrdW2uAbLfGyc3d4&libraries=visualization&callback=initMap" async defer></script>
<script>
var data=<?php echo json_encode($data); ?>;
var lat=<?php echo $lat  ?>;
var lng=<?php echo $lng  ?>;
 var map, heatmap;
function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 9,
    center: {lat: lat, lng: lng},
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  heatmap = new google.maps.visualization.HeatmapLayer({
    data: getPoints(),
    map: map
  });
}

function toggleHeatmap() {
  heatmap.setMap(heatmap.getMap() ? null : map);
}

function changeGradient() {
  var gradient = [
    'rgba(0, 255, 255, 0)',
    'rgba(0, 255, 255, 1)',
    'rgba(0, 191, 255, 1)',
    'rgba(0, 127, 255, 1)',
    'rgba(0, 63, 255, 1)',
    'rgba(0, 0, 255, 1)',
    'rgba(0, 0, 223, 1)',
    'rgba(0, 0, 191, 1)',
    'rgba(0, 0, 159, 1)',
    'rgba(0, 0, 127, 1)',
    'rgba(63, 0, 91, 1)',
    'rgba(127, 0, 63, 1)',
    'rgba(191, 0, 31, 1)',
    'rgba(255, 0, 0, 1)'
  ]
  heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
}

function changeRadius() {
  heatmap.set('radius', heatmap.get('radius') ? null : 40);
}

function changeOpacity() {
  heatmap.set('opacity', heatmap.get('opacity') ? null : 2);
}

function getPoints() {
     var heatMapData = [];
    for(var i=0;i<data.length;i++){
        heatMapData.push(new google.maps.LatLng(data[i].geo_lat, data[i].geo_lon));
    }
    return heatMapData;
}
$( document ).ready(function() {
    setTimeout(function () {
        $('#change-radius').click();
        $('#change-opacity').click();
    }, 1);
});
</script>


@endsection