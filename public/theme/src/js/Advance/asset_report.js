function getAssetDeeperData(v){
    var slgp_id=$(v).attr('slgp_id');
    var zone_id=$(v).attr('zone_id');
    var stage=$(v).attr('stage');
    var start_date=$(v).attr('s_date');
    var end_date=$(v).attr('e_date');
    v.removeAttribute('onclick');
    v.setAttribute('onclick', 'assetHeadingClickData(this)');
    $('#asst_click_head').append(v);
    var _token = $("#_token").val();
    $.ajax({
        type:"POST",
        url:"getAssetDeeperData",
        data:{
            slgp_id:slgp_id,
            zone_id:zone_id,
            stage:stage,
            start_date:start_date,
            end_date:end_date,
            _token:_token
        },
        success:function(data){

        },
        error:function(error){
            console.log(error);
        }
    });
}

function getAssetOutletDetails(v){
    var slgp_id=$(v).attr('slgp_id');
    var astm_id=$(v).attr('astm_id');
    var zone_id=$(v).attr('id');
    var stage=$(v).attr('stage');
    var start_date=$(v).attr('s_date');
    var end_date=$(v).attr('e_date');
    var _token = $("#_token").val();
    $("#asset_modal").modal({backdrop: false});
    $('#asset_modal').modal('show');
    $("#asset_dt_cont").empty();
    $('#asset_load').show();
    $.ajax({
        type:"POST",
        url:"/getAssetOutletDetails",
        data:{
            slgp_id:slgp_id,
            zone_id:zone_id,
            start_date:start_date,
            end_date:end_date,
            astm_id:astm_id,
            _token:_token
        },
        dataType:"json",
        success:function(data){
            $('#asset_load').hide();
            var html='';
            var count=1;
            for (var i = 0; i < data.length; i++) {
                html += '<tr class="ast_olt_tr">' +
                    '<td>' + count + '</td>' +
                    '<td>' + data[i]['site_name'] + '<i id="show" style="color:forestgreen;cursor:pointer;" site_id="'+data[i]['site_id']+'" astm_id="'+astm_id+'" slgp_id="'+slgp_id+'" onclick="getAssetOutletCurrentYearSummary(this)" class="fa fa-info-circle fa-2x  pull-right"></i></td>' +
                    '<td>' + data[i]['site_mob1'] + '</td>' +
                    '<td>' + data[i]['mktm_name'] + '</td>' +
                    '<td>' + data[i]['ward_name'] + '</td>' +
                    '<td>' + data[i]['than_name'] + '</td>' +
                    '<td>' + data[i]['dsct_name'] + '</td>' +
                    '<td>' + data[i]['site_ordr'] + '</td>' +
                    '<td>' + data[i]['ast_itm_ordr'] + '</td>' +
                    '</tr>';
                count++;
            }
            $("#asset_dt_cont").empty();
            $("#asset_dt_cont").append(html);

        },
        error:function(error){
            console.log(error);
        }
    });
}
function getAssetOutletCurrentYearSummary(v){
    var slgp_id=$(v).attr('slgp_id');
    var astm_id=$(v).attr('astm_id');
    var site_id=$(v).attr('site_id');
    var stage=$(v).attr('stage');
    var _token = $("#_token").val();
    $("#asset_olt_year").modal({backdrop: false});
    $('#asset_olt_year').modal('show');
    $("#asset_olt_year_cont").empty();
    $('#asset_olt_year_load').show();
    $.ajax({
        type:"POST",
        url:"/getAssetOutletCurrentYearSummary",
        data:{
            slgp_id:slgp_id,
            astm_id:astm_id,
            site_id:site_id,
            _token:_token
        },
        dataType:"json",
        success:function(data){
            $('#asset_olt_year_load').hide();
            var html='';
            var count=1;
            for (var i = 0; i < data.length; i++) {
                html += '<tr>' +
                    '<td>' + count + '</td>' +
                    '<td>' + data[i]['mnth'] + '</td>' +
                    '<td>' + data[i]['site_name'] + '</td>' +
                    '<td>' + data[i]['site_ordr'] + '</td>' +
                    '<td>' + data[i]['ast_itm_ordr'] + '</td>' +
                    '</tr>';
                count++;
            }
            $("#asset_olt_year_cont").append(html);

        },
        error:function(error){
            console.log(error);
        }
    });
}
function getAssetOutletDetailsThanaWise(v){
    var slgp_id=$(v).attr('slgp_id');
    var astm_id=$(v).attr('astm_id');
    var zone_id=$(v).attr('id');
    var start_date=$(v).attr('s_date');
    var end_date=$(v).attr('e_date');
    var _token = $("#_token").val();
    $("#asset_olt_than").modal({backdrop: false});
    $('#asset_olt_than').modal('show');
    $("#asset_olt_than_cont").empty();
    $('#asset_olt_than_load').show();
    $.ajax({
        type:"POST",
        url:"/getAssetOutletDetailsThanaWise",
        data:{
            slgp_id:slgp_id,
            zone_id:zone_id,
            start_date:start_date,
            end_date:end_date,
            astm_id:astm_id,
            _token:_token
        },
        dataType:"json",
        success:function(data){
            $('#asset_olt_than_load').hide();
            var html='';
            var count=1;
            for (var i = 0; i < data.length; i++) {
                html += '<tr class="ast_olt_tr">' +
                    '<td>' + count + '</td>' +
                    '<td>' + data[i]['than_name'] + '</td>' +
                    '<td>' + data[i]['dsct_name'] + '</td>' +
                    '<td>' + data[i]['site_ordr'] + '</td>' +
                    '<td>' + data[i]['ast_itm_ordr'] + '</td>' +
                    '</tr>';
                count++;
            }
            $("#asset_olt_than_cont").empty();
            $("#asset_olt_than_cont").append(html);

        },
        error:function(error){
            console.log(error);
        }
    });
}


$('#search_asset_olt').on('keyup', function() {
	var searchVal = $(this).val();
	var filterItems = $('.ast_olt_tr');

	if ( searchVal != '' ) {
		filterItems.addClass('hidden');
       // $('.leader:contains("'+searchVal+'")').show();
       $('.ast_olt_tr td:contains("'+searchVal+'")').closest('.ast_olt_tr').css('background-color','#FAF9F6');
       $('.ast_olt_tr td:contains("'+searchVal+'")').closest('.ast_olt_tr').removeClass('hidden');
	} else {
		filterItems.removeClass('hidden').css('background','');
	}
});


function exportTableToCSVForCustomItem(filename, tableId) {
    // alert(tableId);
    var csv = [];
    var rows = document.querySelectorAll('#' + tableId + '  tr');
    for (var i = 0; i < rows.length; i++) {
        if($(rows[i]).is(':visible')){
            var row = [], cols = rows[i].querySelectorAll("td, th");
            for (var j = 0; j < cols.length; j++)
                row.push(cols[j].innerText);
            csv.push(row.join(","));
        }
    }
    downloadCSV(csv.join("\n"), filename);
}

function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    csvFile = new Blob([csv], {type: "text/csv"});
    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}