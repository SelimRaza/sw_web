<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">

                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>Success!</strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Danger! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h5 style="margin-top: 0px;font-size: 18px;text-align: left;">Show Employee Password</h5>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="<?php echo e(route('employee.store')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <?php echo e(csrf_field()); ?>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Staff ID<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="staff_id" class="form-control numberonly" placeholder="Please Enter Staff Id" type="text" >
                                    </div>
                                    <button type="button" class="btn btn-success" onclick="showPassword()"> <span class="fa fa-search"></span> Search </button>
                                </div>
                            </form>
                            <br>
                            <table id="show_content" class="table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>User Name</th>
                                    <th>Staff ID</th>
                                    <th>Old Password</th>
                                    <th>New Password</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $("#show_content").hide();
        function showPassword(){

            var staff_id=$('#staff_id').val();
            $('#ajax_load').css("display", "block");
            $.ajax({

                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/load/password_details",
                data: {

                    staff_id: staff_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    if(data.length>0){

                        var rows = '';
                        var i=1;
                        $('#ajax_load').css("display", "none");
                        $.each(data, function (key, value) {
                            rows = rows + '<tr class="tbl_body_gray">';
                            rows = rows + '<td class="cell_left_border">' + i++ + '</td>';
                            rows = rows + '<td>' + value.name + '</td>';
                            rows = rows + '<td>' + value.staff_id + '</td>';
                            rows = rows + '<td>' + value.pswd_opwd + '</td>';
                            rows = rows + '<td>' + value.pswd_npwd + '</td>';
                        });
                        $("tbody").html(rows);
                        $("#show_content").show();
                        $('#staff_id').val("");

                    }else{

                        var rows = rows+'<tr>'+
                            '<td style="text-align: center;color: #00AA88">'+"No Data Available..!!"+'</td>'+
                            '</tr>';
                        $("tbody").html(rows);
                        $("#show_content").show();
                        $('#ajax_load').css("display", "none");

                    }

                }
            });

        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/employee_psw_show.blade.php ENDPATH**/ ?>