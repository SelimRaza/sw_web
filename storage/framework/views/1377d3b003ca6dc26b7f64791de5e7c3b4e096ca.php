

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li>
                            <strong>All Ward</strong>
                        </li>
                    </ol>
                </div>
                <form action="<?php echo e(URL::to('/ward')); ?>" method="get">
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
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/ward/create')); ?>"><span
                                            class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                        New</b></a>

                                        <a class="btn btn-danger btn-sm" style="float: right" href="<?php echo e(URL::to('getAllWard')); ?>"><span
                                            class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                        File</b></a>
                            <?php endif; ?>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <?php echo e($wards->appends(Request::only('search_text'))->links()); ?>

                            <table id="datatables" class="table search-table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border ">SL</th>
                                    <th>Ward ID</th>
                                    <th>Ward Name</th>
                                    <th>Ward Code</th>
                                    <th>Thana</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $wards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border"><?php echo e($index+1); ?></td>
                                        <td><?php echo e($ward->id); ?></td>
                                        <td><?php echo e($ward->ward_name); ?></td>
                                        <td><?php echo e($ward->ward_code); ?></td>
                                        <td><?php echo e($ward->than_name); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_delt): ?>
                                                <form style="display:inline"
                                                      action="<?php echo e(route('ward.destroy',$ward->id)); ?>"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input class="btn btn-round btn-xs"
                                                           style="color:white; background-color: <?php echo $ward->lfcl_id == 1 ? '#06993a' : '#9f0e35'?>"
                                                           type="submit"
                                                           value="<?php echo $ward->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge"
                                                      style="background-color: <?php echo $ward->lfcl_id == 1 ? '#06993a' : '#9f0e35'?>"><?php echo $ward->lfcl_id == 1 ? 'Active' : 'Inactive'?></span>
                                            <?php endif; ?>


                                        </td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('ward.show',$ward->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(route('ward.edit',$ward->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
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
        $(document).ready(function(){
            $('table.search-table').tableSearch({
                searchPlaceHolder:'Search Text'
            });
        });
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/ward/index.blade.php ENDPATH**/ ?>