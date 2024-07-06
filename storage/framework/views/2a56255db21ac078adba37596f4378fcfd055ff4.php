

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
                            <strong>All App Menu Profile</strong>
                        </li>
                    </ol>
                </div>
                <form action="<?php echo e(URL::to('/specialBudget')); ?>" method="get">
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
                            <h4><strong>App Menu Profile</strong></h4>
                            <div class="clearfix"></div>

                            <?php if($permission->wsmu_crat): ?>
                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/appMenuGroup/create')); ?>"
                                   style="float: right; margin-top: -30px;"><span
                                            class="fa fa-plus-circle" style="color: white; font-size: 1.3em;"></span>&nbsp;&nbsp;<b>Add
                                        New</b></a>
                            <?php endif; ?>
                        </div>


                        <div class="x_content">
                            <?php echo e($tm_amng->appends(Request::only('search_text'))->links()); ?>

                            <table class="table table-striped projects">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $tm_amng; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tm_amng1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border"><?php echo e($index+1); ?></td>
                                        <td><?php echo e($tm_amng1->amng_name); ?></td>
                                        <td><?php echo e($tm_amng1->amng_code); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('appMenuGroup.show',$tm_amng1->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View
                                                </a>&nbsp;|&nbsp;
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <?php if($tm_amng1->lfcl_id==1): ?>
                                                    <a href="<?php echo e(route('appMenuGroup.edit',$tm_amng1->id)); ?>"
                                                       class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Edit
                                                    </a>
                                                <?php endif; ?>
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
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Setting/AppMenuGroup/index.blade.php ENDPATH**/ ?>