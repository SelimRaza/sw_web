// SR Activity MAP Report

function getHReport() {
    hide_me();
    var reportType = $("input[name='reportType']:checked").val();
    var sr_id = $("#sr_id_h").val();
    var sr_id_m = $('#sr_id_h_manual').val();
    var start_date = '';
    var end_date = '';
    var _token = $("#_token").val();
    var usr_type = $("#usr_type").val();
    var slgp_id=$('#sales_group_id_h').val();
    if (reportType === undefined) {
        alert('Please select report');
        return false;
    }
    if(slgp_id=='' && sr_id=='' && sr_id_m==''){
        alert("Please select sales group atleast")
        return false;
    }
    if(usr_type==1 && sr_id=='' && sr_id_m=='' && slgp_id==''){
        alert("Please select sales group atleast")
        return false;
    }
    if(usr_type==2 && sr_id=='' && sr_id_m=='' ){
        alert("Please select supervisor")
        return false;
    }
    else if (reportType == '') {
        alert('Please select report');
        return false;
    }
    var time_period = $("#start_date_period_h").val();
    if (time_period == '') {
        start_date = $('#start_date_h').val()
        end_date = $('#end_date_h').val()
    }
   
    $('#ajax_load').css('display', 'block');
    $.ajax({
        type: "POST",
        url: "/getHistoryReportData1",
        data: {
            _token: _token,
            sr_id: sr_id,
            sr_id_m: sr_id_m,
            time_period: time_period,
            start_date: start_date,
            end_date: end_date,
            usr_type: usr_type,
            reportType: reportType,
            slgp_id:slgp_id,
           
        },
        dataType: "json",
        success: function (data) {
            $('#ajax_load').css('display', 'none');


            var html = '';
            var heading = '';
            var footer = '';
            var start_date = data.start_date;
            var end_date = data.end_date;
            var data = data.data;

            switch (reportType) {
                case "sr_activity_sales_hierarchy":
                        var date = '<?php echo date("Y_m_d"); ?>';
                        heading += '<tr><th>Date </th>' +
                            '<th>Zone</th><th>SR ID</th><th>SR Name</th>' +
                            '<th>Rout Name</th><th>Rout Outlet</th>' +
                            '<th>Visit</th><th>RO-Visit</th><th>WR-Visit</th><th>Order</th>' +
                            '<th>Strike Rate</th><th>LPC</th>' +
                            '<th>Exp.</th><th>Action</th><th>Test</th><tr>';
                        var t_visit = 0;
                        var t_memo = 0;
                        var t_amnt = 0.00;
                        var t_olt = 0;
                        var t_ro = 0;
                        var t_wr = 0;
                        var s_rate = 0;
                        var t_lpc = 0;
                        for (var i = 0; i < data.length; i++) {
                            t_visit = t_visit + data[i]['t_visit'];
                            t_memo = t_memo + data[i]['t_memo'];
                            t_amnt = t_amnt + data[i]['t_amnt'];
                            t_olt = t_olt + data[i]['rout_olt'];
                            t_ro = t_ro + parseInt(data[i]['rout_visit']);
                            t_wr = t_wr + parseInt(data[i]['t_visit'] - data[i]['rout_visit']);
                            s_rate = s_rate + parseFloat(data[i]['strikeRate']);
                            t_lpc = t_lpc + parseFloat(data[i]['lpc']);
                            html += '<tr><td>' + data[i]['dhbd_date'] + '</td>' +
                                '<td>' + data[i]['zone_name'] + '</td>' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                '<td>' + data[i]['rout_id'] + '</td>' +
                                '<td>' + data[i]['rout_olt'] + '</td>' +
                                '<td>' + data[i]['t_visit'] + '</td>' +
                                '<td>' + data[i]['rout_visit'] + '</td>' +
                                '<td>' + (data[i]['t_visit'] - data[i]['rout_visit']) + '</td>' +
                                '<td>' + data[i]['t_memo'] + '</td>' +
                                '<td>' + data[i]['strikeRate'] + '</td>' +
                                '<td>' + data[i]['lpc'] + '</td>' +
                                '<td>' + data[i]['t_amnt'] + '</td>' +
                                '<td><button class="btn btn-success in_tg" onclick="showWardWiseVisitDetails(this)" sr_id="' + data[i]["aemp_id"] + '" date="' + data[i]["dhbd_date"] + '">Visit</button>' +
                                '<button class="btn btn-danger in_tg" onclick="showVisitMap(' + data[i]['aemp_id'] + ',this,0)" date="' + data[i]['dhbd_date'] + '" sr_id="' + data[i]["aemp_id"] + '">Map</button>' +
                                '<button class="btn btn-primary in_tg" onclick="showAllVisitedOutletList(this)" date="' + data[i]['dhbd_date'] + '" sr_id="' + data[i]["aemp_id"] + '">Outlet</button></td>' +
                                '<td>'+data[i]['aemp_id']+'</td>'+
                                '</tr>';
                        }
                        footer += '<tr><th>GT</th><th></th><th></th><th></th><th></th>' +
                            '<th>' + t_olt + '</th>' +
                            '<th>' + t_visit + '</th>' +
                            '<th>' + t_ro + '</th>' +
                            '<th>' + t_wr + '</th>' +
                            '<th>' + t_memo + '</th>' +
                            '<th>' + (s_rate / data.length).toFixed(2) + '</th>' +
                            '<th>' + (t_lpc / data.length).toFixed(2) + '</th>' +
                            '<th>' + (t_amnt).toFixed(2) + '</th><th></th></tr>';
            
                    break;
                case "sr_activity_gvt_hierarchy":
                    $('#activity_section').removeAttr('onclick');
                    $('#activity_section').attr('onclick', 'exportTableToCSV("sr_activity_gvt_hierarchy.csv","tableDiv_sr_history")');
                    heading += '<tr>' +
                           '<th>SR ID</th>' +
                            '<th>SR Name</th><th>Market Name</th><th>Ward Name</th><th>Thana Name</th><th>District Name</th></tr>';
                    for(var i=0;i<data.length;i++){
                        html+='<tr><td>'+data[i].aemp_usnm+'</td>'+
                                '<td>'+data[i].aemp_name+'</td>'+
                                '<td>'+data[i].mktm_name+'</td>'+
                                '<td>'+data[i].ward_name+'</td>'+
                                '<td>'+data[i].than_name+'</td>'+
                                '<td>'+data[i].dsct_name+'</td></tr>';
                    }
                    break;
                case "activity_summary":
                    console.log(data);
                    // var total_days = Math.floor((Date.parse(end_date) - Date.parse(start_date)) / 86400000);
                    // total_days = total_days + 1;
                    let total_days=0;
                    $('#activity_section').removeAttr('onclick');
                    $('#activity_section').attr('onclick', 'exportTableToCSV("activity_summary.csv","tableDiv_sr_history")');
                    heading += '<tr><th>MONTH NO</th>' +
                        '<th>STAFF ID</th>' +
                        '<th>EMP NAME</th>' +
                        '<th>WORKING DAYS</th>' +
                        '<th>PRESENT DAYS</th><th>NO. OF DISTRICT' +
                        '<th>NO. OF THANA</th>' +
                        '<th>NO. OF WARD</th>' +
                        '<th>TOTAL NOTE</th>' +
                        '<th>RETAIL VISIT</th>' +
                        '<th>OTHER VISIT</th>' +
                        '<th colspan="2">ACTION</th>';
                    var w_days = 0;
                    var dsct = 0;
                    var than = 0;
                    var ward = 0;
                    var note = 0;
                    var tr_vst = 0;
                    var t0_vst = 0;
                    for (var i = 0; i < data.length; i++) {
                        w_days += data[i].WorkindDay;
                        dsct += data[i].No_of_District;
                        than += data[i].No_of_thana;
                        ward += data[i].No_of_ward;
                        note += parseInt(data[i].T_note);
                        tr_vst += parseInt(data[i].Rvisit);
                        t0_vst += parseInt(data[i].Otvisit);
                        total_days+=parseInt(data[i].t_days);
                        html += '<tr><td>' + data[i].mnth + '</td>' +
                            '<td>' + data[i].aemp_usnm + '</td>' +
                            '<td>' + data[i].aemp_name + '</td>' +
                            '<td>'+data[i].t_days+'</td>' +
                            '<td>' + data[i].WorkindDay + '</td>' +
                            '<td>' + data[i].No_of_District + '</td>' +
                            '<td>' + data[i].No_of_thana + '</td>' +
                            '<td>' + data[i].No_of_ward + '</td>' +
                            '<td>' + data[i].T_note + '<a href="#" start_date="' + data[i].ss_date + '" end_date="' + data[i].ee_date + '" aemp_usnm="' + data[i].aemp_usnm + '" onclick="getCategoryWiseNoteDetails(this)"><i class="fa fa-eye" style="float:right;"></i></a></td>' +
                            '<td>' + data[i].Rvisit + '</td>' +
                            '<td>' + data[i].Otvisit + '</td>' +
                            '<td><a href="#" start_date="' + data[i].ss_date + '" end_date="' + data[i].ee_date + '" aemp_usnm="' + data[i].aemp_usnm + '" onclick="getDetailsOfActivitySummary(this)"><i class="fa fa-eye"></i></a></td>';
                        html += "<td><a  href='activity_summary/heatmap/" + data[i].aemp_usnm + "/" + data[i].ss_date + "/" + data[i].ee_date + "' target='_blank' class='btn btn-info btn-xs'><i class='fa fa-map-marker' style='color:red;'></i> </a>";


                    } footer += '<tr><td>GT</td><td></td><td></td>' +
                        '<td>' + total_days + '</td>' +
                        '<td>' + parseInt(w_days) + '</td>' +
                        '<td>' + parseInt(dsct) + '</td>' +
                        '<td>' + parseInt(than) + '</td>' +
                        '<td>' + parseInt(ward) + '</td>' +
                        '<td>' + parseInt(note) + '</td>' +
                        '<td>' + parseInt(tr_vst) + '</td>' +
                        '<td>' + parseInt(t0_vst) + '</td></tr>';

                default:
                    break;
            }
            // $("#head_history").append(heading);
            // $("#cont_history").append(html);
            // $("#foot_history").append(footer);
            appendData(heading,html);
            //console.log(data);

        }, error: function (error) {
            $('#ajax_load').css('display', 'none');
            Swal.fire({
                icon:'warning',
                text: '1. For SR You have to Select atleast Group  2. For Supervisor You have to select/provide staff id',
            })
            console.log(error);
        }
    });
}
function appendData(head,content){
    $('#head_history').empty();
    $('#cont_history').empty();
    $('#head_history').append(head);
    $('#cont_history').append(content);
    $('#tableDiv_sr_history').show();
}

