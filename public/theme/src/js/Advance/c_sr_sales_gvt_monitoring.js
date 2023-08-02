
/*
Initial Function Of Sales Hierarchy & Govt Hierarchy Resides in Common_Report.Blade File => getDiggingReport
*Sales Hierarchy Function List Start
*/

var head = '<th>SL</th>' +
            '<th>NAME</th>' +
            '<th>MOBILE</th>' +
            '<th>SR</th>' +
            ' <th>OUTLET</th>' +
            ' <th>VISITED</th>' +
            ' <th>VISIT/SR</th>' +
            '<th>NON-VISITED</th>' +
            ' <th>ORDER</th>' +
            ' <th>ORDER/SR</th>' +
            ' <th>S.RATE</th>' +
            ' <th>LPC</th>' +
            '<th>EXP</th>' +
            '<th>EXP/SR</th>' +
            '<th>TGT</th>';
function testClick(v) {
    var emid = $(v).attr('emid');
    var role_name = $(v).attr('role_name');
    v.removeAttribute('onclick');
    v.setAttribute('onclick', 'desigEmployeeAddRemove(this)');
    console.log(v);
    var html = role_name + " >  ";
    var period = $('#sh_date').val();
    var _token = $("#_token").val();
    $('#desig').append(v);
    $('#emid_div').empty();
    var emid_cnt = '<input type="hidden" value="' + emid + '" id="emid">';
    $('#emid_div').append(emid_cnt);
    var start_date = $("#period").val();
    hideSalesHSingleDateDiv();
    if(period=='-1'){
        showSalesHSingleDateDiv();
    }
    $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: "/getUserWiseReport",
        data: {
            _token: _token,
            emid: emid,
            period: period,
            start_date:start_date
        },
        dataType: "json",
        success: function (data) {
            $('#ajax_load').css("display", "none");
            //$('#tableDiv_traking').show();
            // $('#head_tracking_dev_note_task').empty();
            // $('#head_tracking_dev_note_task').append(head);
            let days=data.days;
            var data=data.un_emp;
            var html = "";
            var count = 1;
            var dp_cnt = '';
            var out_color = '';
            var visit_color = '';
            var t_sr = 1;
            var data_footer='';
            let foot_t_sr=0;
            let foot_t_olt=0;
            let foot_t_visit=0;
            let foot_t_nvisit=0;
            let foot_t_memo=0;
            let foot_t_order_amount=0;
            let foot_t_total_target=0;
            let foot_dhbd_line=0;
            for (var i = 0; i < data.length; i++) {
                var nonV=data[i]['t_outlet'] - data[i]['total_visited']<0?0:data[i]['t_outlet'] - data[i]['total_visited'];
                if (data[i]['role_id'] < 6) {
                    out_color = data[i]['outlet_color'];
                    visit_color = data[i]['visit_color'];
                }
                foot_t_sr+=parseInt(data[i]['totalSr']);
                foot_t_olt+=parseInt(data[i]['t_outlet']);
                foot_t_visit+=parseInt(data[i]['total_visited']);
                foot_t_nvisit+=parseInt(data[i]['t_outlet'] - data[i]['total_visited']);
                foot_t_memo+=parseInt(data[i]['memo']);
                foot_t_order_amount+=parseFloat(data[i]['order_amount']);
                foot_dhbd_line+=parseFloat(data[i]['dhbd_line']);
                foot_t_total_target+=parseFloat(data[i]['total_target']/26);
                html += '<tr>' +
                    '<td>' + count + '</td>' +
                    '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                    '<td>' + data[i]['totalSr'] + '</td>' +
                    '<td style="color:' + out_color + '">' + data[i]['t_outlet'] + '</td>';
                if (data[i]['role_id'] == 2 && period==0) {
                    html += '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '<i id="show" style="color:forestgreen;cursor:pointer;" onclick="showCategoryWiseOutlet(' + data[i]["oid"] + ')" class="fa fa-info-circle fa-2x  pull-right"></i></td>';
                    html+='<td>' + data[i]['vpsr'] + '</td>';
                    html += '<td>' + nonV + '</td>';
                } else {
                    html += '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '</td>';
                    html+='<td>' + data[i]['vpsr'] + '</td>';
                    html += '<td>' + nonV + '</td>';
                }
                html += '<td>' + data[i]['memo'] + '</td>' +
                        '<td>' + data[i]['mpsr'] + '</td>' +
                        '<td>' + data[i]['strikeRate'] + '</td>' +
                        '<td>' + data[i]['lpc'] + '</td>' +                        
                        '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                        '<td>' +data[i]['expsr'] + '</td>' +
                        '<td>' + (data[i]['total_target'] / 26).toFixed(2) + '</td>' +
                        '</tr>';
                dp_cnt += '<li><a href="#" onclick="testClick(this)" emid="' + data[i]["oid"] + '" role_name="' + data[i]['aemp_name'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp;' + '</a></li>';
                count++;
            }
            foot_t_order_amount=foot_t_order_amount>0?foot_t_order_amount.toFixed(2):0;
            foot_t_total_target=foot_t_total_target>0?foot_t_total_target.toFixed(2):0;
            let expsr=foot_t_order_amount/(foot_t_sr*days);
            expsr=expsr>0?expsr.toFixed(2):0;
            data_footer='<tr><td colspan="11"></td></tr><tr>'+
                        '<td>GT</td><td></td><td></td>'+
                        '<td>'+foot_t_sr+'</td>'+
                        '<td>'+foot_t_olt+'</td>'+
                        '<td>'+foot_t_visit+'</td>'+
                        '<td>'+(foot_t_visit/(foot_t_sr*days)).toFixed()+'</td>'+
                        '<td>'+foot_t_nvisit+'</td>'+
                        '<td>'+foot_t_memo+'</td>'+
                        '<td>'+(foot_t_memo/(foot_t_sr*days)).toFixed()+'</td>'+
                        '<td>'+(foot_t_memo*100/foot_t_visit).toFixed(2)+'%'+'</td>'+
                        '<td>'+(foot_dhbd_line/foot_t_memo).toFixed(2)+'</td>'+
                        '<td>'+foot_t_order_amount+'</td>'+
                        '<td>'+expsr+'</td>'+
                        '<td>'+foot_t_total_target+'</td></tr>';
            // $('#cont_traking').empty();
            // $('#all_dp_content').empty();
            // $('#cont_traking').append(html);
            // $('#cont_traking').append(data_footer);
            emptyContentAndAppendDataTrack1(head,html+data_footer);
            $('#all_dp_content').html(dp_cnt);
            $('#rpt').height($("#tableDiv_traking").height() + 150);
        }, error: function (error) {
            $('#ajax_load').css("display", "none");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            })
            console.log(error);
        }
    });
}
function desigEmployeeAddRemove(elem) {
    $(elem).nextAll().remove();
    var emid = $(elem).attr('emid');
    $('#emid_div').empty();
    var emid_cnt = '<input type="hidden" value="' + emid + '" id="emid">';
    $('#emid_div').append(emid_cnt);
    var emid = $(elem).attr('emid');
    var _token = $("#_token").val();
    var period = $('#sh_date').val();
    var start_date = $("#period").val();
    hideSalesHSingleDateDiv();
    if(period=='-1'){
        showSalesHSingleDateDiv();
    }
    $.ajax({
        type: "POST",
        url: "/getUserWiseReport",
        data: {
            _token: _token,
            emid: emid,
            period: period,
            start_date: start_date
        },
        dataType: "json",
        success: function (data) {
            // $('#tableDiv_traking').show();
            // $('#head_tracking_dev_note_task').empty();
            // $('#head_tracking_dev_note_task').append(head);
            let days=data.days;
            var data=data.un_emp;
            var html = "";
            var count = 1;
            var dp_cnt = '';
            var out_color = '';
            var visit_color = '';
            var t_sr = 1;
            var data_footer='';
            let foot_t_sr=0;
            let foot_t_olt=0;
            let foot_t_visit=0;
            let foot_t_nvisit=0;
            let foot_t_memo=0;
            let foot_t_order_amount=0;
            let foot_t_total_target=0;
            let flag=0;
            let foot_dhbd_line=0;
            if(period==0 || period==1){
                flag=1;
            }
            for (var i = 0; i < data.length; i++) {
                var nonV=data[i]['t_outlet'] - data[i]['total_visited']<0?0:data[i]['t_outlet'] - data[i]['total_visited'];
                if (data[i]['role_id'] < 6) {
                    out_color = data[i]['outlet_color'];
                    visit_color = data[i]['visit_color'];
                }
                if (data[i]['role_id'] < 6) {
                    out_color = data[i]['outlet_color'];
                    visit_color = data[i]['visit_color'];
                }
                foot_t_sr+=parseInt(data[i]['totalSr']);
                foot_t_olt+=parseInt(data[i]['t_outlet']);
                foot_t_visit+=parseInt(data[i]['total_visited']);
                foot_t_nvisit+=parseInt(data[i]['t_outlet'] - data[i]['total_visited']);
                foot_t_memo+=parseInt(data[i]['memo']);
                foot_t_order_amount+=parseFloat(data[i]['order_amount']);
                foot_dhbd_line+=parseFloat(data[i]['dhbd_line']);
                foot_t_total_target+=parseFloat(data[i]['total_target']/26);
                html += '<tr>' +
                    '<td>' + count + '</td>' +
                    '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                    '<td>' + data[i]['aemp_mob1'] + '</td>' +
                    '<td>' + data[i]['totalSr'] + '</td>' +
                    '<td style="color:' + out_color + '">' + data[i]['t_outlet'] + '</td>';
                if (data[i]['role_id'] == 2 && flag==1) {
                    html += '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '<i id="show" style="color:forestgreen;cursor:pointer;" onclick="showCategoryWiseOutlet(' + data[i]["oid"] + ')" class="fa fa-info-circle fa-2x  pull-right"></i></td>';
                    html+='<td>' + data[i]['vpsr'] + '</td>';
                    html += '<td>' + nonV + '</td>';
                } else {
                    html += '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '</td>';
                    html+='<td>' + data[i]['vpsr'] + '</td>';
                    html += '<td>' + nonV + '</td>';
                }
                html += '<td>' + data[i]['memo'] + '</td>' +
                '<td>' + data[i]['mpsr'] + '</td>' +
                '<td>' + data[i]['strikeRate'] + '</td>' +
                '<td>' + data[i]['lpc'] + '</td>' +                        
                '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                '<td>' + data[i]['expsr'] + '</td>' +
                "<td>" + (data[i]['total_target'] / 26).toFixed(2) + "</td>" +
                '</tr>';
                dp_cnt += '<li><a href="#" onclick="testClick(this)" emid="' + data[i]['oid'] + '" role_name="' + data[i]['aemp_name'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp;' + '</a></li>';
                count++;
            }
            //'<i id="show" style="color:darkblue;cursor:pointer;" onclick="showCategoryWiseNonVisitOutlet(' + data[i]["oid"] + ')" class="fa fa-info-circle fa-2x  pull-right"></i>
            foot_t_order_amount=foot_t_order_amount>0?foot_t_order_amount.toFixed(2):0;
            foot_t_total_target=foot_t_total_target>0?foot_t_total_target.toFixed(2):0;
            let expsr=foot_t_order_amount/(foot_t_sr*days);
            expsr=expsr>0?expsr.toFixed(2):0;
            data_footer='<tr><td colspan="11"></td></tr><tr>'+
                    '<td>GT</td><td></td><td></td>'+
                    '<td>'+foot_t_sr+'</td>'+
                    '<td>'+foot_t_olt+'</td>'+
                    '<td>'+foot_t_visit+'</td>'+
                    '<td>'+(foot_t_visit/(foot_t_sr*days)).toFixed()+'</td>'+
                    '<td>'+foot_t_nvisit+'</td>'+
                    '<td>'+foot_t_memo+'</td>'+
                    '<td>'+(foot_t_memo/(foot_t_sr*days)).toFixed()+'</td>'+
                    '<td>'+(foot_t_memo*100/foot_t_visit).toFixed(2)+'%'+'</td>'+
                    '<td>'+(foot_dhbd_line/foot_t_memo).toFixed(2)+'</td>'+
                    '<td>'+foot_t_order_amount+'</td>'+
                    '<td>'+expsr+'</td>'+
                    '<td>'+foot_t_total_target+'</td></tr>';
            // $('#cont_traking').empty();
            // $('#all_dp_content').empty();
            // $('#cont_traking').append(html);
            // $('#cont_traking').append(data_footer);
            emptyContentAndAppendDataTrack1(head,html+data_footer);
            $('#all_dp_content').html(dp_cnt);
            $('#rpt').height($("#tableDiv_traking").height() + 150);
        }, error: function (error) {
            $('#ajax_load').css("display", "none");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            })
            console.log(error);
        }
    });
}
function getDateWiseUserReport(date) {
    var emid = $('#emid').val();
    var _token = $("#_token").val();
    var start_date = $("#period").val();
    hideSalesHSingleDateDiv();
    if(date=='-1'){
        showSalesHSingleDateDiv();
    }
    $('#ajax_load').css("display", "block");
        $.ajax({
            type: "POST",
            url: "/getUserWiseReport",
            data: {
                _token: _token,
                emid: emid,
                period: date,
                start_date: start_date
            },
            dataType: "json",
            success: function (data) {
                $('#ajax_load').css("display", "none");
                // $('#tableDiv_traking').show();
                // $('#head_tracking_dev_note_task').empty();
                // $('#head_tracking_dev_note_task').append(head);
                let days=data.days;
                var data=data.un_emp;
                var html = "";
                var count = 1;
                var dp_cnt = '';
                var out_color = '';
                var visit_color = '';
                var t_sr = 1;
                var data_footer='';
                let foot_t_sr=0;
                let foot_t_olt=0;
                let foot_t_visit=0;
                let foot_t_nvisit=0;
                let foot_t_memo=0;
                let foot_t_order_amount=0;
                let foot_t_total_target=0;
                let flag=0;
                let foot_dhbd_line=0;
                if(date==0 || date==1){
                    flag=1;
                }
                for (var i = 0; i < data.length; i++) {
                    var nonV=data[i]['t_outlet'] - data[i]['total_visited']<0?0:data[i]['t_outlet'] - data[i]['total_visited'];
                    if (data[i]['role_id'] < 6) {
                        out_color = data[i]['outlet_color'];
                        visit_color = data[i]['visit_color'];
                    }
                    foot_t_sr+=parseInt(data[i]['totalSr']);
                    foot_t_olt+=parseInt(data[i]['t_outlet']);
                    foot_t_visit+=parseInt(data[i]['total_visited']);
                    foot_t_nvisit+=parseInt(data[i]['t_outlet'] - data[i]['total_visited']);
                    foot_t_memo+=parseInt(data[i]['memo']);
                    foot_t_order_amount+=parseFloat(data[i]['order_amount']);
                    foot_dhbd_line+=parseFloat(data[i]['dhbd_line']);
                    foot_t_total_target+=parseFloat(data[i]['total_target']/26);
                    html += '<tr>' +
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['aemp_name'] + '-' + data[i]['aemp_usnm'] + '</td>' +
                        '<td>' + data[i]['aemp_mob1'] + '</td>' +
                        '<td>' + data[i]['totalSr'] + '</td>' +
                        '<td style="color:' + out_color + '">' + data[i]['t_outlet'] + '</td>';
                    if (data[i]['role_id'] == 2 && flag==1) {
                        html += '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '<i id="show" style="color:forestgreen;cursor:pointer;" onclick="showCategoryWiseOutlet(' + data[i]["oid"] + ')" class="fa fa-info-circle fa-2x  pull-right"></i></td>';
                        html+='<td>' + data[i]['vpsr'] + '</td>';
                        html += '<td>' + nonV + '</td>';
                    } else {
                        html += '<td style="color:' + visit_color + '">' + data[i]['total_visited'] + '</td>';
                        html+='<td>' + data[i]['vpsr'] + '</td>';
                        html += '<td>' + nonV + '</td>';
                    }
                    html += '<td>' + data[i]['memo'] + '</td>' +
                    '<td>' + data[i]['mpsr'] + '</td>' +
                    '<td>' + data[i]['strikeRate'] + '</td>' +
                    '<td>' + data[i]['lpc'] + '</td>' +                        
                    '<td>' + (data[i]['order_amount']).toFixed(2) + '</td>' +
                    '<td>' + data[i]['expsr']+ '</td>' +
                    "<td>" + (data[i]['total_target'] / 26).toFixed(2) + "</td>" +
                    '</tr>';
                    dp_cnt += '<li><a href="#" onclick="testClick(this)" emid="' + data[i]['oid'] + '" role_name="' + data[i]['aemp_name'] + '">' + ' ' + data[i]['aemp_name'] + ' ⥤&nbsp;&nbsp;' + '</a></li>';
                    count++;
                }
                foot_t_order_amount=foot_t_order_amount>0?foot_t_order_amount.toFixed(2):0;
                foot_t_total_target=foot_t_total_target>0?foot_t_total_target.toFixed(2):0;
                let expsr=foot_t_order_amount/(foot_t_sr*days);
                expsr=expsr>0?expsr.toFixed(2):0;
                data_footer='<tr><td colspan="11"></td></tr><tr>'+
                            '<td>GT</td><td></td><td></td>'+
                            '<td>'+foot_t_sr+'</td>'+
                            '<td>'+foot_t_olt+'</td>'+
                            '<td>'+foot_t_visit+'</td>'+
                            '<td>'+(foot_t_visit/(foot_t_sr*days)).toFixed()+'</td>'+
                            '<td>'+foot_t_nvisit+'</td>'+
                            '<td>'+foot_t_memo+'</td>'+
                            '<td>'+(foot_t_memo/(foot_t_sr*days)).toFixed()+'</td>'+
                            '<td>'+(foot_t_memo*100/foot_t_visit).toFixed(2)+'%'+'</td>'+
                            '<td>'+(foot_dhbd_line/foot_t_memo).toFixed(2)+'</td>'+
                            '<td>'+foot_t_order_amount+'</td>'+
                            '<td>'+expsr+'</td>'+
                            '<td>'+foot_t_total_target+'</td></tr>';
                // $('#cont_traking').empty();
                // $('#all_dp_content').empty();
                // $('#cont_traking').append(html);
                // $('#cont_traking').append(data_footer);
                emptyContentAndAppendDataTrack1(head,html+data_footer);
                $('#all_dp_content').html(dp_cnt);
                $('#rpt').height($("#tableDiv_traking").height() + 150);
            }, error: function (error) {
                $('#ajax_load').css("display", "none");
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                })
                console.log(error);
            }
        });
}
function showSalesHSingleDateDiv(){
    $('#period').show();
}
function hideSalesHSingleDateDiv(){
    $('#period').hide();
}
function showCategoryWiseOutlet(emid) {

    $("#myModalVisit").modal({backdrop: false});
    $('#modalCat').empty();
    $('#myModalVisit').append('Category wise Visit');
    $('#myModalVisit').modal('show');
    $('#cat_out_load').show();
    var date = $('#sh_date').val();
    $.ajax({
        type: "get",
        url: "/getCatWiseOutVisit/" + emid + "/" + date,
        dataType: "json",
        success: function (data) {

            $('#cat_out_load').hide();

            console.log(data);
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<tr><td>' + data[i]['otcg_name'] + '</td>' +
                    '<td>' + data[i]['num'] + '</td>' +
                    '<td><i id="show" class="fa fa-info-circle fa-2x "   onclick="getVisitedOutletDetails(' + emid + ',' + data[i]['id'] + ')" style="cursor:pointer;color:forestgreen;"></i></td>' +
                    '</tr>';
            }
            // $('.modal-backdrop ').removeClass('modal-backdrop');
            $('#myModalVisitBody').empty();
            $('#myModalVisitBody').append(html);
        }, error: function (error) {
            console.log(error);
        }
    });
}
function getVisitedOutletDetails(emid, cat_id) {
    var date = $('#sh_date').val();
    $("#myModalVisitedOutlet").modal({backdrop: false});
    $('#modalOuthead').empty();
    $('#modalOuthead').append('Visited Outlet Details');
    $('#myModalVisitedOutlet').modal('show');
    $('#cat_out_load_details').show();
    $.ajax({
        type: "get",
        url: "/getVisitedOutletDetails/" + emid + "/" + date + "/" + cat_id,
        dataType: "json",
        success: function (data) {
            $('#cat_out_load_details').hide();
            console.log(data);
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<tr><td>' + data[i]['site_code'] + '</td>' +
                    '<td>' + data[i]['site_name'] + '</td>' +
                    '<td>' + data[i]['site_mob1'] + '</td>' +
                    '<td>' + data[i]['site_adrs'] + '</td>' +
                    '</tr>';
            }
            // $('.modal-backdrop ').removeClass('modal-backdrop');
            $('#myModalVisitedOutletBody').empty();
            $('#myModalVisitedOutletBody').append(html);
        }, error: function (error) {
            console.log(error);
        }
    });
}

