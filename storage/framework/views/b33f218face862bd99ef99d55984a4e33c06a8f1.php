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
                            <strong>All Route Plan</strong>
                        </li>
                    </ol>
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
                            <?php if($permission->wsmu_crat): ?>
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('rpln-mapping/create')); ?>">Upload</a>
                            <?php endif; ?>
                            <?php if($permission->wsmu_updt): ?>
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('rpln-mapping/replace')); ?>">Rout Transfer</a>
                            <?php endif; ?>
                            <?php if($permission->wsmu_updt): ?>
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('rpln-mapping/exchange')); ?>">Rout Exchange</a>
                            <?php endif; ?>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
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
                            <table id="datatable" class="table table-bordered projects" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Group</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $pjps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$pjp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>

                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($pjp->aemp_usnm); ?></td>
                                        <td><?php echo e($pjp->name); ?></td>
                                        <td><?php echo e($pjp->group_name); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_read ): ?>
                                                <a href="<?php echo e(route('pjp.show',$pjp->emp_id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> Details
                                                </a>
                                            <?php endif; ?>


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
    <script type="text/javascript">
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Mapping/RoutePlan/rout_plan.blade.php ENDPATH**/ ?>