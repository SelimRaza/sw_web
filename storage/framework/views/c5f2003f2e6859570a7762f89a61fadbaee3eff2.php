

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
                            <a href="<?php echo e(URL::to('/pjp/'.$emp->id)); ?>"><?php echo e($emp->aemp_name.'('.$emp->aemp_usnm.')'); ?></a>
                        </li>
                        <li class="active">
                            <strong>Show Route Outlet</strong>
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
                        <strong>Success!</strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Danger! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Route Outlet</h1>
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
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Route Name <span
                                                class="required">*</span>
                                    </label>
                                    <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="name"
                                           value="<?php echo e($route->rout_name); ?>"
                                           placeholder="Name" required="required" type="text">
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Route Code <span
                                                class="required">*</span>
                                    </label>
                                    <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="code"
                                           value="<?php echo e($route->rout_code); ?>"
                                           placeholder="Code" required="required" type="text">
                                </div>

                            </div>
                            <?php if($permission->wsmu_updt): ?>
                            <form style="display:inline"
                                  action="<?php echo e(URL::to('pjp/site_add/'.$route->id)); ?>"
                                  class="pull-xs-right5 card-link" method="GET">
                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('DELETE')); ?>


                                <table class="table table-striped projects">
                                    <thead>

                                    </thead>
                                    <tbody>
                                    <td>
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Add Outlet
                                            <span class="required"></span>
                                        </label>

                                    </td>
                                    <td>

                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="site_id"
                                               value=""
                                               placeholder="Site code" required="required" type="text">
                                    </td>
                                    <td>
                                        <input class="btn btn-success" type="submit" value="Add">
                                    </td>

                                    </tbody>
                                </table>


                            </form>
                            <?php endif; ?>

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Outlet Id</th>
                                    <th>Outlet Name</th>
                                    <th>Outlet Code</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $routeSites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$routeSite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($routeSite->site_id); ?></td>
                                        <td><?php echo e($routeSite->site()->site_name); ?></td>
                                        <td><?php echo e($routeSite->site()->site_code); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_updt): ?>
                                            <form style="display:inline"
                                                  action="<?php echo e(URL::to('pjp/route_site_delete/'.$routeSite->id)); ?>"
                                                  class="pull-xs-right5 card-link" method="GET">
                                                <?php echo e(csrf_field()); ?>

                                                <?php echo e(method_field('DELETE')); ?>

                                                <input class="btn btn-danger btn-xs" type="submit"
                                                       value="Delete"
                                                       >
                                                </input>
                                            </form>
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

        $("#site_id").select2({width: 'resolve'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Delete?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/pjp/show_site.blade.php ENDPATH**/ ?>