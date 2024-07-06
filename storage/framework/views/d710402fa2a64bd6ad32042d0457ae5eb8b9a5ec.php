

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <a href="<?php echo e(URL::to('/appMenuGroup')); ?>">All App Menu Profile</a>
                        </li>
                        <li class="active">
                            <strong>Show App Menu Profile</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
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

                        <div class="x_title">
                            <h4><strong>App Menu Profile</strong></h4>
                            <div class="clearfix"></div>

                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Menu</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $appMenuGroupLine; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $appMenuGroupLine1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border"><?php echo e($index+1); ?></td>
                                        <td><?php echo e($appMenuGroupLine1->amnu_name); ?></td>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Setting/AppMenuGroup/show.blade.php ENDPATH**/ ?>