function getCategoryWiseNoteDetails(obj){
    let aemp_usnm=$(obj).attr('aemp_usnm');
    let start_date=$(obj).attr('start_date');
    let end_date=$(obj).attr('end_date');
    $("#note_type").modal({ backdrop: false });
    $("#note_type").modal('show');
    $('#note_type_load').show();
    $('#note_type_cont').empty();
    $.ajax({
        type:"GET",
        url: "/getNoteDetails/" + aemp_usnm + "/" + start_date + "/" + end_date,
        
        dataType:"json",
        success:function(data){
            $('#note_type_load').hide();
            let append_data='';
            let count=1;
            for(let i=0;i<data.length;i++){
                append_data+='<tr><td>'+count+'</td>'+
                                '<td>'+data[i].note_dtim+'</td>'+
                                '<td>'+data[i].site_name+'</td>'+
                                '<td>'+data[i].note_titl+'</td>'+
                                '<td>'+data[i].note_body+'</td>'+
                                '<td><img src="https://images.sihirbox.com/'+data[i].note_img+'" alt="n/a" height="75" width="100%" class="thumb" style="border-radius:3px; cursor:pointer;" id="'+data[i].id+'" onclick="getNoteImage(this)"></td>'+
                                '<td>'+data[i].ntpe_name+'</td></tr>';
                count++;
            }
            $('#note_type_cont').append(append_data);

        },
        error:function(error){
            console.log(error)
        }
    });
}

