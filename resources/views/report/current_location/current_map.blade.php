@extends('theme.map')
@section('content')
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: {lat: 23.7945961, lng:90.4479657},
                mapTypeId: 'terrain'
            });
            var green = new google.maps.MarkerImage(
                'http://maps.google.com/mapfiles/ms/micons/green-dot.png',
                new google.maps.Size(92, 32),   // size
                new google.maps.Point(0, 0), // origin
                new google.maps.Point(16, 32)   // anchor
            );
            var yellow = new google.maps.MarkerImage(
                'http://maps.google.com/mapfiles/ms/micons/yellow-dot.png',
                new google.maps.Size(95, 40),   // size
                new google.maps.Point(0, 0), // origin
                new google.maps.Point(16, 32)   // anchor
            );
            var red = new google.maps.MarkerImage(
                'http://maps.google.com/mapfiles/ms/micons/red-dot.png',
                new google.maps.Size(95, 40),   // size
                new google.maps.Point(0, 0), // origin
                new google.maps.Point(16, 32)   // anchor
            );

            var locations = [
                    @foreach($locations as $location)
                ['<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<a target="_blank" href="{{ URL::to('/')}}/attendance/summaryLocationAll/{{$location->emp_id}}/{{$location->date}}"><h1 id="firstHeading" class="firstHeading">{{$location->name}}</h1><a/>'+

                '<div id="bodyContent">' +
                '<b> Role:  {{$location->role}} </b><br>' +
                '<b> User Name:  {{$location->user_name}} </b><br>' +
                '<b> Mobile:  {{$location->mobile}} </b><br>' +
                '<b> Last Time:  {{$location->time." ".$location->date}} </b><br>' +
                '</div>' +
                '</div>', {{$location->lat}}, {{$location->lon}}, '{{$location->dif}}', '{{$location->user_name}}'],
                    @endforeach
            ];



            var infowindow = new google.maps.InfoWindow();

            var marker, i, marker1;

            for (i = 0; i < locations.length; i++) {
                console.log(locations[i][4]);
                var lavel = '' + locations[i][4];
                if (locations[i][3] < 20) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                        label: {
                            text: lavel,
                            color: 'red',
                            style: 'bold'
                        },
                        icon: green
                    });

                } else if (locations[i][3] > 20 && locations[i][3] < 60) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                        label: {
                            text: lavel,
                            color: 'red',
                            style: 'bold'
                        },
                        icon: yellow
                    });

                } else {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                        label: {
                            text: lavel,
                            color: 'red',
                            style: 'bold'
                        },
                        icon: red
                    });
                }


                google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                    return function () {
                        infowindow.setContent(locations[i][0]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
                google.maps.event.addListener(marker, 'mouseout', (function (marker, i) {
                    return function () {
                        // infowindow.close();
                    }
                })(marker, i));
            }
            var i, marker1;


        }
    </script>

@endsection