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
                            <strong>All Group</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

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
                            <div class="item form-group">
                                <?php if($permission->wsmu_crat): ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/employee/create')); ?>">Add
                                        New</a>
                                    <a class="btn btn-success btn-sm"
                                       href="<?php echo e(URL::to('/employee/employeeHrisUpload')); ?>">Add HRIS</a>
                                    <a class="btn btn-success btn-sm"
                                       href="<?php echo e(URL::to('employee/employeeUpload')); ?>">Upload</a>
                                    <a class="btn btn-success btn-sm"
                                       href="<?php echo e(URL::to('get/employee/routeSearch/view')); ?>">Search Route</a>
                                    <a class="btn btn-success btn-sm"
                                       href="<?php echo e(URL::to('employee/get/routeLike/view')); ?>">Route Like</a>
                                <?php endif; ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action="<?php echo e(url('/depot/filterDepotddd')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <?php echo e(csrf_field()); ?>



                                <div class="x_title">
                                    <div class="item form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                   for="name" style="text-align: left">Company<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="acmp_id" id="acmp_id"
                                                        onchange="getGroup(this.value)">
                                                    <option value="">Select Company</option>
                                                    <?php $__currentLoopData = $acmp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmpList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($acmpList->id); ?>"><?php echo e($acmpList->acmp_code); ?>

                                                            - <?php echo e($acmpList->acmp_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                   style="text-align: left">Group<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="slgp_id" id="slgp_id">
                                                    <option value="">Select Group</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                   style="text-align: left">Zone<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="zone_id" id="zone_id">
                                                    <option value="">Select Zone</option>
                                                    <?php $__currentLoopData = $zone; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($zoneList->id); ?>"><?php echo e($zoneList->zone_code); ?>

                                                            - <?php echo e($zoneList->zone_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        
                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                   for="name" style="text-align: left">SV ID<span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input type="text" name="sv_id" class="form-control" id="sv_id">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-6 ">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                   for="name" style="text-align: left">Staff Id<span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input type="text" name="sr_id" class="form-control" id="sr_id">
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <div class="item form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <button id="send" type="button" style="margin-left:10px;"
                                                    class="btn btn-success"
                                                    onclick="getReport()"><span class="fa fa-search"
                                                                                style="color: white;"></span>
                                                <b>Search</b>
                                            </button>
                                        </div>

                                    </div>


                                    <div class="clearfix"></div>
                                </div>
                            </form>
                        </div>


                    </div>
                    <div class="x_panel" id="div_report">
                        <div class="x_content">
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="exportTableToCSV('employee_list_<?php echo date('Y_m_d'); ?>.csv','datatables')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>

                            <table id="datatables" class="table table-bordered table-responsive font_color search-table"
                                   data-page-length='100' style="width: 100%;">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th style="width: 10%">User Id</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Designation</th>
                                    <th>Group</th>
                                    <th>Manager</th>
                                    <th>Mobile Access</th>
                                    <th colspan="2">Action</th>
                                </tr>
                                </thead>
                                <tbody id="cont">

                                </tbody>
                            </table>

                            <!-- end project list -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('table.search-table').tableSearch({
                searchPlaceHolder: 'Search Text'
            });
        });

        $("#div_report").hide();
        $(document).ready(function () {
            $("select").select2({width: 'resolve'});
        });
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function getGroup(slgp_id) {
            $("#slgp_id").empty();
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

                    $("#sales_group_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Group</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#slgp_id").append(html);

                }
            });
        }


        function getReport() {
            $("#cont").empty();
            var acmp_id = $('#acmp_id').val();
            var slgp_id = $('#slgp_id').val();
            var zone_id = $('#zone_id').val();
            var sr_id = $('#sr_id').val();
            let sv_id = $('#sv_id').val();
            var html = '';

            $("#div_report").hide();
            var _token = $("#_token").val();
            //alert(acmp_id);
            //alert(slgp_id);
            if (sr_id != "" || sv_id !="") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(URL::to('/')); ?>/employee/filter/empdetails",
                    data: {
                        acmp_id: acmp_id,
                        slgp_id: slgp_id,
                        zone_id: zone_id,
                        sr_id: sr_id,
                        sv_id: sv_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        let permission=data.permission;
                        var data=data.srData;
                        var count = 1;
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            html += '<tr class="tbl_body_gray">' +
                                '<td class="cell_left_border">' + count + '</td>' +
                                '<td>' + data[i]['id'] + '</td>' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                '<td>' + data[i]['role_name'] + '</td>' +
                                '<td>' + data[i]['edsg_name'] + '</td>' +
                                '<td>' + data[i]['slgp_name'] + '</td>' +
                                '<td>' + data[i]['m_code'] + " - " + data[i]['m_name'] + '</td>' +
                                '<td>' + data[i]['amng_name'] + '</td>';
                                if(permission.wsmu_vsbl){
                                    html+='<td colspan="2"><a href="employee/' + r_id + '" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View</a>&nbsp;|&nbsp;';
                                }
                                if(permission.wsmu_updt){
                                    html+='<a href="employee/' + r_id + '/edit" class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Edit </a>&nbsp;|&nbsp;';
                                }
                                if(permission.wsmu_delt){
                                    html+= '<form style="display:inline" action="employee/' + r_id + '/reset" class="pull-xs-right5 card-link" method="POST"><?php echo e(csrf_field()); ?><?php echo e(method_field("PUT")); ?><input class="btn btn-danger btn-xs" type="submit" value="Pass Reset" onclick="return ConfirmReset()"></input></form>';
                                }
                                html+='| &nbsp;'+data[i].lfcl_name+'</td></tr>';
                                                              
                            count++;
                        }
                        $("#div_report").show();
                        $("#cont").append(html);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;
                        $('#tableDiv').show();
                    },error:function(error){
                        swal.fire({
                            icon:'error',
                            text:'Something Went Wrong!!',
                        });
                    }

                });

            }else if (slgp_id != "") {
                $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(URL::to('/')); ?>/employee/filter/empdetails",
                    data: {
                        acmp_id: acmp_id,
                        slgp_id: slgp_id,
                        zone_id: zone_id,
                        sr_id: sr_id,
                        sv_id: sv_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        var count = 1;
                        let permission=data.permission;
                        var data=data.srData;
                        for (var i = 0; i < data.length; i++) {
                            var r_id = data[i]['id'];
                            html += '<tr class="tbl_body_gray">' +
                                '<td class="cell_left_border">' + count + '</td>' +
                                '<td>' + data[i]['id'] + '</td>' +
                                '<td>' + data[i]['aemp_usnm'] + '</td>' +
                                '<td>' + data[i]['aemp_name'] + '</td>' +
                                '<td>' + data[i]['aemp_mob1'] + '</td>' +
                                '<td>' + data[i]['role_name'] + '</td>' +
                                '<td>' + data[i]['edsg_name'] + '</td>' +
                                '<td>' + data[i]['m_code'] + " - " + data[i]['m_name'] + '</td>' +
                                '<td>' + data[i]['amng_name'] + '</td>';
                                if(permission.wsmu_vsbl){
                                    html+='<td><a href="employee/' + r_id + '" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View</a>&nbsp;|&nbsp;';
                                }
                                if(permission.wsmu_updt){
                                    html+='<a href="employee/' + r_id + '/edit" class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Edit </a>&nbsp;|&nbsp;';
                                }
                                if(permission.wsmu_delt){
                                    html+= '<form style="display:inline" action="employee/' + r_id + '/reset" class="pull-xs-right5 card-link" method="POST"><?php echo e(csrf_field()); ?><?php echo e(method_field("PUT")); ?><input class="btn btn-danger btn-xs" type="submit" value="Pass Reset" onclick="return ConfirmReset()"></input></form></td></tr>';
                                }
                                                              
                            count++;
                        }
                        $("#div_report").show();
                        $("#cont").append(html);
                        $('#ajax_load').css("display", "none");
                        var html = '';
                        var count = 1;
                        $('#tableDiv').show();
                    },error:function(error){
                        swal.fire({
                            icon:'error',
                            text:'Something Went Wrong!!',
                        });
                        
                    }

                });
            } else {
                alert("Please select Group and Try again!!!");
            }

        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/master_data/employee/emp_filter/index.blade.php ENDPATH**/ ?>