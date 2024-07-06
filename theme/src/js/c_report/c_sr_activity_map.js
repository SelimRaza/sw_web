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
            npv += "/theme/image/map_icon/npv.png";
            pv += "/theme/image/map_icon/pv.png";
            nv += "/theme/image/map_icon/nv.png";
            loc += "/theme/image/map_icon/loc24.png";
            end += "/theme/image/end_1.png";
            console.log('-------------------------------------')
            var hloc_geo = [];
            var lon = [];
            var visit = [];
            var site_h=[];
        

            for (var i = 0; i < data.length; i++) {
                if(data[i]['site_name']=='0'){
                    var b = {'lat': data[i]['geo_lat'], 'lng': data[i]['geo_lon']};
                    hloc_geo.push(b);
                }
                else{
                    var b= {'site_code': data[i]['site_code'], 'site_name': data[i]['site_name'],'site_adrs': data[i]['site_adrs'],'geo_lat': data[i]['geo_lat'],'geo_lon': data[i]['geo_lon']};
                    site_h.push(b);
                    // rsp = rsp.filter(item => item.site_code !==data[i]['site_code'])

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
            var map = new google.maps.Map(document.getElementById('googleMap'), {
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
            console.log(hloc_geo);
            
            const lineSymbol = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                strokeColor: "#393",
            };


                // map object chilo
            
            var infowindow = new google.maps.InfoWindow();

            var marker, i;
            for (i = 0; i < data.length; i++) {

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
                
                // if (data[i]['site_name'] == '') {
                //     marker.setIcon(wicon);
                // }
                // if (i == 0) {
                //     marker.setIcon(sicon);
                // }
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
            
                var flightPath = new google.maps.Polyline({
                path:hloc_geo,
                strokeColor: "#40DFEF",
                strokeOpacity: 2,
                strokeWeight: 5,
                icons: [
                    {
                        icon: lineSymbol,
                        offset: "100%",
                    },
                ],
                map: map,
            });
            //animateCircle(flightPath);
            //flightPath.setMap(map);
            
        }, 
        error: function (error) {
            console.log(error);
        }

    });


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


