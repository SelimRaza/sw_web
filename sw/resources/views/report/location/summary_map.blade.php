@extends('theme.map')
@section('content')
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: {lat: 23.7945961, lng:90.4479657},
                mapTypeId: 'terrain'
            });
            var image = new google.maps.MarkerImage(
                'http://maps.google.com/mapfiles/ms/micons/yellow-dot.png',
                new google.maps.Size(32, 32),
                new google.maps.Point(0,0),
                new google.maps.Point(16, 32)
            );
            var image2="http://maps.google.com/mapfiles/ms/micons/green-dot.png";
            var image4="http://my.sihirfms.com/group_head/img/location_icon.png";
            var image3="http://my.sihirfms.com/group_head/img/location_from.png";
            var image1 = new google.maps.MarkerImage(
                'http://maps.google.com/mapfiles/ms/micons/yellow-dot.png',
                new google.maps.Size(32, 32),
                new google.maps.Point(0,0),
                new google.maps.Point(16, 32)
            );

            var locations = [
                    @foreach($attendances as $attendance)
                ['<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<h1 id="firstHeading" class="firstHeading">{{$attendance->time.' '.$attendance->date}}</h1>' +
                '<div id="bodyContent">' +
                '</div>', {{$attendance->lat}}, {{$attendance->lon}}, {{$attendance->emp_id}}],
                @endforeach
            ];




            var locations1 = [
                    {{--@foreach($notes as $note)
                ['<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<h1 id="firstHeading" class="firstHeading">{{$note->time.' '.$note->date}} {{$note->title}}</h1>' +
                '</div>', {{$note->lat}}, {{$note->lon}}, 1],
                @endforeach--}}
            ];

            var flightPlanCoordinates = [
                    @foreach($attendances as $attendance)
                {
                    lat: {{ $attendance->lat }}, lng: {{$attendance->lon}}
                },
                @endforeach
            ];
            var lineSymbol = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                strokeColor: '#ffff0d'
            };
            var flightPath = new google.maps.Polyline({
                path: flightPlanCoordinates,
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                icons: [{
                    icon: lineSymbol,
                    offset: '100%'
                }]
            });
            var infowindow = new google.maps.InfoWindow();
            var marker, i,marker1;
            for (i = 0; i < locations.length; i++) {
                if (i==locations.length-1){
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                        icon: image3
                    });
                }else if (i==0){
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                        icon: image4
                    });
                }else {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                    });
                }
                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infowindow.setContent(locations[i][0]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
                google.maps.event.addListener(marker, 'mouseover', (function (marker, i) {
                    return function () {
                        infowindow.setContent(locations[i][0]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }

            var  i,marker1;
            for (i = 0; i < locations1.length; i++) {
                marker1 = new google.maps.Marker({
                    position: new google.maps.LatLng(locations1[i][1], locations1[i][2]),
                    map: map,
                    icon: image2
                });
                google.maps.event.addListener(marker1, 'click', (function (marker1, i) {
                    return function () {
                        infowindow.setContent(locations1[i][0]);
                        infowindow.open(map, marker1);
                    }
                })(marker1, i));

                google.maps.event.addListener(marker1, 'mouseover', (function (marker1, i) {
                    return function () {
                        infowindow.setContent(locations1[i][0]);
                        infowindow.open(map, marker1);
                    }
                })(marker1, i));
            }
            flightPath.setMap(map);
            animateCircle(flightPath);
        }
        function animateCircle(line) {
            var count = 0;
            window.setInterval(function() {
                count = (count + 1) % 200;

                var icons = line.get('icons');
                icons[0].offset = (count / 2) + '%';
                line.set('icons', icons);
            }, 70);
        }
    </script>

@endsection