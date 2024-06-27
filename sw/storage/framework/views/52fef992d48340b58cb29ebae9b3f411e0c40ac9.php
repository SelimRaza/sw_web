

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
                            <a href="<?php echo e(URL::to('/target')); ?>">All Target</a>
                        </li>
                        <li class="active">
                            <strong>Target By SR</strong>
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
                        <strong>Success! </strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Alert! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">

                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                               role="button"
                                               aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Settings 1</a>
                                                </li>
                                                <li><a href="#">Settings 2</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <br/>
                                    <table class="table table-striped projects">
                                        <thead>
                                        <tr>
                                            <th>S/L</th>
                                            <th>User Name</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>Month Name</th>
                                            <th>Item Name</th>
                                            <th>Target CTN</th>
                                            <th>Target Value</th>
                                            <th style="width: 20%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $bySrdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$bySrdata1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index+1); ?></td>
                                                <td><?php echo e($bySrdata1->supervisor_name); ?></td>
                                                <td><?php echo e($bySrdata1->year); ?></td>
                                                <td><?php echo e($bySrdata1->month); ?></td>
                                                <td><?php echo e($bySrdata1->month_name); ?></td>
                                                <td><?php echo e($bySrdata1->item_name); ?></td>
                                                <td><?php echo e($bySrdata1->initial_target_in_ctn); ?></td>
                                                <td><?php echo e($bySrdata1->initial_target_in_value); ?></td>
                                                <td>
                                                    <a href="<?php echo e(URL('target/removeByItem',[$bySrdata1->amim_id,$bySrdata1->year,$bySrdata1->month,$bySrdata1->manager_id])); ?>" class="btn btn-danger btn-sm"><i class="fa fa-pencil"></i>Remove</a>
                                                </td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#emp_id").select2({width: 'resolve'});

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Target/target_by_item.blade.php ENDPATH**/ ?>