function showCategoryWiseNonVisitOutlet(emid){
    $("#myModalVisit").modal({backdrop: false});
    $('#modalCat').empty();
    $('#myModalVisit').append('Category wise Non Visited');
    $('#myModalVisit').modal('show');
    $('#cat_out_load').show();
    var date = $('#sh_date').val();
    $.ajax({
        type: "get",
        url: "/getCatWiseOutNonVisit/" + emid + "/" + date,
        dataType: "json",
        success: function (data) {
            $('#cat_out_load').hide();
            console.log(data);
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<tr><td>' + data[i]['otcg_name'] + '</td>' +
                    '<td>' + data[i]['num'] + '</td>' +
                    '<td><i id="show" class="fa fa-info-circle fa-2x "   onclick="getNonVisitedOutletDetails(' + emid + ',' + data[i]['id'] + ')" style="cursor:pointer;color:forestgreen;"></i></td>' +
                    '</tr>';
            }
            // $('.modal-backdrop ').removeClass('modal-backdrop');
            $('#myModalVisitBody').empty();
            $('#myModalVisitBody').append(html);
        }, error: function (error) {
            console.log(error);
        }
    });
}

function getNonVisitedOutletDetails(emid, cat_id) {
    var date = $('#sh_date').val();
    $("#myModalVisitedOutlet").modal({backdrop: false});
    $('#modalOuthead').empty();
    $('#modalOuthead').append('Visited Outlet Details');
    $('#myModalVisitedOutlet').modal('show');
    $('#cat_out_load_details').show();
    $.ajax({
        type: "get",
        url: "/getVisitedOutletDetails/" + emid + "/" + date + "/" + cat_id,
        dataType: "json",
        success: function (data) {
            $('#cat_out_load_details').hide();
            console.log(data);
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<tr><td>' + data[i]['site_code'] + '</td>' +
                    '<td>' + data[i]['site_name'] + '</td>' +
                    '<td>' + data[i]['site_mob1'] + '</td>' +
                    '<td>' + data[i]['site_adrs'] + '</td>' +
                    '</tr>';
            }
            // $('.modal-backdrop ').removeClass('modal-backdrop');
            $('#myModalVisitedOutletBody').empty();
            $('#myModalVisitedOutletBody').append(html);
        }, error: function (error) {
            console.log(error);
        }
    });
}

