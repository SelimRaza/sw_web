
function reportRequestPlacementAdvance() {
    hide_me();
    var reportType = $("input[name='reportType']:checked").val();
    var acmp_id = $('#acmp_id').val();
    var dirg_id = $('#dirg_id').val();
    var sales_group_id = $('#sales_group_id').val();
    var zone_id = $('#zone_id').val();
    var dist_id = $('#dist_id').val();
    var than_id = $('#than_id').val();
    var _token = $("#_token").val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var astm_id = $('#astm_id').val();
    var rtype=$('#dtls_sum').val();
    var utype=$('#sr_sv').val();
    var sr_zone=$('#sr_zone').val();
    var validityCheck = false;

    if (reportType === undefined) {
        alert('Please select report');
        return false;
    }
    else if (reportType == '') {
        alert('Please select report');
        return false;
    }
    if (reportType == 'market_outlet_sr_outlet') {
        validityCheck = validateInputField(reportType, acmp_id, sales_group_id, start_date_period, dist_id, than_id);
    }
    else {
        validityCheck = validateInputField(reportType, acmp_id, sales_group_id, start_date_period);
    }
    if (validityCheck != false) {
        var master_email=$('#email_address').val(); 
        Swal.fire({
            title: 'Please confirm your email address',
            html: `<input type="email" style="width:80%;text-align:center;" id="email_address" class="swal2-input" value=${master_email}>`,
            inputAttributes: {
              autocapitalize: 'off'
            },
            width: '500px',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            allowOutsideClick: () => !Swal.isLoading(),
            backdrop:true,
          }).then((result) => {
            if (result.isConfirmed) {
              let email_address= Swal.getPopup().querySelector('#email_address').value;
              $('#ajax_load').css("display", "block");
              $.ajax({
                type: "POST",
                url: "/commonReportRequestAdvance",
                data: {
                    reportType: reportType,
                    acmp_id: acmp_id,
                    zone_id: zone_id,
                    sales_group_id: sales_group_id,
                    dist_id: dist_id,
                    than_id: than_id,
                    dirg_id: dirg_id,
                    rtype: rtype,
                    utype: utype,
                    sr_zone: sr_zone,
                    start_date: start_date,
                    end_date: end_date,
                    astm_id:astm_id,
                    email_address:email_address,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    Swal.fire({
                        icon:'success',
                        title: 'Success!',
                        text: 'Thanks for your report request. You will get notified via email withing next 24 Hours!!',
                    })
                }, error: function (error) {
                    console.log(error);
                    $('#ajax_load').css("display", "none");
                    Swal.fire({
                        icon:'warning',
                        title: 'Please wait!',
                        text: 'Check it by clicking the  "requested report status". If you do not find it here after 5 minutes then make another request!!!',
                    })
                }
              });
            }
          })
    }
}


