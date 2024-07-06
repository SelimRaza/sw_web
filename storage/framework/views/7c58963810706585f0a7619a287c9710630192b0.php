

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
                            <strong>All Group</strong>
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
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/sales-group/create')); ?>">Add New</a>
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

                            <table id="datatable" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Id</th>
                                    <th>Group Name</th>
                                    <th>Group Code</th>
                                    <th>Company</th>
                                    <th colspan="3">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $salesGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$salesGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($salesGroup->id); ?></td>
                                        <td><?php echo e($salesGroup->slgp_name); ?></td>
                                        <td><?php echo e($salesGroup->slgp_code); ?></td>
                                        <td><?php echo e($salesGroup->company()->acmp_name); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('sales-group.show',$salesGroup->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(route('sales-group.edit',$salesGroup->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(URL::to('sales-group/category/'.$salesGroup->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Category
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(URL::to('sales-group/sku/'.$salesGroup->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>SKU
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(URL::to('sales-group/employee/'.$salesGroup->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Employee
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a target="_blank" href="<?php echo e(URL::to('price_list/sku/'.$salesGroup->plmt_id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i>Price List
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_delt): ?>
                                                <form style="display:inline"
                                                      action="<?php echo e(route('sales-group.destroy',$salesGroup->id)); ?>"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $salesGroup->status_id == 1 ? 'Active' : 'Inactive'?>"
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
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/sales_group/index.blade.php ENDPATH**/ ?>