/* 
End Of Sales Hierarchy
Start Of Govt Hierarchy
*/

function getGvtDeeperData(v) {
    $('#deviation_date_div').hide();
    var id = $(v).attr('id');
    var stage = $(v).attr('stage');
    var slgp_id=$(v).attr('slgp_id');
    var acmp_id=$(v).attr('acmp_id');
    var time_period=$('#sh_date_gvt').val();
    v.removeAttribute('onclick');
    v.setAttribute('onclick', 'appendTopTitle(this)');
    var _token = $("#_token").val();
    $('#gvt_hierarchy').append(v);
    $('#emid_div1').empty();
    var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage + '" slgp_id="'+slgp_id+'">';
    $('#emid_div1').append(emid_cnt);
    $('#sh_date_gvt_single_date').hide();
    var start_date=$('#sh_date_gvt_single_date').val();
    if(time_period==-1){
        $('#sh_date_gvt_single_date').show();
    }
    $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: "/getGvtDeeperSalesData",
        data: {      
            id: id,
            stage: stage,
            slgp_id: slgp_id,
            acmp_id:acmp_id,
            time_period:time_period,
            start_date:start_date,
            _token: _token,
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            $('#ajax_load').css("display", "none");
            var sub_head = '<tr style="font-size:10px;">' +
            '<th></th>' +
            '<th></th>';
            var html = "";
            var head1="";
            var solt_ref='',sv_ref='',sro_ref='',sm_ref='',so_ref='',sd_ref='';
            var visit=1,s_rate=0,swrv=0,dp_cnt='';
            var t_solt_ref='',t_vsit_ref='',t_rvst_ref='',t_wvst_ref='',t_memo_ref='',t_ordr_ref='',t_deli_ref='',t_srat_ref='';
            var res=[],t_ord=0,t_deli=0;
            var len=data.data.length;
            var slgp=data.slgp;
            var data=data.data;
            switch (stage){
                case "-1":
                    head1 += '<tr><th>Division Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['disn_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="0"  id="' +data[i]['disn_id'] + '">' + ' ' + data[i]['disn_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "0":
                    head1 += '<tr><th>District Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['dsct_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="1"  id="' +data[i]['dsct_id'] + '">' + ' ' + data[i]['dsct_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "1":
                    head1 += '<tr><th>Thana Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['than_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="2"  id="' +data[i]['than_id'] + '">' + ' ' + data[i]['than_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "2":
                    head1 += '<tr><th>Ward Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['ward_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="3"  id="' +data[i]['ward_id'] + '">' + ' ' + data[i]['ward_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "3":
                    head1 += '<tr><th>Market Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['mktm_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="4"  id="' +data[i]['mktm_id'] + '">' + ' ' + data[i]['mktm_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "4":
                    if (stage == 4) {
                        html = "<h4>No Data </h4>";
                        head1 = '';
                        sub_head = '';
                    }
                    break;
                default:
                    html="<h1>No Matched</html>";
                    break;
            }
            if(stage !=4){
                for (var i = 0; i <slgp.length; i++) {
                    head1 += '<th colspan="8" style="text-align:center;">' +slgp[i]['slgp_name'] + '</th>';
                    sub_head += '<th>Outlet</th>'+'<th>Visit</th>'+'<th>RO-Visit</th>' +'<th>WR-Visit</th>'+ '<th>Memo</th>'+ 
                                '<th>Exp</th>'+
                                '<th>Delivery</th>'+ 
                                '<th>Strike Rate(%)</th>';
                }
                head1 += '</tr>';
                sub_head += '</tr>';

                html+='<tr style="font-weight:bold;">'+
                        '<td>GT</td>'+
                        '<td>'+res['tolt']+'</td>';
                for(var j=0;j<slgp.length;j++){
                    t_solt_ref='solt'+slgp[j].id;
                    t_vsit_ref='vsit'+slgp[j].id;
                    t_rvst_ref='rvst'+slgp[j].id;
                    t_wvst_ref='wvst'+slgp[j].id;
                    t_memo_ref='memo'+slgp[j].id;
                    t_ordr_ref='ordr'+slgp[j].id;
                    t_deli_ref='deli'+slgp[j].id;
                    t_srat_ref='srat'+slgp[j].id;
                    t_ord=res[t_ordr_ref]?res[t_ordr_ref].toFixed(2):0.00;
                    t_deli=res[t_deli_ref]?res[t_deli_ref].toFixed(2):0.00;
                    html+='<td>'+res[t_solt_ref]+'</td>'+
                    '<td>'+res[t_vsit_ref]+'</td>'+
                    '<td>'+res[t_rvst_ref]+'</td>'+
                    '<td>'+res[t_wvst_ref]+'</td>'+
                    '<td>'+res[t_memo_ref]+'</td>'+
                    '<td>'+t_ord+'</td>'+
                    '<td>'+t_deli+'</td>'+
                    '<td>'+(res[t_srat_ref]/(len+1)).toFixed(2)+'</td>';
                
                }                                    
                html+='</tr>';
            }

            // $('#tableDiv_tracking_gvt_header1').empty();
            // $('#cont_traking_gvt').empty();
            // $('#all_dp_content1').empty();
            // $('#tableDiv_tracking_gvt_header1').append(head1 + sub_head);
            // $('#cont_traking_gvt').append(html);
            emptyContentAndAppendDataTrack(head1+sub_head,html)
            $('#all_dp_content1').html(dp_cnt);
            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

        }, error: function (error) {
            $('#ajax_load').css("display", "none");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            })
            console.log(error);
        }
    });
}


