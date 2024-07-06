

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>All Block Order</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <!-- start field -->
                        
                                <?php echo csrf_field(); ?>
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <!-- <div  class="col-md-12 col-sm-12 col-xs-12"> -->
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Company<span class="required"></span>
                                    </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="acmp_id"
                                                    id="acmp_id"
                                                    onchange="getGroup(this.value)">
                                                
                                                <option value="">Select Company</option>
                                                <?php $__currentLoopData = $acmp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                 <option value="<?php echo e($acmp->id); ?>"><?php echo e($acmp->acmp_code ."-".$acmp->acmp_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Group<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="slgp_id"
                                                id="slgp_id">

                                            <option value="">Select Group</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-4 col-sm-4 col-xs-12" >
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Start Date<span class="required">*</span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg"
                                                name="start_date"
                                                id="start_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">End Date<span class="required">*</span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg"
                                                name="end_date"
                                                id="end_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="sv_id">Manager ID<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="sv_id"
                                                id="sv_id">
                                            
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $sv_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sv->id); ?>"><?php echo e($sv->aemp_usnm.'-'.$sv->aemp_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                     <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">SR ID<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="sr_usnm" placeholder="Staff Id" name="staff_id">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Site Code<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="site_code" placeholder="Site Code" name="site_code">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Order No<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="ordm_ornm" placeholder="Order no" name="ordm_ornm">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">                               
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="radio" id="html" name="rpt_type" value="1">
                                            <label for="html">Special Budget</label><br>
                                            <input type="radio" id="lab1" name="rpt_type" value="2" checked>
                                            <label for="lab1">Credit</label><br>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-10">
                                            <button class="btn btn-success btn-block" type="submit" onclick="filterData()">Show</button>
                                        </div> 
                                </div>

                            <!-- </div> -->
                        
                            <!-- end field -->
                        </div>
                    </div>
                </div>
            <div class="row">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong></strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong></strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12">
                    <div class="x_panel">


                            <div class="x_title">
                                <h3 style="text-align:center;">Credit Approval Details</h3>
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="block_order_report" style="width:100%;overflow-x:auto;">
                                <a href="#"
                                    onclick="exportTableToCSV('credit_req_details<?php echo date('Y_m_d'); ?>.csv','block_order_report')"
                                    class="btn btn-primary"
                                    id="employee_sales_traking_report_slgp" style="float:right;">Export
                                    CSV File
                                </a>
                                <table class="table table-responsive" id="block_data">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>ORDER DATE</th>
                                        <th>ORDER NO</th>
                                        <th>GROUP NAME</th>
                                        <th>SR ID</th>
                                        <th>SR NAME</th>
                                        <th>OUTLET CODE</th>
                                        <th>OUTLET NAME</th>
                                        <th>ORDER AMNT</th>
                                        <th>CREDIT REQUEST</th>
                                        <th>APPROVED AMNT</th>
                                        <th>REQUEST STATUS</th>
                                        <th>APPROVER ID</th>
                                        <th>APPROVER NAME</th>
                                        <th>COLLECTION DATE</th>
                                        <th>DUES AMNT</th>
                                        <th>COLLECTION STATUS</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">

                                    </tbody>
                                </table>

                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#start_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });
        $("#end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });
        $('.cmn_select2').select2();

        function filterData() {
            var acmp_id = $("#acmp_id").val();
            var slgp_id = $("#slgp_id").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var emp_id = $("#sr_usnm").val();
            var site_code = $("#site_code").val();
            var ordm_ornm = $("#ordm_ornm").val();
            var sv_id = $("#sv_id").val();
            var rpt_type = $("input[type='radio'][name=rpt_type]:checked").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/cpcr_spbm_details/report",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    slgp_id: slgp_id,
                    acmp_id:acmp_id,
                    sr_usnm: emp_id,
                    site_code: site_code,
                    ordm_ornm: ordm_ornm,
                    rpt_type: rpt_type,
                    sv_id: sv_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    for (var i = 0; i < data.length; i++) {
                        let color='';
                        if(data[i].spbd_type=='Credit Req'){
                            color='background-color:#F8F9D7';
                        }else{
                            
                        }
                        html += '<tr style="'+color+'">' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].ordm_date + '</td>' +
                            '<td>' + data[i].ordm_ornm + '</td>' +
                            '<td>' + data[i].slgp_name + '</td>' +
                            '<td>' + data[i].sr_id + '</td>' +
                            '<td>' + data[i].sr_name + '</td>' +
                            '<td>' + data[i].site_code + '</td>' +
                            '<td>' + data[i].site_name+'</td>' +
                            "<td>" + data[i].ordm_amnt+ "</td>" +
                            "<td>" + data[i].sreq_amnt + "</td>" +
                            "<td>" + data[i].sapr_amnt + "</td>" +
                            "<td>" + data[i].spbd_type + "</td>" +
                            "<td>" + data[i].sv_id + "</td>" +
                            "<td>" + data[i].sv_name + "</td>" +
                            "<td>" + data[i].cpcr_cdat + "</td>" +
                            "<td>" + data[i].due_amnt + "</td>" +
                            "<td>" + data[i].c_status + "</td></tr>";
                        count++;
                    }

                    $("#cont").append(html)
                    // $('#block_data').DataTable({
                    //     dom: 'Bfrtip',
                    //     retrieve: true,
                    //     pageLength:5,
                    //     buttons: [
                    //         'copy', 'csv', 'excel', 'pdf', 'print'
                    //     ]
                    // });


                },error:function(error){
                    console.log(error);
                }
            });
        }

        function getGroup(slgp_id) {
                var _token = $("#_token").val();
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(URL::to('/')); ?>/load/report/getGroup",
                    data: {
                        slgp_id: slgp_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html = '<option value="">Select</option>';
                        for (var i = 0; i < data.length; i++) {
                            console.log(data[i]);
                            html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                        }
                        
                            $("#slgp_id").empty();
                            $("#slgp_id").append(html);
                       
                    }
                });
        }



        function exportTableToCSV(filename, tableId) {
                // alert(tableId);
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
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/blockOrder/report.blade.php ENDPATH**/ ?>