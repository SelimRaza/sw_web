
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>{{ config('app.name', 'SPRO') }}</title>
    <style>
        #map {
            height: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div id="map"></div>
@yield('content')
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBpAJgpt4GAO3C3HrLlosYOyR71FlT7eno&callback=initMap">
</script>
</body>
</html>