function appendTopTitle(v) {
    $('#deviation_date_div').hide();
    var id = $(v).attr('id');
    var stage = $(v).attr('stage');
    var slgp_id=$(v).attr('slgp_id');
    var acmp_id=$(v).attr('acmp_id');
    var time_period=$('#sh_date_gvt').val();
    var start_date=$('#sh_date_gvt_single_date').val();
    $(v).nextAll().remove();
    var _token = $("#_token").val();
    $('#emid_div1').empty();
    var emid_cnt = '<input type="hidden" value="' + id + '" id="gvt_hierarchy_id" stage="' + stage +'" slgp_id="'+slgp_id+'"">';
    $('#emid_div1').append(emid_cnt);
    $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: "/getGvtDeeperSalesData",
        data: {
            id: id,
            stage: stage,
            slgp_id: slgp_id,
            acmp_id: acmp_id,
            time_period:time_period,
            start_date:start_date,
            _token: _token,
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            $('#ajax_load').css("display", "none");
            var sub_head = '<tr style="font-size:10px;">' +
            '<th></th>' +
            '<th></th>';
            var html = "";
            var head1="";
            var solt_ref='',sv_ref='',sro_ref='',sm_ref='',so_ref='',sd_ref='';
            var visit=1,s_rate=0,swrv=0,dp_cnt='';
            var t_solt_ref='',t_vsit_ref='',t_rvst_ref='',t_wvst_ref='',t_memo_ref='',t_ordr_ref='',t_deli_ref='',t_srat_ref='';
            var res=[],t_ord=0,t_deli=0;
            var len=data.data.length;
            var slgp=data.slgp;
            var data=data.data;
            switch (stage){
                case "-1":
                    head1 += '<tr><th>Division Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['disn_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="0"  id="' +data[i]['disn_id'] + '">' + ' ' + data[i]['disn_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "0":
                    head1 += '<tr><th>District Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['dsct_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="1"  id="' +data[i]['dsct_id'] + '">' + ' ' + data[i]['dsct_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "1":
                    head1 += '<tr><th>Thana Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['than_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="2"  id="' +data[i]['than_id'] + '">' + ' ' + data[i]['than_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "2":
                    head1 += '<tr><th>Ward Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['ward_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="3"  id="' +data[i]['ward_id'] + '">' + ' ' + data[i]['ward_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "3":
                    head1 += '<tr><th>Market Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['mktm_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="4"  id="' +data[i]['mktm_id'] + '">' + ' ' + data[i]['mktm_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "4":
                    if (stage == 4) {
                        html = "<h4>No Data </h4>";
                        head1 = '';
                        sub_head = '';
                    }
                    break;
                default:
                    html="<h1>No Matched</html>";
                    break;
            }
            if(stage !=4){
                for (var i = 0; i <slgp.length; i++) {
                    head1 += '<th colspan="8" style="text-align:center;">' +slgp[i]['slgp_name'] + '</th>';
                    sub_head += '<th>Outlet</th>'+'<th>Visit</th>'+'<th>RO-Visit</th>' +'<th>WR-Visit</th>'+ '<th>Memo</th>'+ 
                                '<th>Exp</th>'+
                                '<th>Delivery</th>'+ 
                                '<th>Strike Rate(%)</th>';
                }
                head1 += '</tr>';
                sub_head += '</tr>';

                html+='<tr style="font-weight:bold;">'+
                        '<td>GT</td>'+
                        '<td>'+res['tolt']+'</td>';
                for(var j=0;j<slgp.length;j++){
                    t_solt_ref='solt'+slgp[j].id;
                    t_vsit_ref='vsit'+slgp[j].id;
                    t_rvst_ref='rvst'+slgp[j].id;
                    t_wvst_ref='wvst'+slgp[j].id;
                    t_memo_ref='memo'+slgp[j].id;
                    t_ordr_ref='ordr'+slgp[j].id;
                    t_deli_ref='deli'+slgp[j].id;
                    t_srat_ref='srat'+slgp[j].id;
                    t_ord=res[t_ordr_ref]?res[t_ordr_ref].toFixed(2):0.00;
                    t_deli=res[t_deli_ref]?res[t_deli_ref].toFixed(2):0.00;
                    html+='<td>'+res[t_solt_ref]+'</td>'+
                    '<td>'+res[t_vsit_ref]+'</td>'+
                    '<td>'+res[t_rvst_ref]+'</td>'+
                    '<td>'+res[t_wvst_ref]+'</td>'+
                    '<td>'+res[t_memo_ref]+'</td>'+
                    '<td>'+t_ord+'</td>'+
                    '<td>'+t_deli+'</td>'+
                    '<td>'+(res[t_srat_ref]/(len+1)).toFixed(2)+'</td>';
                
                }                                    
                html+='</tr>';
            }
            emptyContentAndAppendDataTrack(head1+sub_head,html);
            $('#all_dp_content1').html(dp_cnt);
            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

        },
         error: function (error) {
            $('#ajax_load').css("display", "none");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            })
            console.log(error);
        }
    });
}

