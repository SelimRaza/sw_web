
const date_gvt_h='<option value="1">Yesterday</option>' +
                    '<option value="10">This Week</option>' +
                    '<option value="-1">Single Date</option>' +
                    '<option value="11">Last Week</option>' +
                    '<option value="12">Previous Week</option>' +
                    '<option value="5">As Of</option>'+
                    '<option value="30">Last Month</option>'; 
const date_sales_h='<option value="0">Today</option>' +
                    '<option value="1">Yesterday</option>' +
                    '<option value="-1">Single Date</option>' +
                    '<option value="10">This Week</option>' +
                    '<option value="11">Last Week</option>' +
                    '<option value="12">Previous Week</option>' +
                    '<option value="5">As Of</option>'; 
$("input[type='radio']").click(function () {
    var reportType = $("input[name='reportType']:checked").val();
    console.log(reportType)
    console.log('test')
    switch (reportType){
        case "sr_activity_gvt_hierarchy":
        case "sr_activity_sales_hierarchy":
            hideFilterArea();
            //$('#history_usr').show();
            $('#history').show();
            break;
        case "activity_summary":
            hideFilterArea();
            $('#history').show();
            break;
        case "note_summary":
        case "note_report":
            hideFilterArea();
            $('#sales_heirarchy').show();
            break;
        case "asset_details":
        case "asset_summary":
        case "asset_order":
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('#asset_div').show();
            break;

        case "class_wise_order_report_amt":         
        case "cat_wise_order_report_amt":
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('#ord_flag_div').show();
            $('#dtls_sum_div').show();
            $('#sr_zone_div').show();
            break;      
        case "sr_wise_order_delivery":      
        case "sku_wise_order_delivery":      
        case "zone_wise_order_delivery_summary":      
        case "sr_wise_order_details":      
            hideFilterArea();
            $('#sales_heirarchy').show();
            break;
        case "sr_productivity":
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('#dtls_sum_div').show();
            $('#sr_sv_div').show();
            break;
        case "zone_summary":
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('#dtls_sum_div').show();
            break;
        case "weekly_outlet_summary":
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('.outlet_weekly').show();
            $('.start_date_period_div').hide();
            $('.gvt').hide();
            $('.zone_div').show();
            $('#dirg_id_div').show();
            break;
        
        case undefined:
            $('#sh_date_gvt').empty();
            $('#sh_date_gvt').append(date_gvt_h);
            $('#sh_date').empty();
            $('#sh_date').append(date_sales_h);
            break;
        case "attendance_report":
            console.log('Hello')
            hideFilterArea();
            $('.attendance_type_div').show();
            $('#sales_heirarchy').show();
            break;
        default:
            hideFilterArea();
            $('#sales_heirarchy').show();
            break;
        
    }
});

function hideFilterArea(){
    $('#sales_heirarchy').hide();
    $('#asset_div').hide();
    $('#history').hide();
    $('#ord_flag_div').hide();
    $('#dtls_sum_div').hide();
    $('#sr_sv_div').hide();
    $('#sr_zone_div').hide();
    $('#history_usr').hide();
    $('.outlet_weekly').hide();
    $('.attendance_type_div').hide();
}
function resetGetReportFunction(){
    $('#send').removeAttr('onclick');
    $('#send').attr('onclick', 'getSummaryReport()');
}

    // case "sr_activity":
    // case "sr_productivity":
    // case "sr_non_productivity":
    // case "sr_summary_by_group":
    // case "sr_activity_hourly_visit":
    // case "sr_wise_item_summary_quatar":