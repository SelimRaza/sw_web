

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
                            <strong>All SKU</strong>
                        </li>

                    </ol>
                </div>

                <form action="<?php echo e(URL::to('/sku')); ?>" method="get">
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

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="x_panel">

                    </div>
                </div>
            </div>

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
                                <a href="<?php echo e(URL::to('/sku/create')); ?>" class="btn btn-success btn-sm">Add New</a>
                                <a href="<?php echo e(URL::to('bulk_sku')); ?>" class="btn btn-success btn-sm">Upload</a>
                            <?php endif; ?>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <?php echo e($skus->appends(Request::only('search_text'))->links()); ?>

                            <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>code</th>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Unit</th>
                                    <th>Subcategory</th>
                                    <th>Class</th>
                                    <th>Image</th>
                                    <th>Image Icon</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $skus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$sku): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index+1); ?></td>
                                        <td><?php echo e($sku->amim_code); ?></td>
                                        <td><?php echo e($sku->amim_name); ?></td>
                                        <td><?php echo e($sku->amin_snme); ?></td>
                                        <td><?php echo e($sku->amim_duft); ?></td>
                                        <td><?php echo e($sku->subCategory()->itsg_name); ?></td>
                                        <td><?php echo e($sku->itemClasss()->itcl_name); ?></td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <?php if($sku->amim_imgl): ?>
                                                        <img src="https://sw-bucket.sgp1.cdn.digitaloceanspaces.com/<?php echo e($sku->amim_imgl); ?>"
                                                             class="avatar" alt="Avatar">
                                                    <?php endif; ?>
                                                </li>

                                            </ul>
                                        </td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <?php if($sku->amim_imic): ?>
                                                        <img src="https://sw-bucket.sgp1.cdn.digitaloceanspaces.com/<?php echo e($sku->amim_imic); ?>"
                                                             class="avatar" alt="Avatar">
                                                    <?php endif; ?>
                                                </li>

                                            </ul>
                                        </td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('sku.show',$sku->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(route('sku.edit',$sku->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_delt): ?>
                                                <form style="display:inline"
                                                      action="<?php echo e(route('sku.destroy',$sku->id)); ?>"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input class="btn btn-<?php echo e($sku->lfcl_id == 1 ? 'success' : 'danger'); ?> btn-xs" type="submit"
                                                           value="<?php echo e($sku->lfcl_id == 1 ? 'Active' : 'Inactive'); ?>"
                                                           onclick="return ConfirmDelete('<?php echo e($sku->lfcl_id); ?>')">
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
        function ConfirmDelete(status) {
            let targetStatus = (status === '1') ? 'Inactive' : 'Active';
            var x = confirm(`Are you sure you want to ${targetStatus}?`);
            if (x)
                return true;
            else
                return false;
        };
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/sku/index.blade.php ENDPATH**/ ?>