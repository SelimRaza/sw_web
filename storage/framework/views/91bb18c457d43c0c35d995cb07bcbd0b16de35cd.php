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
                            <strong><a href="<?php echo e(url('/market_open')); ?>">All Market</a></strong>
                        </li>
                        <li class="active">
                            <strong>Market Report</strong>
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
                            <center><strong> ::: Campaign (SMS) :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="x_content">
                        <form class="form-horizontal form-label-left" action="<?php echo e(url('/get/market/report')); ?>"
                              method="get" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="sales_group_id" id="sales_group_id"
                                            onchange="jsonGetEmployeeList()">
                                        <option value="">Select Company</option>
                                        <?php $__currentLoopData = $acmp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmpList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($acmpList->id); ?>"><?php echo e($acmpList->acmp_code); ?>

                                                - <?php echo e($acmpList->acmp_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Schedule<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="sales_group_id" id="sales_group_id"
                                            onchange="jsonGetEmployeeList()">
                                        <option value="">Select Schedule</option>
                                        <option value="">One Time</option>
                                        <option value="">Schedule</option>
                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Body<span
                                            class="required">*</span>
                                </label>

                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea rows="4" cols="50" class="form-control col-md-12 col-xs-12" name="textMessage" form="usrform">
                                            </textarea>
                                </div>
                            </div>

                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Region<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="district_id" id="district_id"
                                            onchange="getThanaBelogToDistrict()">
                                        <option value="">Select Region</option>
                                        <?php $__currentLoopData = $region; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regionList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($regionList->id); ?>"><?php echo e($regionList->dirg_code); ?> - <?php echo e($regionList->dirg_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="thana_id" id="thana_id"
                                            onchange="getWardNameBelogToThana()">

                                        <option value="">Select Zone</option>
                                        <?php $__currentLoopData = $zoneList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneLists): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($zoneLists->id); ?>"><?php echo e($zoneLists->zone_code); ?> - <?php echo e($zoneLists->zone_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Non Productive
                                    (7)<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="ward_id" id="ward_id"
                                            onchange="loadWardMarket()">


                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Outlet Type<span
                                            class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="market_id" id="market_id">
                                        <option value="">Select Category</option>
                                        <?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categorys): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($categorys->id); ?>"><?php echo e($categorys->otcg_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </select>
                                </div>


                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">

                                <center>
                                    <button id="send" type="submit" class="btn btn-success">Send SMS</button>
                                </center>
                            </div>

                        </form>
                    </div>



                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });

        function loadWardMarket() {

            var ward_id = $('#ward_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/get/ward_wise/market_list",
                data: {
                    ward_id: ward_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#market_id');
                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.market_name));
                        });
                        $el.selectpicker('refresh');
                    }


                }
            });

        }

        function getWardNameBelogToThana() {

            var thana_id = $('#thana_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/get/market_open/word_list",
                data: {
                    thana_id: thana_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    var $el = $('#ward_id');
                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.ward_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });
        }

        function getThanaBelogToDistrict() {

            var district_id = $('#district_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/get/market_open/thana_list",
                data: {
                    district_id: district_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    var $el = $('#thana_id');
                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.than_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });
        }

        function ConfirmDelete() {

            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };

        function jsonGetEmployeeList() {

            var sales_group_id = $('#sales_group_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/jsonGetEmployeeList",
                data: {
                    sales_group_id: sales_group_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#employee_id');

                    if (!data) {
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    } else {

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {
                            $el.append($("<option></option>").attr("value", value.id).text(value.Name + '-' + value.code));
                        });
                        $el.selectpicker('refresh');
                    }


                }
            });

        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/Campaign/campaign.blade.php ENDPATH**/ ?>