
const date='<option value="1">Yesterday</option>' +
                    '<option value="10">This Week</option>' +
                    '<option value="-1">Single Date</option>' +
                    '<option value="11">Last Week</option>' +
                    '<option value="12">Previous Week</option>' +
                    '<option value="5">As Of</option>'+
                    '<option value="30">Last Month</option>'+
                    '<option value="">Custom Date?</option>'; 
const date_all='<option value="0">Today</option>' +
                    '<option value="-1">Single Date</option>' +
                    '<option value="1">Yesterday</option>' +
                    '<option value="10">This Week</option>' +
                    '<option value="11">Last Week</option>' +
                    '<option value="12">Previous Week</option>' +
                    '<option value="5">As Of</option>'+
                    '<option value="">Custom Date?</option>';
const date_gvt_h='<option value="1">Yesterday</option>' +
                '<option value="10">This Week</option>' +
                '<option value="-1">Single Date</option>' +
                '<option value="11">Last Week</option>' +
                '<option value="12">Previous Week</option>' +
                '<option value="5">As Of</option>'+
                '<option value="30">Last Month</option>';
const history='<option value="0">Today</option>'+
                '<option value="1">Yesterday</option>' +
                '<option value="10">This Week</option>' +
                '<option value="11">Last Week</option>' +
                '<option value="12">Previous Week</option>' +
                '<option value="5">As Of</option>'+
                '<option value="">Custom Date?</option>';
const date_sales_h='<option value="0">Today</option>' +
                '<option value="1">Yesterday</option>' +
                '<option value="-1">Single Date</option>' +
                '<option value="10">This Week</option>' +
                '<option value="11">Last Week</option>' +
                '<option value="12">Previous Week</option>' +
                '<option value="5">As Of</option>'; 
$("input[type='radio'][name='reportType']").click(function () {
    var reportType = $("input[name='reportType']:checked").val();
    customDateHide()
    emptyFilter();
    appendDate(date_all);
    resetGetReportFunction();
    $('#line_chart').hide();
    switch (reportType){
        
        case "sr_hourly_activity":
            appendDate(date);
            $('#sales_heirarchy').show();
            //showSalesHierarchyCommonFilter();
            hideIsDetailsOrSummaryFilter();
            break;
        case "weekly_outlet_summary":
            hideIsDetailsOrSummaryFilter();
            hideFilterArea()
            appendDate(date_all);
            $('.year_mnth').show();
            $('.sr_id').show();
            $('#sales_heirarchy').show();
            $('.zone_div').show();
            $('#dirg_id_div').show();
            $('.start_date_period_div').hide();
            break;
        case "trip_print":
            hideFilterArea();
            $('#trip_filter').show();
            break;
        case "order_report":
        case "Tele_NonProductive_Reason_Summary":
            appendDate(date_all);
            showIsDetailsOrSummaryFilter();
            customDateHide();
            $('#sales_heirarchy').show();
            break;
        case "np_reason_chart":
            appendDate(date_all);
            hideIsDetailsOrSummaryFilter();
            customDateHide();
            $('#sales_heirarchy').show();
            break;
        case "item_summary":
            appendDate(date_all);
            $('#sales_heirarchy').show();
            break;
        case "sr_activity_gvt_hierarchy":
        case "sr_activity_sales_hierarchy":
            hideFilterArea();
            customDateHide();
            //$('#history_usr').show();
            $('#history').show();
            break;
        case "activity_summary":
            hideFilterArea();
            customDateHide();
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
            //$('#dtls_sum_div').show();
            detailsSumValueAppend(1)
            $('#sr_sv_div').show();
            break;
        case "zone_summary":
            hideFilterArea();
            $('#sales_heirarchy').show();
            //$('#dtls_sum_div').show();
            detailsSumValueAppend(2)
            break;
        case "market_outlet_sr_outlet":
        case "outlet_coverage":
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('.gvt').show();
            break;
        case "special_budget":
            hideFilterArea();
            $('#accounts_filter').show();
            $('#year_month1_budget').show();
            break;
        case "credit_budget":
            hideFilterArea();
            $('#accounts_filter').show();
            $('#year_month1_budget').hide();
            break;
        case "sr_item_survey":
            $('#sales_heirarchy').show();
            break;
        default:
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('.zone_div').show();
            $('#dirg_id_div').show();
            break;
        
    }
});
$('#sh_date').html(date_sales_h);
function hideFilterArea(){
    $('#sales_heirarchy').hide();
    $('#accounts_filter').hide();
    $('#asset_div').hide();
    $('#history').hide();
    $('#ord_flag_div').hide();
    $('#dtls_sum_div').hide();
    $('#sr_sv_div').hide();
    $('#sr_zone_div').hide();
    $('#history_usr').hide();
    $('.outlet_weekly').hide();
    $('.gvt').hide();
}
function appendDate(date){
    $('#start_date_period').empty();
    $('#start_date_period').append(date);
    $('#sh_date_gvt').html(date_gvt_h);
    $('#start_date_period_h').html(date_all);
    $('#sh_date').html(date_sales_h);
}
function customDateHide(){
    console.log('hi');
    $('.year_mnth').hide();
    $('.start_date_period_div').show();
    $('.start_date_period_div_h').show();
    $('.start_date_div').hide();
    $('.start_date_div_h').hide();
}
function hideIsDetailsOrSummaryFilter(){
    $('.is_details').hide();
    $('.sr_id').hide();
}
function showIsDetailsOrSummaryFilter(){
    $('.is_details').show();
}
function emptyFilter(){
    $('#sales_group_id').empty();
    $('#sales_group_id').multiselect('reload');
    $('#zone_id').empty();
    $('#zone_id').multiselect('reload');
    $('#sr_id').empty();
    $('#sr_id').multiselect('reload');
    $('#dirg_id').multiselect('reload');
   
}
function resetGetReportFunction(){
    $('#send').removeAttr('onclick');
    $('#send').attr('onclick', 'getSummaryReport()');
}

function showSalesHierarchyCommonFilter(){
    $('#filter_common').show();
}
function detailsSumValueAppend(type){
    var options=`<option value="1">Details</option>
    <option value="2">Summary</option>
    <option value="3">SV Summary</option>`;
    var option=`<option value="1">Details</option>
    <option value="2">Summary</option>`;
    if(type==1){
        $('#dtls_sum').html(options);
    }
    else{
        $('#dtls_sum').html(option);
    }
    $('#dtls_sum_div').show();

}