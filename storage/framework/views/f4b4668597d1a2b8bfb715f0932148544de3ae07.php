

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
                            <strong>All Route</strong>
                        </li>
                    </ol>
            </div>



                    <form action="<?php echo e(URL::to('/route')); ?>" method="get">
                        <div class="title_right">
                            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                                <div class="input-group">

                                    <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                           value="<?php echo e($search_text); ?>">
                                    <span class="input-group-btn">
                      <button class="btn btn-default" type="submit">Go!</button>
                    </span>

                                </div>
                            </div>
                        </div>
                    </form>

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

                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/route/create')); ?>">Add New</a>
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('route/routeMasterUpload')); ?>">Upload</a>
                            <?php endif; ?>
                            <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('route/routeBaseMapping')); ?>">Base Mapping</a>
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
                            <?php echo e($routes->appends(Request::only('search_text'))->links()); ?>

                            <table id="datatable" class="table table-bordered projects" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Base</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($route->id); ?></td>
                                        <td><?php echo e($route->name); ?></td>
                                        <td><?php echo e($route->code); ?></td>
                                        <td><?php echo e($route->base_name); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('route.show',$route->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(route('route.edit',$route->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(URL::to('route/route_site/'.$route->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Site List
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_delt): ?>
                                                <form style="display:inline"
                                                      action="<?php echo e(route('route.destroy',$route->id)); ?>"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $route->status_id == 1 ? 'Inactive' : 'Active'?>"
                                                           onclick="return ConfirmDelete()">
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
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/route/index.blade.php ENDPATH**/ ?>