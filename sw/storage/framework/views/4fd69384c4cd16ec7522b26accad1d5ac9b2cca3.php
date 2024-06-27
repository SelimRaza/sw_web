<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>All Employee</strong>
                        </li>
                    </ol>
                </div>
                <form action="<?php echo e(URL::to('/employee')); ?>" method="get">
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
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/employee/create')); ?>">Add New</a>
                                <a class="btn btn-success btn-sm"
                                   href="<?php echo e(URL::to('employee/employeeUpload')); ?>">Upload</a>

                                <a class="btn btn-success btn-sm"
                                   href="<?php echo e(URL::to('get/employee/routeSearch/view')); ?>">Search Route</a>
                                <a class="btn btn-success btn-sm"
                                   href="<?php echo e(URL::to('employee/get/routeLike/view')); ?>">Route Like</a>
                            <?php endif; ?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="x_content">
                            <!-- <?php echo e($employees->appends(Request::only('search_text'))->links()); ?> -->

                            <table  class="table" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL


                                    </th>
                                    <th>Emp Id</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Designation</th>
                                    <th>manager</th>
                                    <th>Line manager</th>
                                    <th>Sales Group</th>
                                    <th>Company</th>
                                    <th>Icon</th>
                                    <th>App Menu</th>
                                    <th>Own Site</th>
                                    <th>IDate</th>
                                    <th>EDate</th>
                                    <th style="width: 30%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($employee->id); ?></td>
                                        <td><?php echo e($employee->aemp_usnm); ?></td>
                                        <td><?php echo e($employee->aemp_name); ?></td>
                                        <td><?php echo e($employee->aemp_mob1); ?></td>
                                        <td><?php echo e($employee->aemp_emal); ?></td>
                                        <td><?php echo e($employee->role_name); ?></td>
                                        <td><?php echo e($employee->edsg_name); ?></td>
                                        <td><?php echo e($employee->mnrg_name); ?></td>
                                        <td><?php echo e($employee->lmid_name); ?></td>
                                        <td><?php echo e($employee->slgp_name); ?></td>
                                        <td><?php echo e($employee->acmp_name); ?></td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <?php if($employee->aemp_picn!=''): ?>
                                                        <img src="https://sw-bucket.sgp1.cdn.digitaloceanspaces.com/<?php echo e($employee->aemp_picn); ?>"
                                                             class="avatar" alt="Avatar">
                                                    <?php endif; ?>
                                                </li>

                                            </ul>
                                        </td>
                                        <td><?php echo e($employee->amng_name); ?></td>
                                        <td><?php echo e($employee->site_code); ?></td>
                                        <td><?php echo e($employee->created_at); ?></td>
                                        <td><?php echo e($employee->updated_at); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('employee.show',$employee->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(route('employee.edit',$employee->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_delt): ?>
                                                <!-- <form style="display:inline"
                                                      action="<?php echo e(route('employee.destroy',$employee->id)); ?>"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $employee->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form> -->
                                               
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <form style="display:inline"
                                                      action="employee/<?php echo e($employee->id); ?>/reset"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field("PUT")); ?>

                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Pass Reset"
                                                           onclick="return ConfirmReset()">
                                                    </input>
                                                </form>
                                            <?php endif; ?>

                                           <?php echo e($employee->lfcl_id == 1 ? 'Active' : 'Inactive'); ?>


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
        function ConfirmReset() {
            var x = confirm("Are you sure you want to Reset?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/index.blade.php ENDPATH**/ ?>