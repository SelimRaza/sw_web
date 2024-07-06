

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
                            <a href="<?php echo e(URL::to('/thana')); ?>">All Thana</a>
                        </li>
                        <li class="active">
                            <strong>New Thana</strong>
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

                            <button class="btn btn-success btn-sm" onclick="getAddThana();"><span
                                        class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                    New Region</b></button>
                            <button class="btn btn-success btn-sm" onclick="addUploadFile();"><span
                                        class="fa fa-cloud-upload" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Upload
                                    File</b></button>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" id="add_thana">

                            <form class="form-horizontal form-label-left" action="<?php echo e(route('thana.store')); ?>"
                                  method="post">
                                <?php echo e(csrf_field()); ?>


                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">District <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control" name="dsct_id" id="dsct_id" required>
                                            <option value="">Select District</option>
                                            <?php $__currentLoopData = $govDistrict; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $govDistrict1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($govDistrict1->id); ?>"><?php echo e(ucfirst($govDistrict1->dsct_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="than_name"
                                               <?php echo e(old('than_name')); ?>

                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="than_code"
                                               <?php echo e(old('than_code')); ?>

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


                        <div class="x_content" id="upload_thana">
                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(URL::to('/data_upload/thanaMasterUpload')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <?php echo e(csrf_field()); ?>

                                <strong>
                                    <center>::: Upload File :::</center>
                                </strong>
                                <div class="ln_solid"></div>
                                <div class="col-md-12">
                                    <a class="btn btn-danger btn-sm" href="<?php echo e(URL::to('data_upload/thanaUploadFormat')); ?>"><span
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
            $("#dsct_id").select2();
            const dsct_id = '<?php echo e(old('dsct_id')); ?>';

            if (dsct_id !== '') {
                $('#dsct_id').val(dsct_id);
            }
        });

        $('#upload_thana').hide();
        function getAddThana() {
            $('#upload_thana').hide();
            $('#add_thana').show();
        }

        function addUploadFile() {
            $('#upload_thana').show();
            $('#add_thana').hide();
        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/thana/create.blade.php ENDPATH**/ ?>