const apiKey='AIzaSyAUz9b1JjhtFMPkg4scrdW2uAbLfGyc3d4';
var map;
var polylines = [];
function showVisitMap(id, v, place) {
    $("#visitMap").modal({backdrop: false});
    $('#visitMap').modal('show');
    var _token = $("#_token").val();
    var date = $(v).attr("date");
    var sr_id = $(v).attr("sr_id");
    $('#visit_map_loader').show();
    $.ajax({
        type: "POST",
        url: "/getMap",
        data: {
            sr_id: sr_id,
            date: date,
            stage: place,
            _token: _token,
        },
        dataType: "json",
        success: function (result) {
            console.log(result);
            $('#visit_map_loader').hide();
            var data=result.data;
            var rsp=result.rout_site;
            var npv = window.location.origin;
            var pv = window.location.origin;
            var nv = window.location.origin;
            var loc = window.location.origin;
            var end = window.location.origin;
            var start = window.location.origin;
            npv += "/theme/image/map_icon_all/npv.png";
            pv += "/theme/image/map_icon_all/pv.png";
            nv += "/theme/image/map_icon_all/nv.png";
            loc += "/theme/image/map_icon_all/point.png";
            start += "/theme/image/map_icon_all/start.png";
            end += "/theme/image/map_icon_all/end.png";
            var hloc_geo = [];
            var lon = [];
            var visit = [];
            var site_h=[];
            var p_line_data=[];
            for (var i = 0; i < data.length; i++) {
                if(data[i]['site_name']=='0'){
                    var b = {'lat': data[i]['geo_lat'], 'lng': data[i]['geo_lon']};
                    hloc_geo.push(b);
                    p_line_data.push(b);
                }
                else{
                    var b= {'site_code': data[i]['site_code'], 'site_name': data[i]['site_name'],'site_adrs': data[i]['site_adrs'],'geo_lat': data[i]['geo_lat'],'geo_lon': data[i]['geo_lon']};
                    site_h.push(b);
                    p_line_data.push({'lat': data[i]['geo_lat'], 'lng': data[i]['geo_lon']});
                }
                
            }
            var  rs = rsp.filter(ad => 
                        site_h.every(fd => fd.site_code !==ad.site_code));
            if(hloc_geo.length==0){
                for (var i = 0; i < data.length; i++) {
                    var b = {'lat': data[i]['geo_lat'], 'lng': data[i]['geo_lon']};
                    hloc_geo.push(b);
                }
            }
             map = new google.maps.Map(document.getElementById('googleMap'), {
                    zoom: 15,
                    center: new google.maps.LatLng(hloc_geo[0]['lat'], hloc_geo[0]['lng']),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var marker;
            for (var b=0;b<rs.length;b++){
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(rs[b]['geo_lat'], rs[b]['geo_lon']),
                    map: map
                });
                marker.setIcon(nv);
                google.maps.event.addListener(marker, 'click', (function (marker, b) {
                    return function () {
                        var info = '<div class="text-center"><p>' + rs[b]['site_name'] + '</p>' +
                            '<p>' + rs[b]['site_adrs'] + '</p></div>';
                        infowindow.setContent(info);
                        infowindow.open(map, marker);
                    }
                })(marker, b));
            }
            const lineSymbol = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                strokeColor: "black",
            };
            var infowindow = new google.maps.InfoWindow();
            var marker, i;
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(data[0]['geo_lat'], data[0]['geo_lon']),
                map: map
            });
            marker.setIcon(start);
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    var info = '<div class="text-center"><p>' + data[0]['site_name'] + '</p>' +
                        '<p>' + data[0]['site_adrs'] + '</p></div>' +
                        '<p>' + data[0]['log_time'] + '</p></div>';
                    infowindow.setContent(info);
                    infowindow.open(map, marker);
                }
            })(marker, i));
            for (i = 1; i < data.length; i++) {

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(data[i]['geo_lat'], data[i]['geo_lon']),
                    map: map
                });
                if(data[i]['site_name'] =='0'){
                    marker.setIcon(loc);
                }else{
                    if(data[i]['v_type']==1){
                        marker.setIcon(pv);
                    }
                    else{
                        marker.setIcon(npv);
                    }
                    
                }
                if (i == (data.length - 1)) {
                    marker.setIcon(end);
                }
                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        var info = '<div class="text-center"><p>' + data[i]['site_name'] + '</p>' +
                            '<p>' + data[i]['site_adrs'] + '</p></div>' +
                            '<p>' + data[i]['log_time'] + '</p></div>';
                        infowindow.setContent(info);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            
            // var flightPath = new google.maps.Polyline({
            //     path:p_line_data,
            //     strokeColor: "skyblue",
            //     strokeOpacity: 2,
            //     strokeWeight:8,
            //     icons: [
            //         {
            //             icon: lineSymbol,
            //             offset: "100%",
            //         },
            //     ],
            //     map: map,
            // });
            // Start :- Snap To Road API
            // console.log(p_line_data[0]);
            // var service = new google.maps.DirectionsService();
            // var directionsDisplay = new google.maps.DirectionsRenderer();    
            // directionsDisplay.setMap(map);
            // var waypts = [];
            // for(j=1;j<p_line_data.length-1;j++){            
            //     waypts.push({location: p_line_data[j],
            //                 stopover: true});
            // }
            // var request = {
            //     origin: p_line_data[0],
            //     destination: p_line_data[p_line_data.length-1],
            //     waypoints: waypts,
            //     travelMode: google.maps.DirectionsTravelMode.DRIVING
            // };
            // service.route(request,function(result, status) {                
            //     if(status == google.maps.DirectionsStatus.OK) {                 
            //         directionsDisplay.setDirections(result);
            //     } else { alert("Directions request failed:" +status); }
            // });

            // The END of Road API
            runSnapToRoad(p_line_data);
        }, 
        error: function (error) {
            console.log(error);
        }

    });


}
function runSnapToRoad(path) {
    var pathValues = [];
    let length=path.length;
    for (var i = 0; i < path.length; i++) {
        var  d=path[i]['lat']+','+path[i]['lng'];
      pathValues.push(d);
    }
  if(length<=100){
    $.get('https://roads.googleapis.com/v1/snapToRoads', {
      interpolate: true,
      key: apiKey,
      path: pathValues.join('|')
    }, function(data) {
      processSnapToRoadResponse(data);
      drawSnappedPolyline();
    });
  }
  else{
        var pathValue1=pathValues.slice(0,99);
        var pathValue2=[];
        var pathValue3=[];
        var pathValue4=[];
        if(length<=200){
            pathValue2=pathValues.slice(100,length-1);
        }
        else if(length<=300){
            pathValue2=pathValues.slice(100,199);
            pathValue3=pathValues.slice(200,length-1);
        }
        else if(length<=400){
            pathValue2=pathValues.slice(100,199);
            pathValue3=pathValues.slice(200,299);
            pathValue4=pathValues.slice(300,length-1);
        }
        
        $.get('https://roads.googleapis.com/v1/snapToRoads', {
            interpolate: true,
            key: apiKey,
            path: pathValue1.join('|')
            }, function(data) {
            processSnapToRoadResponse(data);
            drawSnappedPolyline();
        });
        $.get('https://roads.googleapis.com/v1/snapToRoads', {
            interpolate: true,
            key: apiKey,
            path: pathValue2.join('|')
            }, function(data) {
            processSnapToRoadResponse(data);
            drawSnappedPolyline();
        });
        if(pathValue3.length>0){
            $.get('https://roads.googleapis.com/v1/snapToRoads', {
                interpolate: true,
                key: apiKey,
                path: pathValue3.join('|')
                }, function(data) {
                processSnapToRoadResponse(data);
                drawSnappedPolyline();
            });
        }
        if(pathValue4.length>0){
            $.get('https://roads.googleapis.com/v1/snapToRoads', {
                interpolate: true,
                key: apiKey,
                path: pathValue4.join('|')
                }, function(data) {
                processSnapToRoadResponse(data);
                drawSnappedPolyline();
            });
        }
  }
    
  }
  function processSnapToRoadResponse(data) {
    snappedCoordinates = [];
    placeIdArray = [];
    var unique = [];
    // for( let i = 0; i < data.snappedPoints.length; i++ ){
    //     if( !unique[data.snappedPoints[i].placeId]){
    //         var latlng = new google.maps.LatLng(
    //             data.snappedPoints[i].location.latitude,
    //             data.snappedPoints[i].location.longitude);
    //         snappedCoordinates.push(latlng);
    //         placeIdArray.push(data.snappedPoints[i].placeId);
    //         unique[data.snappedPoints[i].placeId] = 1;
    //     }
    // }
    // console.log(data.snappedPoints);
    for (var i = 0; i < data.snappedPoints.length; i++) {
      var latlng = new google.maps.LatLng(
          data.snappedPoints[i].location.latitude,
          data.snappedPoints[i].location.longitude);
      snappedCoordinates.push(latlng);
      placeIdArray.push(data.snappedPoints[i].placeId);
    }
  }
  function drawSnappedPolyline() {
    var snappedPolyline = new google.maps.Polyline({
      path: snappedCoordinates,
      strokeColor: 'skyblue',
      strokeWeight: 4,
      strokeOpacity: 0.9,
    });
  
    snappedPolyline.setMap(map);
    polylines.push(snappedPolyline);
  }
  function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
  }

function animateCircle(line) {
    let count = 0;

    window.setInterval(() => {
        count = (count + 1) % 200;
        const icons = line.get("icons");
        icons[0].offset = count / 2 + "%";
        line.set("icons", icons);
    }, 120);
}


