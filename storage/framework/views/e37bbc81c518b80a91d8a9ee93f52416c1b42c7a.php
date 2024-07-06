

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
                            <a href="<?php echo e(URL::to('/specialBudget')); ?>">All Special Budget</a>
                        </li>
                        <li class="active">
                            <strong>Show Special Budget</strong>
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
                            <h1>Special Budget Details</h1>

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
                            <div class="col-md-1 col-sm-1 col-xs-12">

                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>S/L</th>
                                    <th> Amount</th>
                                    <th>Type</th>
                                    <th>TRN</th>
                                    <th>Time</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $specialBudgetLine; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $specialBudgetLine1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($specialBudgetLine1->spbd_amnt); ?></td>
                                        <td><?php echo e($specialBudgetLine1->spbd_type); ?></td>
                                        <td><?php echo e($specialBudgetLine1->ordm_ornm); ?></td>
                                        <td><?php echo e($specialBudgetLine1->created_at); ?></td>

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
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/blockOrder/budget/show.blade.php ENDPATH**/ ?>