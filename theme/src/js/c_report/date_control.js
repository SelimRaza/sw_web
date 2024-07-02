$("input[type='radio']").click(function () {
    var reportType = $("input[name='reportType']:checked").val();
    if(reportType =='market_outlet_sr_outlet'){
    }
    
    else{
        
        dateFilterOptionControl(reportType);
        $('.start_date_div').hide();
        $('.start_date_period_div').show();
        $('.start_date_period_div_h').show();
        $('.start_date_div_h').hide();
    }
    
    $('#send').removeAttr('onclick');
    $('#send').attr('onclick', 'getSummaryReport()');
    $('#custom_history_rpt_btn').removeAttr('onclick');
    $('#custom_history_rpt_btn').attr('onclick', 'getHReport()');
    
   
});
function dateFilterOptionControl(reportType) {
    var html = '';
    var history = '';
    
    switch(reportType){
        case "sr_hourly_activity":
        case "item_summary":
        case "item_coverage":
            html =  '<option value="1">Yesterday</option>' +
                '<option value="-1">Single Date?</option>' +
                '<option value="10">This Week</option>' +
                '<option value="11">Last Week</option>' +
                '<option value="12">Previous Week</option>' +
                '<option value="5">As Of</option>';
        break;
        default :
            html =  '<option value="0">Today</option>' +
            '<option value="1">Yesterday</option>' +
            '<option value="-1">Single Date?</option>' +
            '<option value="10">This Week</option>' +
            '<option value="11">Last Week</option>' +
            '<option value="12">Previous Week</option>' +
            '<option value="5">As Of</option>' +
            '<option value="">Custom Date?</option>';
        break;
    }
    history = '<option value="0">Today</option>' +
            '<option value="1">Yesterday</option>' +
            '<option value="10">This Week</option>' +
            '<option value="11">Last Week</option>' +
            '<option value="12">Previous Week</option>' +
            '<option value="5">As Of</option>' +
            '<option value="">Custom Date?</option>';
    
    $('#start_date_period').empty();
    $('#start_date_period_h').empty();
    $('#start_date_period_h').append(history);
    $('#start_date_period').append(html);
}



