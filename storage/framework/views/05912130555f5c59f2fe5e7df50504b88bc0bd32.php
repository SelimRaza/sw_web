<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="label-success">
                            <a>Group Permission</a>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>
            <p id="employee_id" style="display: none"><?php echo e($emp_id); ?></p>
            <div class="row">
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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: All Groups:::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table id="" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>All<br><input type="checkbox" id="group_all"></th>
                                    <th>SL</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1?>
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php if($result->status==1): ?>
                                                <input type="checkbox" class="sub_chk" name="group" value="<?php echo e($result->id); ?>" checked>
                                            <?php else: ?>
                                                <input type="checkbox" class="sub_chk" name="group" value="<?php echo e($result->id); ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($i++); ?></td>
                                        <td><?php echo e($result->slgp_name); ?>----><?php echo e($result->slgp_code); ?></td>
                                        <td><span class="badge badge-secondary"><?php if($result->status==1): ?><?php echo e("Assign"); ?><?php endif; ?></span></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="">
            <div class="row">
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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: All Zones :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table id="" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>All<br><input type="checkbox" id="zone_all"></th>
                                    <th>SL</th>
                                    <th>Zone</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1?>
                                <?php $__currentLoopData = $result2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php if($result->status==1): ?>
                                               <input type="checkbox" class="sub_chk1" name="zone" value="<?php echo e($result->id); ?>" checked>
                                            <?php else: ?>
                                                <input type="checkbox" class="sub_chk1" name="zone" value="<?php echo e($result->id); ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($i++); ?></td>
                                        <td><?php echo e($result->zone_name); ?>----><?php echo e($result->zone_code); ?></td>
                                        <td><span class="badge badge-secondary"><?php if($result->status==1): ?><?php echo e("Assign"); ?><?php endif; ?></span></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>

                        </div>
                        <button style="margin-bottom: 10px" class="btn btn-danger zone_submit_all">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });
        function ConfirmDelete()
        {
            var x = confirm("Are you sure you want to delete?");
            if (x)
                return true;
            else
                return false;
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function () {

                $('#group_all').on('click', function(e) {

                    if($(this).is(':checked',true)){

                        $(".sub_chk").prop('checked', true);

                    } else {

                        $(".sub_chk").prop('checked',false);

                    }

                });

                $('#zone_all').on('click', function(e) {

                    if($(this).is(':checked',true)){

                        $(".sub_chk1").prop('checked', true);

                    } else {

                        $(".sub_chk1").prop('checked',false);

                    }

                });

                $(".zone_submit_all").click(function(){

                    var zones = [];
                    var groups = [];
                    var uncheck_zones =[];
                    var uncheck_groupa =[];
                    var employee_id=$("#employee_id").html();
                    $.each($("input[name='zone']:checked"), function(){

                        zones.push($(this).val());
                    });
                    $.each($("input[name='group']:checked"), function(){

                        groups.push($(this).val());
                    });

                    $.each($("input[name='zone']:not(:checked)"), function(){

                        uncheck_zones.push($(this).val());
                    });

                    $.each($("input[name='group']:not(:checked)"), function(){

                        uncheck_groupa.push($(this).val());
                    });

                    var check_all_group = groups.join(",");
                    var check_all_zone = zones.join(",");
                    var uncheck_all_group = uncheck_groupa.join(",");
                    var uncheck_all_zones = uncheck_zones.join(",");

                    $.ajax({

                        type: "GET",
                        url: "<?php echo e(URL::to('/')); ?>/json/assign_emp/group_zoon_permission",
                        data: {

                            check_all_group: check_all_group,
                            check_all_zone: check_all_zone,
                            uncheck_all_group:uncheck_all_group,
                            uncheck_all_zones:uncheck_all_zones,
                            employee_id:employee_id

                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {

                            console.log(data);

                           alert("Successfully Done...!!");


                        },
                        error: function(data) {

                            console.log(data);

                        }
                    });

                });

        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/demo/group_zoon_permission.blade.php ENDPATH**/ ?>