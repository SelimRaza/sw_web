

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
            <!-- <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Date Range</label>

                                    <div class="input-group date-picker input-daterange">


                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>

                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <!-- start field -->
                        
                                <?php echo csrf_field(); ?>
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div  class="col-md-12 col-sm-12 col-xs-12 tracing">
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
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
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
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
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Block Type<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="block_type"
                                                id="block_type">
                                            
                                            <option value="">Select Type</option>
                                            <option value="1">Invoice</option>
                                            <option value="17">Special Block</option>
                                            <option value="14">Over Due Block</option>
                                            <option value="9">Credit Block</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 " >
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Start Date<span class="required">*</span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg start_date"
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
                                        <input type="text" class="form-control in_tg start_date"
                                                name="end_date"
                                                id="end_date" value="<?php echo date('Y-m-d'); ?>"
                                                autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="chnl_id">Channel<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="chnl_id"
                                                id="chnl_id">
                                            
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $chnl_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chnl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($chnl->id); ?>"><?php echo e($chnl->chnl_code.'-'.$chnl->chnl_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="sv_id">Depot<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="dlrm_id"
                                                id="dlrm_id">
                                            
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $dlrm_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dlrm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($dlrm->id); ?>"><?php echo e($dlrm->dlrm_code.'-'.$dlrm->dlrm_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="sv_id">Supervisor Id<span class="required"></span>
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
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                     <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Staff Id<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="staff_id" placeholder="Staff Id" name="staff_id">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Site Code<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="site_code" placeholder="Site Code" name="site_code">
                                    </div>
                                </div>
                                <div class="item form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                            for="start_date">Order No<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="ordm_ornm" placeholder="Order no" name="ordm_ornm">
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">                               
                                        <!-- <input type="button" class="btn btn-success" id="show_pricelist" value="Show" onclick="getPriceList()"> -->
                                        <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-10 col-sm-offset-10">
                                            <button class="btn btn-success btn-block" type="submit" onclick="filterData()">Show</button>
                                        </div> 
                                </div>

                            </div>
                        
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
                                <h1 style="text-align:center;">Maintain Block Order</h1>
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="block_order_report" style="width:100%;overflow-x:auto;">
                                <a href="#"
                                    onclick="exportTableToCSV('block_order_report<?php echo date('Y_m_d'); ?>.csv','block_order_report')"
                                    class="btn btn-primary"
                                    id="employee_sales_traking_report_slgp" style="float:right;">Export
                                    CSV File
                                </a>
                                <table class="table table-responsive" id="block_data">
                                    <thead>
                                    <tr>
                                        <th>S/L</th>
                                        <th>Ord No</th>
                                        <th>Dpt Name</th>
                                        <th>Group</th>
                                        <th>Ord Amnt</th>
                                        <th>Ord Time</th>
                                        <th>SV Name</th>
                                        <th>SR Name</th>
                                        <th>Olt Name</th>
                                        <th>Olt limt</th>
                                        <th>Olt Days</th>
                                        <th>SPB Time</th>
                                        <th>CDB Time</th>
                                        <th>ODB Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
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
        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                //change ".checkbox" checked status
            });
        });

        $('.checkbox').change(function () { //".checkbox" change
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false
            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });
        $('.cmn_select2').select2();

        function filterData() {
            var acmp_id = $("#acmp_id").val();
            var slgp_id = $("#slgp_id").val();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var emp_id = $("#staff_id").val();
            var block_type = $("#block_type").val();
            var site_code = $("#site_code").val();
            var chnl_id = $("#chnl_id").val();
            var ordm_ornm = $("#ordm_ornm").val();
            var sv_id = $("#sv_id").val();
            var _token = $("#_token").val();
            console.log(start_date + end_date);
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/block/filterMaintainBlock",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    slgp_id: slgp_id,
                    acmp_id:acmp_id,
                    emp_id: emp_id,
                    block_type: block_type,
                    site_code: site_code,
                    ordm_ornm: ordm_ornm,
                    sv_id: sv_id,
                    chnl_id: chnl_id,
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
                        var readonly1 = '';
                        if (data[i].status_id != 1) {
                            readonly1 = 'disabled readonly'
                        }
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].order_id + '</td>' +
                            '<td>' + data[i].depo_name + '</td>' +
                            '<td>' + data[i].slgp_name + '</td>' +
                            '<td>' + data[i].order_amount + '</td>' +
                            '<td>' + data[i].order_date_time + '</td>' +
                            '<td>' + data[i].sv_name + '</td>' +
                            '<td>' + data[i].user_name+'-'+data[i].emp_name + '</td>' +
                            "<td>" + data[i].site_code+'-'+data[i].site_name + "</td>" +
                            "<td>" + data[i].stcm_limt + "</td>" +
                            "<td>" + data[i].stcm_days + "</td>" +
                            "<td>" + data[i].special + "</td>" +
                            "<td>" + data[i].cdb + "</td>" +
                            "<td>" + data[i].odb + "</td>" +
                            "<td>" + data[i].status_name + "</td>";
                        html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/printer/order/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>Print </a>";
                        if (data[i].status_id == 17) {
                            html += "<a target='_blank' href='<?php echo e(URL::to('/')); ?>/block/specialRelease/" +  data[i].so_id + "' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i> SP Release </a>";
                        }
                        if (data[i].status_id == 9) {
                            html += "<a target='_blank' href='<?php echo e(URL::to('/')); ?>/block/creditRelease/" +  data[i].so_id + "' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i> CDB Release </a>";
                        }
                        if (data[i].status_id == 14) {
                            html += "<a target='_blank' href='<?php echo e(URL::to('/')); ?>/block/overDueRelease/" +  data[i].so_id + "' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i>ODB Release </a>";
                        }
                        html += '</td></tr>';
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
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/blockOrder/maintain_order.blade.php ENDPATH**/ ?>