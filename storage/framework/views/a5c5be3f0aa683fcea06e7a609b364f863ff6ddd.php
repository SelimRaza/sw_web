

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
                            <a href="<?php echo e(URL::to('/ward')); ?>">All Ward</a>
                        </li>
                        <li class="active">
                            <strong>New Ward</strong>
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

                            <button class="btn btn-success btn-sm" onclick="getAddWard();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Region</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" id="add_ward">
                            <strong><center>::: Add New Ward :::</center></strong>
                            <div class="ln_solid"></div>
                            <form class="form-horizontal form-label-left" action="<?php echo e(route('ward.store')); ?>"
                                  method="post">
                                <?php echo e(csrf_field()); ?>


                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                           style="text-align: left">Thana <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control" name="than_id" id="than_id" required>
                                            <option value="">Select Thana</option>
                                            <?php $__currentLoopData = $govThana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $govThana1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($govThana1->id); ?>"><?php echo e(ucfirst($govThana1->than_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                           style="text-align: left">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="ward_name"
                                               <?php echo e(old('ward_name')); ?>

                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                           style="text-align: left">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="ward_code"
                                               <?php echo e(old('ward_code')); ?>

                                               placeholder="Code" required="required" type="text">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button id="send" type="submit" class="btn btn-primary btn-sm"><span
                                                    class="fa fa-check-circle"
                                                    style="color: white; font-size: 1.3em"></span> <b>Submit</b>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="x_content" id="upload_ward">
                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/data_upload/wardMasterUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>

                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm" href="<?php echo e(URL::to('/data_upload/wardUploadFormat')); ?>"><span
                                                class="fa fa-cloud-download"
                                                style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                            Format</b></a>
                                </div>
                                <div class="col-md-12">
                                    <input id="name" class="form-control col-md-7 col-xs-12"
                                           data-validate-length-range="6" data-validate-words="2" name="import_file"
                                           placeholder="Shop List file" type="file"
                                           step="1">
                                </div>
                                <br/><br/><br/>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button id="send" type="submit" class="btn btn-primary btn-sm"
                                                style="margin-top: 10px;"><span
                                                    class="fa fa-cloud-upload"
                                                    style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                                File</b></button>
                                    </div>
                                </div>
                            </form>
                            <br>
                            <br>
                            <br>
                            <br>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(document).ready(function () {
            $("#than_id").select2();
            const than_id = '<?php echo e(old('than_id')); ?>';

            if (than_id !== '') {
                $('#than_id').val(than_id);
            }
        });

        $('#upload_ward').hide();
        function getAddWard() {
            $('#upload_ward').hide();
            $('#add_ward').show();
        }

        function addUploadFile() {
            $('#upload_ward').show();
            $('#add_ward').hide();
        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/ward/create.blade.php ENDPATH**/ ?>