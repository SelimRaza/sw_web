

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="label-success">
                            <a href="<?php echo e(URL::to('/site')); ?>">All Outlet</a>
                        </li>
                        <li >
                            <strong>Edit Outlet</strong>
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
                            <h1>Outlet </h1>
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

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(route('site.update',$site->id)); ?>" enctype="multipart/form-data"
                                  method="post">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('PUT')); ?>


                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Market
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="mktm_id" id="mktm_id"
                                                    required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $govMarket; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $govMarket1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($govMarket1->id); ?>"><?php echo e($govMarket1->mktm_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sub Channel
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="scnl_id" id="scnl_id"
                                                    required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $subChannels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subChannels1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($subChannels1->id); ?>"><?php echo e($subChannels1->scnl_name.'-'.$subChannels1->scnl_code); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Outlet
                                            Category
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="otcg_id"
                                                    id="otcg_id"
                                                    required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $outletCategorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outletCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($outletCategory->id); ?>"><?php echo e($outletCategory->otcg_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_name" name="site_name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_name); ?>"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Site Code <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_code" name="site_code" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_code); ?>"
                                                   placeholder="Code" required="required" type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Outlet Code <span
                                                    class="required"></span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="outlet_code" name="outlet_code" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($outlet->oult_code); ?>"
                                                   placeholder="Outlet Code"  type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_olnm" name="site_olnm" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Ln Name"  type="text"  value="<?php echo e($site->site_olnm); ?>"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Address
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_adrs" name="site_adrs" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   placeholder="Address"  type="text"  value="<?php echo e($site->site_adrs); ?>"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Address
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_olad" name="site_olad"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_olad); ?>"
                                                   placeholder="Ln Address"  type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <?php if(Auth::user()->country()->module_type==2): ?>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                                            Owner Nationality
                                            <span
                                                    class="required"></span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="ow_cont_id"
                                                    id="ow_cont_id"
                                                    >
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($scmp): ?>
                                                        <?php if($cnt->id==$scmp[0]->cont_id): ?>
                                                        <option value="<?php echo e($cnt->id); ?>" selected><?php echo e($cnt->cont_code.'-'.$cnt->cont_name); ?></option>
                                                        <?php else: ?>
                                                        <option value="<?php echo e($cnt->id); ?>"><?php echo e($cnt->cont_code.'-'.$cnt->cont_name); ?></option>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                    <option value="<?php echo e($cnt->id); ?>"><?php echo e($cnt->cont_code.'-'.$cnt->cont_name); ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Owner Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_ownm" name="site_ownm"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_ownm); ?>"
                                                   placeholder="Owner Name"  type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Owner
                                            Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_olon" name="site_olon"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_olon); ?>"
                                                   placeholder="Ln Owner Name"  type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile
                                            1
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_mob1" name="site_mob1" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_mob1); ?>"
                                                   placeholder="Mobile 1"  type="text"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile 2
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_mob2" name="site_mob2" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_mob2); ?>"
                                                   placeholder="Mobile 2" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_emal" name="site_emal" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"  value="<?php echo e($site->site_emal); ?>"
                                                   placeholder="Email" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Reg No
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_reg" name="site_reg" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" value="<?php echo e($site->site_reg); ?>"
                                                   placeholder="Reg No" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">House No
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_hsno" name="site_hsno" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" value="<?php echo e($site->site_hsno); ?>"
                                                   placeholder="House No" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <?php if(Auth::user()->country()->module_type==2): ?>

                                    <?php if($scmp): ?>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">License No
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="licn_no" name="licn_no" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($scmp[0]->licn_no); ?>"
                                                   placeholder="License no" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Expiry Date
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="expr_date" name="expr_date" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($scmp[0]->expr_date); ?>"
                                                   
                                                   step="1">
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">License No
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="licn_no" name="licn_no" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e(old('licn_no')); ?>"
                                                   placeholder="License no" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Expiry Date
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="expr_date" name="expr_date" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo date('Y-m-d'); ?>"
                                                   
                                                   step="1">
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat TRN No
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_vtrn" name="site_vtrn" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" value="<?php echo e($site->site_vtrn); ?>"
                                                   placeholder="Vat TRN No" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat Status<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="site_vsts" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" <?php echo e($site->site_vsts == '1' ? 'checked' : ''); ?>

                                                   name="site_vsts" type="checkbox"
                                            >
                                        </div>
                                    </div>
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Submit</button>
                                        </div>
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
        $(document).ready(function() {

            const mktm_id = '<?php echo e($site->mktm_id); ?>';
            const scnl_id = '<?php echo e($site->scnl_id); ?>';
            const otcg_id = '<?php echo e($site->otcg_id); ?>';
            if(mktm_id !== '') {
                $('#mktm_id').val(mktm_id);
            }
            if(scnl_id !== '') {
                $('#scnl_id').val(scnl_id);
            }
            if(otcg_id !== '') {
                $('#otcg_id').val(otcg_id);
            }
            $("#mktm_id").select2({width: 'resolve'});
            $("#scnl_id").select2({width: 'resolve'});
            $("#otcg_id").select2({width: 'resolve'});
            $("#ow_cont_id").select2({width: 'resolve'});
            $('#expr_date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '0d',
                    
                    autoclose: 1,
                    showOnFocus: true
            });
        });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/site/edit.blade.php ENDPATH**/ ?>