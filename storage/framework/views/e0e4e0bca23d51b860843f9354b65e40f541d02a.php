

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
                            <a href="<?php echo e(URL::to('/mrr')); ?>">All MRR </a>
                        </li>
                        <?php if($permission->wsmu_crat): ?>
                            <li class="label-success">
                                <a href="<?php echo e(URL::to('/mrr/mrrUploadFormat')); ?>">MRR Upload
                                    Format</a>
                            </li>
                        <?php endif; ?>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>Success! </strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Alert! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12">

                    <?php if($permission->wsmu_crat): ?>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">

                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                   role="button"
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
                                        <br/>
                                        <form id="demo-form2" data-parsley-validate
                                              class="form-horizontal form-label-left"
                                              action="<?php echo e(URL::to('/mrr/mrrUpload')); ?>"
                                              enctype="multipart/form-data"
                                              method="post">
                                            <?php echo e(csrf_field()); ?>

                                            <?php echo e(method_field('POST')); ?>

                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">File
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="name" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6"
                                                           data-validate-words="2"
                                                           name="import_file"
                                                           placeholder="Shop List file" type="file"
                                                           step="1">
                                                </div>
                                            </div>
                                            <div class="ln_solid"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <button type="submit" class="btn btn-success">Upload
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">

                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                               role="button"
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
                                    <br/>
                                    <table class="table table-striped projects">
                                        <thead>
                                        <tr>
                                            <th>S/L</th>
                                            <th>MRR Id</th>
                                            <th>MRR Code</th>
                                            <th>MRR created</th>
                                            <th>MRR Verified</th>
                                            <th>Depot name</th>
                                            <th style="width: 20%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $mrrLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$mrr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index+1); ?></td>
                                                <td><?php echo e($mrr->id); ?></td>
                                                <td><?php echo e($mrr->mrrm_code); ?></td>
                                                <td><?php echo e($mrr->employeeCreated()->aemp_name); ?></td>
                                                <td><?php echo e($mrr->employee()->aemp_name); ?></td>
                                                <td><?php echo e($mrr->depot()->dlrm_name); ?></td>
                                                <td><?php echo e($mrr->status()->lfcl_name); ?></td>
                                                <td>
                                                    <?php if($permission->wsmu_read): ?>
                                                        <a href="<?php echo e(route('mrr.show',$mrr->id)); ?>"
                                                           class="btn btn-primary btn-xs"><i class="fa fa-folder"></i>
                                                            View
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($permission->wsmu_updt): ?>
                                                        <?php if($mrr->lfcl_id==1): ?>
                                                            <a class="btn btn-danger btn-xs"
                                                               href="<?php echo e(route('mrr.edit',$mrr->id)); ?>"
                                                               class="btn btn-info btn-xs"><i></i> Verify
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
        </div>
    </div>
    <script type="text/javascript">
        $("#emp_id").select2({width: 'resolve'});

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/Depot/MRR/index.blade.php ENDPATH**/ ?>