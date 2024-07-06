

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
                            <a href="<?php echo e(URL::to('/pjp')); ?>">All Route Plan</a>
                        </li>
                        <li class="active">
                            <strong>Show Route Plan</strong>
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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Route Plan </h1>
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


                            <?php echo e(csrf_field()); ?>


                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <form style="display:inline"
                                          action="<?php echo e(URL::to('pjp/route_add/'.$emp->id)); ?>"
                                          class="pull-xs-right5 card-link" method="GET">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                           value="Employee" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                           value="<?php echo e($sales_group->salesGroup()->slgp_name); ?>" readonly>
                                                    <input type="hidden" class="form-control" id="sales_gorup_id" name="sales_gorup_id"
                                                           value="<?php echo e($sales_group->sales_group_id); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <input type="text" class="form-control"
                                                           value="<?php echo e($emp->aemp_name.'('.$emp->aemp_usnm.')'); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-striped projects">
                                            <thead>

                                            </thead>
                                            <tbody>
                                            <td>

                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name"
                                                        required>

                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" name="route_id" id="route_id"
                                                        required>
                                                    <option value="">Select</option>
                                                    <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($route->id); ?>"><?php echo e($route->name.'('.$route->code.')'); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="btn btn-success" type="submit" value="Add">
                                            </td>

                                            </tbody>
                                        </table>


                                    </form>
                                    <table id="data_table" class="table table-striped table-bordered"
                                           data-page-length='25'>
                                        <thead>
                                        <tr style="background-color: #2b4570; color: white;">
                                            <th>Day</th>
                                            <th>Route</th>
                                            <th>Action</th>
                                            <th>Site list</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        <?php $__currentLoopData = $pjps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pjp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="saturday"
                                                           name="day[]"
                                                           value="<?php echo e($pjp->rpln_day); ?>" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="saturday"
                                                           name="saturday"
                                                           value="<?php echo e($pjp->route()->rout_name); ?>" readonly>

                                                </td>

                                                <td>
                                                    <form style="display:inline"
                                                          action="<?php echo e(route('pjp.destroy',$pjp->id)); ?>"
                                                          class="pull-xs-right5 card-link" method="POST">
                                                        <?php echo e(csrf_field()); ?>

                                                        <?php echo e(method_field('DELETE')); ?>

                                                        <input class="btn btn-danger btn-xs" type="submit"
                                                               value="Delete"
                                                               onclick="return ConfirmDelete()">
                                                        </input>
                                                    </form>

                                                </td>
                                                <td>
                                                <?php if($permission->wsmu_read): ?>
                                                    <a href="<?php echo e(URL::to('pjp/route_site/'.$pjp->rout_id.'/'.$pjp->aemp_id)); ?>"
                                                       class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Site List
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
        </div>
    </div>
    <script>

        $("#route_id").select2({width: 'resolve'});
        $("#day_name").select2({width: 'resolve'});
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/pjp/show.blade.php ENDPATH**/ ?>