function getDetailsOfActivitySummary(v) {
    var start_date = $(v).attr('start_date');
    var end_date = $(v).attr('end_date');
    var aemp_usnm = $(v).attr('aemp_usnm');
    $("#ac_dt").modal({ backdrop: false });
    $("#ac_dt").modal('show');
    $('#ac_dt_load').show();
    $('#ac_dt_body').empty();
    $.ajax({
        type: "GET",
        url: "/getActivitySummaryDetails/" + aemp_usnm + "/" + start_date + "/" + end_date,
        success: function (data) {
            console.log(data);
            $('#ac_dt_load').hide();
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<tr><td>' + data[i].mnth + '</td>' +
                    '<td>' + data[i].attn_date + '</td>' +
                    '<td>' + data[i].s_time + '</td>'+
                    '<td>' + data[i].e_time + '</td>'+
                    '<td>' + data[i].than_name + '</td>' +
                    '<td>' + data[i].dsct_name + '</td>' +
                    '<td>'+ data[i].Total_note+'<a href="#" start_date="' + data[i].attn_date + '" end_date="' + data[i].attn_date + '" aemp_usnm="' + data[i].aemp_usnm + '" onclick="getCategoryWiseNoteDetails(this)"><i class="fa fa-eye" style="margin-left:4px;" ></i></a></td>'+
                    '<td>' + data[i].retailVisit + '</td>' +
                    '<td>' + data[i].OtherVisit + '</td>'+
                    '</tr>';
            }
            $("#ac_dt_body").append(html);

        }, error: function (error) {
            $('#ac_dt_load').show();
            console.log(error);
        }
    });
}


