
const date='<option value="1">Yesterday</option>' +
                    '<option value="10">This Week</option>' +
                    '<option value="-1">Single Date</option>' +
                    '<option value="11">Last Week</option>' +
                    '<option value="12">Previous Week</option>' +
                    '<option value="5">As Of</option>'+
                    '<option value="30">Last Month</option>'+
                    '<option value="">Custom Date?</option>'; 
const date_all='<option value="0">Today</option>' +
                    '<option value="1">Yesterday</option>' +
                    '<option value="10">This Week</option>' +
                    '<option value="11">Last Week</option>' +
                    '<option value="12">Previous Week</option>' +
                    '<option value="5">As Of</option>'+
                    '<option value="">Custom Date?</option>';
$("input[type='radio'][name='reportType']").click(function () {
    var reportType = $("input[name='reportType']:checked").val();
    customDateHide()
    emptyFilter();
    appendDate(date_all);
    $('#line_chart').hide();
    switch (reportType){
        
        case "sr_hourly_activity":
            appendDate(date);
            $('#sales_heirarchy').show();
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
        case "sr_productivity":
        case "order_report":
        case "np_summary":
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
        default:
            hideFilterArea();
            $('#sales_heirarchy').show();
            $('.zone_div').show();
            $('#dirg_id_div').show();
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
    $('.gvt').hide();
}
function appendDate(date){
    $('#start_date_period').empty();
    $('#start_date_period').append(date);
}
function customDateHide(){
    $('.year_mnth').hide();
    $('.start_date_period_div').show();
    $('.start_date_div').hide();
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