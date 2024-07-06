

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
                            Upload Master Data
                            
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
                        <strong>Success!<?php echo e(Session::get('success')); ?> </strong>
                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Error! <?php echo e(Session::get('danger')); ?> </strong>
                    </div>
                <?php endif; ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> :::Employee Upload:::
                                </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/employee/employeeUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(URL::to('/employee/employeeUploadFormat')); ?>">Generate
                                                Employee
                                                Upload
                                                Format </a></strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>


                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> :::Employee Group, Price List, Zone Mapping::: </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/sales-group/groupEmpMappingUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(URL::to('/sales-group/groupEmpMappingUploadFormatGen')); ?>">Generate
                                                Employee Mapping
                                                Upload
                                                Format </a></strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>


                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> :::Upload Route Master::: </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/route/routeMasterUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(asset("sample/route_upload_format.xlsx")); ?>"> Download Route
                                                Upload Format </a>
                                        </strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> :::Upload Route Plan::: </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/pjp/empRouteUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(asset("sample/route_plan_upload_format.xlsx")); ?>"> Download
                                                Route Plan
                                                Format </a></strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>


                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> :::Upload Distributor Master::: </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/depot/depotUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(URL::to('/depot/depotFormat')); ?>">Generate
                                                Distributor
                                                Upload
                                                Format </a></strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>


                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: SR Distributor Mapping ::: </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/depot/depotEmployeeMappingUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(URL::to('/depot/depotEmployeeMappingUploadFormat')); ?>">Download
                                                SR Distribution Mapping Format </a></strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>


                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-warning">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: Employee, Dealer, Zone, Group, Price List Mapping ::: </strong>
                            </center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/employee/employeeGroupZoneUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>



                                <div class="item form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Download Format : </label>
                                        <strong><a class="col-md-10 col-sm-10 col-xs-12"
                                                   href="<?php echo e(URL::to('/employee/employeeGroupZoneUploadFormat')); ?>">Download
                                                Employee Upload Format </a></strong>

                                    </div>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-md-2 col-sm-2 col-xs-12">Upload File<span
                                                    class="required">* : </span>
                                        </label>
                                        <div class="col-md-10 col-sm-10 col-xs-12">
                                            <input id="name" class="form-control col-md-12 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="import_file"
                                                   placeholder="Shop List file" type="file"
                                                   step="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/master_data_upload.blade.php ENDPATH**/ ?>