function getLocationAddress(lat, long) {
    //var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+long+"&key=AIzaSyDjQWbYPIKm-omHF7XHQEnJfeaONobga9M&sensor=false";
    var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + long + "&key=AIzaSyAUz9b1JjhtFMPkg4scrdW2uAbLfGyc3d4";
    $.get(url, function (data) {
        var results = data.results;
        if (data.status === 'OK') {
            if (results[0]) {
                var city = "";
                var thana = "";
                var zilla = "";
                var state = "";
                var country = "";
                var zipcode = "";

                var address_components = results[0].address_components;
                //console.log(address_components);
                for (var i = 0; i < address_components.length; i++) {
                    if (address_components[i].types[0] === "administrative_area_level_1" && address_components[i].types[1] === "political") {
                        state = address_components[i].long_name;
                    }
                    if (address_components[i].types[0] === "administrative_area_level_2" && address_components[i].types[1] === "political") {
                        zilla = address_components[i].long_name;
                    }
                    if (address_components[i].types[0] === "locality" && address_components[i].types[1] === "political") {
                        city = address_components[i].long_name;
                    }
                    if (address_components[i].types[1] === "sublocality" && address_components[i].types[0] === "political") {
                        thana = address_components[i].long_name;
                    }

                    if (address_components[i].types[0] === "postal_code" && zipcode == "") {
                        zipcode = address_components[i].long_name;

                    }

                    if (address_components[i].types[0] === "country") {
                        country = address_components[i].long_name;

                    }
                }
                var address = {
                    "city": city,
                    "thana": thana,
                    "zilla": zilla,
                    "state": state,
                    "country": country,
                    "zipcode": zipcode,
                };
                //console.log(address);
                return address;
                //console.log("==============================")
                // console.log(address);
            }
            else {
                window.alert('No results found');
            }
        }
        else {
            console.log('Geocoder failed due to: ' + data);

        }
    });
}

