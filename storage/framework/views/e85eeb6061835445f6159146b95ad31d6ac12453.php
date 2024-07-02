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
                            <a class="label-success" href="<?php echo e(URL::to('/employee')); ?>">All Employee</a>
                        </li>
                        <li class="active">
                            <strong>Route Like</strong>
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
                            <center><strong> ::: Route Like :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="/json/submit/routeLike"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <?php echo e(csrf_field()); ?>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="company_id" id="company_id" required
                                                onchange="jsonLoadGroupName()">
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($company->id); ?>"><?php echo e($company->acmp_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="group_id" id="group_id" required>

                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Region
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="region_id" id="region_id" required
                                                onchange="jsonLoadZoneName()">
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($region->id); ?>"><?php echo e($region->dirg_code); ?>

                                                    - <?php echo e($region->dirg_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="zone_id" id="zone_id" required
                                                onchange="jsonGetUserDepenOnGroupZone()">

                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-6" for="name">From User
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="from_user_id" id="from_user_id" required>
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-6" for="name">To User
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="to_user_id" id="to_user_id" required>
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-6" for="name">Select Type
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="a_type" id="a_type" onchange="getDivShow(this.value)" required>
                                            <option value="">Select Type</option>
                                            <option value="replace">Replace</option>
                                            <option value="likeAs">Like As</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group" id="optionSelector">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-6" for="name">
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="checkbox" id="dealer" name="dealer" value="Dealer">
                                        <label for="dealer"> Dealer</label><br>
                                        <input type="checkbox" id="thana" name="thana" value="thana">
                                        <label for="thana"> Thana</label><br>
                                        <input type="checkbox" id="priceList" name="priceList" value="priceList">
                                        <label for="priceList"> Price List</label><br><br>
                                    </div>


                                </div>


                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-5">
                                        <button id="send" type="submit" class="btn btn-success btn-bloc"> Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

            $('#optionSelector').hide();

        });

        function getDivShow(id) {
            if(id =="likeAs"){
                $('#optionSelector').show();
            }else{
                $('#optionSelector').hide();
            }
        }

        function createRouteLike() {

            var from_user_id = $('#from_user_id').val();
            var to_user_id = $('#to_user_id').val();
            var a_type = $('#a_type').val();

            /*alert(from_user_id);
            alert(to_user_id);
            alert(a_type);*/

        }

        function jsonLoadGroupName() {

            var company_id = $('#company_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/load/company_wise/group",
                data: {

                    company_id: company_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#group_id');
                    if (!data) {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');

                    } else {

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value['id']).text(value.slgp_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });

        }

        function jsonLoadZoneName() {

            var region_id = $('#region_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/load/region_wise/zone",
                data: {

                    region_id: region_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#zone_id');
                    if (!data) {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');

                    } else {

                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value['id']).text(value.zone_code + " - " + value.zone_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });

        }

        function jsonGetUserDepenOnGroupZone() {

            var group_id = $('#group_id').val();
            var zoon_id = $('#zone_id').val();

            /*alert(group_id);
            alert(zoon_id);*/
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/json/load/group_zoon_wise/user",
                data: {

                    group_id: group_id,
                    zoon_id: zoon_id

                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    var $el = $('#to_user_id');
                    if (!data) {

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');

                    } else {

                        loadSelectOption1(data);
                        loadSelectOption2(data);

                    }

                    function loadSelectOption1(data) {

                        var $el = $('#from_user_id');
                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value['id']).text(value.code + " - " + value.Name));
                        });


                    }

                    function loadSelectOption2(data) {


                        var $el = $('#to_user_id');
                        $el.html(' ');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function (key, value) {

                            $el.append($("<option></option>").attr("value", value['id']).text(value.code + " - " + value.Name));
                        });
                        $el.selectpicker('refresh');


                    }


                }

            });

        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/route_like.blade.php ENDPATH**/ ?>