function getDateWiseGvtReport(date){
    var id = $('#gvt_hierarchy_id').val();
    var slgp_id = $('#sh_slgp_id').val();
    var acmp_id = $('#sh_acmp_id').val();
    var stage = $('#gvt_hierarchy_id').attr('stage');
    var _token = $("#_token").val();
    var time_period=date;
    var start_date=$('#sh_date_gvt_single_date').val();
    $('#sh_date_gvt_single_date').hide();
    if(time_period==-1){
        $('#sh_date_gvt_single_date').show();
    }
    if(slgp_id==''){
        return confirm('Please select sales group');
    }
    $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: "/getGvtDeeperSalesData",
        data: {      
            id: id,
            stage: stage,
            slgp_id: slgp_id,
            acmp_id:acmp_id,
            time_period:time_period,
            start_date:start_date,
            _token: _token,
        },
        dataType: "json",
        success: function (data) {
            console.log(data);
            $('#ajax_load').css("display", "none");
            var sub_head = '<tr style="font-size:10px;">' +
            '<th></th>' +
            '<th></th>';
            var html = "";
            var head1="";
            var solt_ref='',sv_ref='',sro_ref='',sm_ref='',so_ref='',sd_ref='';
            var visit=1,s_rate=0,swrv=0,dp_cnt='';
            var t_solt_ref='',t_vsit_ref='',t_rvst_ref='',t_wvst_ref='',t_memo_ref='',t_ordr_ref='',t_deli_ref='',t_srat_ref='';
            var res=[],t_ord=0,t_deli=0;
            var len=data.data.length;
            var slgp=data.slgp;
            var data=data.data;
            switch (stage){
                case "-1":
                    head1 += '<tr><th>Division Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['disn_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="0"  id="' +data[i]['disn_id'] + '">' + ' ' + data[i]['disn_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "0":
                    head1 += '<tr><th>District Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['dsct_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="1"  id="' +data[i]['dsct_id'] + '">' + ' ' + data[i]['dsct_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "1":
                    head1 += '<tr><th>Thana Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['than_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="2"  id="' +data[i]['than_id'] + '">' + ' ' + data[i]['than_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "2":
                    head1 += '<tr><th>Ward Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['ward_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="3"  id="' +data[i]['ward_id'] + '">' + ' ' + data[i]['ward_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "3":
                    head1 += '<tr><th>Market Name</th>' +
                    '<th>Total Outlet</th>';
                    for (var i = 0; i <data.length; i++) {
                        html += '<tr>' +
                            '<td>' + data[i]['mktm_name'] + '</td>' +
                            '<td>' + data[i]['total_outlet'] + '</td>';
                        dp_cnt += '<li><a href="#" onclick="getGvtDeeperData(this)" acmp_id="'+acmp_id+'" slgp_id="'+slgp_id+'" stage="4"  id="' +data[i]['mktm_id'] + '">' + ' ' + data[i]['mktm_name'] + ' ⥤&nbsp;&nbsp; ' + '</a></li>';
                        res['tolt']=parseFloat((res['tolt']||0)+parseFloat(data[i]['total_outlet']));
                        for(var j=0;j<slgp.length;j++){
                                solt_ref='solt'+slgp[j].id;
                                sv_ref='v'+slgp[j].id;
                                sro_ref='rv'+slgp[j].id;
                                sm_ref='m'+slgp[j].id;
                                sd_ref='d'+slgp[j].id;
                                so_ref='o'+slgp[j].id;
                                swrv=data[i][sv_ref]-data[i][sro_ref];
                                visit=data[i][sv_ref]==0?1:data[i][sv_ref];
                                memo=parseInt(data[i][sm_ref]);
                                s_rate=(memo*100/visit).toFixed(2);
                                
                                html+='<td>'+data[i][solt_ref]+'</td>'+
                                    '<td>'+data[i][sv_ref]+'</td>'+
                                    '<td>'+data[i][sro_ref]+'</td>'+
                                    '<td>'+swrv+'</td>'+
                                    '<td>'+data[i][sm_ref]+'</td>'+
                                    '<td>'+data[i][so_ref].toFixed(2)+'</td>'+
                                    '<td>'+data[i][sd_ref].toFixed(2)+'</td>'+
                                    '<td>'+s_rate+" %"+'</td>';
                                t_solt_ref='solt'+slgp[j].id;
                                t_vsit_ref='vsit'+slgp[j].id;
                                t_rvst_ref='rvst'+slgp[j].id;
                                t_wvst_ref='wvst'+slgp[j].id;
                                t_memo_ref='memo'+slgp[j].id;
                                t_ordr_ref='ordr'+slgp[j].id;
                                t_deli_ref='deli'+slgp[j].id;
                                t_srat_ref='srat'+slgp[j].id;
                                res[t_solt_ref]=parseFloat((res[t_solt_ref]||0)+parseFloat(data[i][solt_ref]));
                                res[t_vsit_ref]=parseFloat((res[t_vsit_ref]||0)+parseInt(data[i][sv_ref]));
                                res[t_rvst_ref]=parseFloat((res[t_rvst_ref]||0)+parseFloat(data[i][sro_ref]));
                                res[t_wvst_ref]=parseFloat((res[t_wvst_ref]||0)+swrv);
                                res[t_memo_ref]=parseFloat((res[t_memo_ref]||0)+memo);
                                res[t_ordr_ref]=parseFloat((res[t_ordr_ref]||0)+parseFloat(data[i][so_ref]));
                                res[t_deli_ref]=parseFloat((res[t_deli_ref]||0)+parseFloat(data[i][sd_ref]));
                                res[t_srat_ref]=parseFloat((res[t_srat_ref]||0)+parseFloat(s_rate));
                        
                        }
                        html+='</tr>'
                    }
                    break;
                case "4":
                    if (stage == 4) {
                        html = "<h4>No Data </h4>";
                        head1 = '';
                        sub_head = '';
                    }
                    break;
                default:
                    html="<h1>No Matched</html>";
                    break;
            }
            if(stage !=4){
                for (var i = 0; i <slgp.length; i++) {
                    head1 += '<th colspan="8" style="text-align:center;">' +slgp[i]['slgp_name'] + '</th>';
                    sub_head += '<th>Outlet</th>'+'<th>Visit</th>'+'<th>RO-Visit</th>' +'<th>WR-Visit</th>'+ '<th>Memo</th>'+ 
                                '<th>Exp</th>'+
                                '<th>Delivery</th>'+ 
                                '<th>Strike Rate(%)</th>';
                }
                head1 += '</tr>';
                sub_head += '</tr>';

                html+='<tr style="font-weight:bold;">'+
                        '<td>GT</td>'+
                        '<td>'+res['tolt']+'</td>';
                for(var j=0;j<slgp.length;j++){
                    t_solt_ref='solt'+slgp[j].id;
                    t_vsit_ref='vsit'+slgp[j].id;
                    t_rvst_ref='rvst'+slgp[j].id;
                    t_wvst_ref='wvst'+slgp[j].id;
                    t_memo_ref='memo'+slgp[j].id;
                    t_ordr_ref='ordr'+slgp[j].id;
                    t_deli_ref='deli'+slgp[j].id;
                    t_srat_ref='srat'+slgp[j].id;
                    t_ord=res[t_ordr_ref]?res[t_ordr_ref].toFixed(2):0.00;
                    t_deli=res[t_deli_ref]?res[t_deli_ref].toFixed(2):0.00;
                    html+='<td>'+res[t_solt_ref]+'</td>'+
                    '<td>'+res[t_vsit_ref]+'</td>'+
                    '<td>'+res[t_rvst_ref]+'</td>'+
                    '<td>'+res[t_wvst_ref]+'</td>'+
                    '<td>'+res[t_memo_ref]+'</td>'+
                    '<td>'+t_ord+'</td>'+
                    '<td>'+t_deli+'</td>'+
                    '<td>'+(res[t_srat_ref]/(len+1)).toFixed(2)+'</td>';
                
                }                                    
                html+='</tr>';
            }
            emptyContentAndAppendDataTrack(head1+sub_head,html)
            $('#all_dp_content1').html(dp_cnt);

            $('#rpt').height($("#tableDiv_traking_gvt").height() + 150);

        }, error: function (error) {
            $('#ajax_load').css("display", "none");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            })
            console.log(error);
        }
    });
    
}
function exportDetailExecutiveGVTReport(){
    var slgp_id = $('#sh_slgp_id').val();
    var acmp_id = $('#sh_acmp_id').val();
    var _token = $("#_token").val();
    var time_period=$('#sh_date_gvt').val();
    $('#gvt_load').show();
    $.ajax({
        type:"post",
        url:"/exportDetailExecutiveGVTReport",
        data:{
            slgp_id:slgp_id,
            acmp_id:acmp_id,
            time_period:time_period,
            _token:_token
        },
        xhrFields:{
            responseType: 'blob'
        },
       //dataType:"blob",
        success:function(data){
            $('#gvt_load').hide();
            //console.log(data);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
				link.download = `Executive_Summary_GVT_Details_Report.xlsx`;
			
            link.click();
			Swal.fire({
				text: 'Thanks!',
			});
        },
        error:function(error){
            console.log(error);
        }
    });
}