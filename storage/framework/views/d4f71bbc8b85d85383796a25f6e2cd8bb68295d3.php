

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <?php if(Session::has('success')): ?>
                        <div class="alert alert-success">
                            <strong>Success!</strong><?php echo e(Session::get('success')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(Session::has('danger')): ?>
                        <div class="alert alert-danger">
                            <strong>Error! </strong><?php echo e(Session::get('danger')); ?>

                        </div>
                    <?php endif; ?>


                    <div class="x_panel">
                        <div class="x_title">


                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            <h3 class="text-center">User Dues & Collection Details</h3>
                            <form class="form-horizontal form-label-left" action="<?php echo e(URL::to('/sr-balance')); ?>"
                                id="base-map" method="post" enctype="multipart/form-data" autocomplete="off">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <?php echo e(csrf_field()); ?>

                                <div class="row justify-content-between">

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>STAFF ID</label>
                                            <input type="text" class="form-control" name="staff_id" id="staff_id"
                                                aria-describedby="helpId" placeholder="STAFF ID">
                                            <?php $__errorArgs = ['staff_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="required-field"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>SITE CODE(From)</label>
                                            <input type="text" class="form-control" name="site_code" id="site_code"
                                                aria-describedby="helpId" placeholder="SITE CODE">
                                            <?php $__errorArgs = ['site_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="required-field"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>SITE CODE(To)</label>
                                            <input type="text" class="form-control" name="site_code2" id="site_code2"
                                                aria-describedby="helpId" placeholder="SITE CODE">
                                            <?php $__errorArgs = ['site_code2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="required-field"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>START DATE</label>
                                            <input type="date" class="form-control start_report_date"
                                                name="start_report_date" id="start_report_date" aria-describedby="helpId"
                                                placeholder="DATE" autocomplete="off">
                                            <?php $__errorArgs = ['start_report_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="required-field"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>END DATE</label>
                                            <input type="date" class="form-control end_report_date"
                                                name="end_report_date" id="end_report_date" aria-describedby="helpId"
                                                placeholder="DATE" autocomplete="off">
                                            <?php $__errorArgs = ['end_report_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <small class="required-field"><?php echo e($message); ?></small>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-sm-12">
                                        <label>REPORT TYPE</label>
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" class="flat" name="report_type" id="report_type"
                                                    value="1" checked onchange="showField(this.value)" /> Both
                                            </label>
                                            <label>
                                                <input type="radio" class="flat" name="report_type" id="report_type"
                                                    value="2" onchange="showField(this.value)" /> Collected
                                            </label>
                                            <label>
                                                <input type="radio" class="flat" name="report_type" id="report_type"
                                                    value="3" onchange="showField(this.value)" /> Dues
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-md-2 col-sm-12">
                                        <button id="send" type="button" onclick="getFilterData()"
                                            class="btn btn-success">Show</button>
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>

                    <div class="x_panel" id="show_report">
                        <div class="x_content">
                                    <button onclick="exportTableToCSV('SR_SITE_BALANCE<?php echo date('Y_m_d'); ?>.csv','show_report')"
                                                class="btn btn-warning">Export
                                    </button>
                            <table id="datatables" class="table table-bordered table-responsive  search-table"
                                data-page-length='100' style="width: 100%;">
                                <thead>
                                    <tr class="tbl_header_light">
                                        <th class="cell_left_border">SL</th>
                                        <th>ORDER_DATE</th>
                                        <th>STAFF_ID</th>
                                        <th>STAFF_NAME</th>
                                        <th>SITE_CODE</th>
                                        <th>SITE_NAME</th>
                                        <th>ORDER_ORNM</th>
                                        <th>DELV_AMNT</th>
                                        <th>COLL_AMNT</th>
                                        <th>DUES</th>
                                    </tr>
                                </thead>
                                <tbody id="show_result">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal fade bs-example-modal-lg" tabindex="-1" aria-hidden="true"
                        id="showSrProposeSitesDetails" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title text-center">Details
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="loader" id="visit_out_load_details"
                                        style="display:none; margin-left:35%;">
                                    </div>
                                    
                                    <table class="table font_color" data-page-length="50" id="tl_dynamic">
                                        <thead id="employee-wise-details">
                                            <tr class="tbl_header_light">
                                                <th class="cell_left_border">SL</th>
                                                <th>TRN_DATE</th>
                                                <th>TRN_NO</th>
                                                <th>DEBIT_AMOUNT</th>
                                                <th>CREDIT_AMOUNT</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reference-details">


                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>

    <style>
        .required-field {
            color: red
        }

        .fa-eye {
            cursor: pointer;
            padding-left: 10px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 100%;
        }

        .modal-lg {
            width: 1150px;
        }

    </style>

    <script type="text/javascript">
        $(".report_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });


        $(document).ready(function() {
            $("select").select2({
                width: 'resolve'
            });
        });

        $("#show_report").hide();

        function exportTableToExcel(elem,filename, tableId){
            var BOM = "\uFEFF";
            var table=document.getElementById(tableId);
            var html = table.outerHTML;
            console.log(url);
            // var url = 'data:application/vnd.ms-excel,' + encodeURI(BOM+html); // Set your html table into url 
            var url = 'data:application/vnd.ms-excel,' + escape(html); // Set your html table into url 
            
            elem.setAttribute("href", url);
            $(elem).attr("download",filename);
            return false;
        }

        
        



        function getFilterData() {
            $("#show_result").empty();
            var staff_id = $('#staff_id').val();
            var site_code = $('#site_code').val();
            var site_code2 = $('#site_code2').val();
            if(site_code2==''){
                site_code2=site_code;
            }
            var start_report_date = $('#start_report_date').val();
            var end_report_date = $('#end_report_date').val();

            var report_type = $("input[name='report_type']:checked").val();

            // var report_type = $('#report_type').val();
            var _token = $("#_token").val();

            $("#show_report").hide();

            if (staff_id != "" || site_code != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(URL::to('/')); ?>/sr-balance",
                    data: {
                        staff_id: staff_id,
                        site_code: site_code,
                        site_code2: site_code2,
                        start_report_date: start_report_date,
                        end_report_date: end_report_date,
                        report_type: report_type,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function(data) {

                        if (data.status == 'error') {
                            $('#ajax_load').css("display", "none");
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            })
                        }
                        console.log(data)
                        var count = 1;
                        for (var i = 0; i < data.data.length; i++) {
                            html += '<tr class="">' +
                                '<td class="cell_left_border">' + count + '</td>' +
                                '<td>' + data.data[i]['ORDM_DATE'] + '</td>' +
                                '<td>' + data.data[i]['AEMP_USNM'] + '</td>' +
                                '<td>' + data.data[i]['aemp_name'] + '</td>' +
                                '<td>' + data.data[i]['SITE_CODE'] + '</td>' +
                                '<td>' + data.data[i]['site_name'] + '</td>' +
                                '<td>' + data.data[i]['ORDM_ORNM'] + '</td>' +
                                '<td>' + data.data[i]['DELV_AMNT'] + '</td> ' +
                                '<td>' + data.data[i]['COLLECTION_AMNT'] + '</td> ' +
                                '<td>' + data.data[i]['due'] +
                                ' <i class="fa fa-eye fa-x icon" onclick="getRefData(\'' + String(data.data[i][
                                    'ORDM_ORNM'
                                ]) + '\')"></i></td> </tr>';
                            count++;
                        }
                        // var currentDate = '<?php echo e(date("Y_m_d")); ?>';
                        // var user=staff_id??site_id;
                        // var file_name='SR_BALANCE_'.user;
                        // var exportLink = '<a onclick="exportTableToExcel(this, \'' + file_name + currentDate + '.xls\', \'tl_dynamic\')" class="btn btn-sm" style="background-color:green;color:white;">Excel</a>';

                        // $('#export_excel').html(exportLink);
                        $("#show_report").show();
                        $("#show_result").append(html);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;
                        $('#tableDiv').show();
                    },
                    error: function(error) {
                        console.log(error);
                        swal.fire({
                            icon: 'error',
                            text: 'Something Went Wrong!!',
                        });
                        $('#ajax_load').css("display", "none");

                    }

                });


            } else {
                alert("Please enter staff or site code and Try again!!!");
            }
        }

        function getRefData(value) {
            $('#ajax_load').css("display", "block");

            console.log(value)
            var ref_no = value;
            var _token = $("#_token").val();

            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/sr-collection-ref-data",
                data: {
                    ref_no: ref_no,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    var html = '';
                    var debit_total_amount = 0;
                    var credit_total_amount = 0;
                    $('#ajax_load').css("display", "none");
                    $("#showSrProposeSitesDetails").modal({
                        backdrop: false
                    });
                    $('#showSrProposeSitesDetails').modal('show');

                    for (let i = 0; i < data.data.length; i++) {
                        debit_total_amount += parseFloat(data.data[i].DEBIT_AMNT) || 0; // Ensure it's a number, handle NaN
                        credit_total_amount += parseFloat(data.data[i].CRECIT_AMNT) || 0; // Ensure it's a number, handle NaN
                        html += `<tr class="tbl_body_gray">
                                        <td> ${i+1} </td>
                                        <td>${data.data[i].TRN_DATE} </td>                                               
                                        <td>${data.data[i].TRANSACTION_ID} </td>                                               
                                        <td>${data.data[i].DEBIT_AMNT} </td>                                          
                                        <td>${data.data[i].CRECIT_AMNT} </td>                                          
                                    </tr>`;
                    }

                    html += `<tr class="tbl_body_gray">
                        <td colspan="3"></td>
                        <td>Debit Total: ${debit_total_amount}</td>
                        <td>Credit Total: ${credit_total_amount}</td>
                        <td></td>
                    </tr>`;
                    console.log(debit_total_amount);

                    $('#reference-details').html(html);

                    $('#ajax_load').css("display", "none");
                },
                error: function(error) {
                    console.log(error);
                    swal.fire({
                        icon: 'error',
                        text: 'Something Went Wrong!!',
                    });
                    $('#ajax_load').css("display", "none");

                }

            });
        }
    </script>
    <script>
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
        function exportTableToCSV(filename, tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#' + tableId + '  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/collection/sr_balance.blade.php ENDPATH**/ ?>