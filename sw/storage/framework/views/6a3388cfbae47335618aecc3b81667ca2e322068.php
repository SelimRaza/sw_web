

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
                            <a href="<?php echo e(URL::to('/sku')); ?>">All SKU</a>
                        </li>
                        <li class="active">
                            <strong>Edit SKU</strong>
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
                            <center><h5><strong> ::: Edit Item ::: </strong></h5></center>

                        </div>

                        <div class="x_content">

                            <form class="form-horizontal form-label-left"
                                  action="<?php echo e(route('sku.update',$sku->id)); ?>" enctype="multipart/form-data"
                                  method="post">
                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('PUT')); ?>

                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_name" value="<?php echo e($sku->amim_name); ?>"
                                                   placeholder="Name" required="required" type="text" maxlength="30">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item Code
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_code" value="<?php echo e($sku->amim_code); ?>"
                                                   placeholder="Item Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bangla Name

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_olin" value="<?php echo e($sku->amim_olin); ?>"
                                                   placeholder="Bangla Name" type="text">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bar Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_bcod" value="<?php echo e($sku->amim_bcod); ?>"
                                                   placeholder="Bar Code" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item Class
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="itcl_id" id="itcl_id"
                                                    required>
                                                <option value="">Select Class</option>
                                                <?php $__currentLoopData = $itemClass; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemClass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($itemClass->id); ?>"><?php echo e($itemClass->itcl_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sub Category
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="itsg_id" id="itsg_id"
                                                    required>
                                                <option value="">Select SubCategory</option>
                                                <?php $__currentLoopData = $subCategorys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($subCategory->id); ?>"><?php echo e($subCategory->itsg_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Short
                                            Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amin_snme" value="<?php echo e($sku->amin_snme); ?>"
                                                   placeholder="Short Name" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thikness

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_tkns" value="<?php echo e($sku->amim_tkns); ?>"
                                                   placeholder="Thikness" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Excise Per
                                            CTN

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_pexc" value="<?php echo e($sku->amim_pexc); ?>"
                                                   placeholder="Excise Per CTN" step="any" type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat
                                            Percentage

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_pvat" value="<?php echo e($sku->amim_pvat); ?>"
                                                   placeholder="Vat Percentage" step="any" type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Image (Max
                                            Size 5 MB)
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="amim_full" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e(old('amim_imgl')); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_imgl"
                                                   placeholder="Image" type="file"
                                                   step="1">
                                        </div>

                                    </div>
                                    <?php if(Auth::user()->country()->module_type==2 && $country !=''): ?>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Source Country
                                            <span
                                                    class=""></span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="cont_id" id="cont_id"
                                                    >
                                                <option value="">Select country</option>
                                                <?php $__currentLoopData = $country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($icmp): ?>
                                                    
                                                    <option value="<?php echo e($cnt->id); ?>" <?php echo e($icmp[0]->cont_id==$cnt->id?'selected':''); ?>><?php echo e($cnt->cont_code.'-'.$cnt->cont_name); ?></option>
                                                    
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
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CTN Size
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_duft" value="<?php echo e($sku->amim_duft); ?>"
                                                   placeholder="CTN Size" min="0" required="required" type="number"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Retails Unit
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">

                                            <select class="form-control" name="amim_runt" id="amim_runt"
                                                    required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $itemUnit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemUnit1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($itemUnit1->id); ?>"><?php echo e($itemUnit1->unit_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Distribution
                                            Unit
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">

                                            <select class="form-control" name="amim_dunt" id="amim_dunt"
                                                    required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $itemUnit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemUnit1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($itemUnit1->id); ?>"><?php echo e($itemUnit1->unit_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mother
                                            Company
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">

                                            <select class="form-control" name="amim_acmp" id="amim_acmp"
                                                    required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $company; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($company1->id); ?>"><?php echo e($company1->acmp_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Dealer Price

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($sku->amim_dppr); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_dppr"
                                                   placeholder="Dealer Price" min="0" type="number"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Trade Price

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($sku->amim_tppr); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_tppr"
                                                   placeholder="Trade Price" min="0" type="number"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">MRP

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($sku->amim_mrpp); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_mrpp"
                                                   placeholder="MRP" min="0" type="number"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CBM

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($sku->amim_cbm); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_cbm"
                                                   placeholder="CBM" min="0" type="number"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Is Sales
                                            Able
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   <?php echo e($sku->amim_issl == '1' ? 'checked' : ''); ?>

                                                   name="amim_issl" type="checkbox"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Color

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e($sku->amim_colr); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_colr"
                                                   placeholder="Color" min="0" type="text"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Image (Max
                                            Size 50 KB)
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="image_icon" class="form-control col-md-7 col-xs-12"
                                                   value="<?php echo e(old('amim_imgl')); ?>"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_icon"
                                                   placeholder="Image" type="file"
                                                   step="1">
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <center>
                                                <button id="send" type="submit" class="btn btn-success">Submit</button>
                                            </center>
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
        $(document).ready(function () {

            const itcl_id = '<?php echo e($sku->itcl_id); ?>';
            const itsg_id = '<?php echo e($sku->itsg_id); ?>';
            const amim_runt = '<?php echo e($sku->amim_runt); ?>';
            const amim_dunt = '<?php echo e($sku->amim_dunt); ?>';
            const amim_acmp = '<?php echo e($sku->amim_acmp); ?>';
            if (itcl_id !== '') {
                $('#itcl_id').val(itcl_id);
            }
            if (itsg_id !== '') {
                $('#itsg_id').val(itsg_id);
            }
            if (amim_runt !== '') {
                $('#amim_runt').val(amim_runt);
            }
            if (amim_dunt !== '') {
                $('#amim_dunt').val(amim_dunt);
            }
            if (amim_acmp !== '') {
                $('#amim_acmp').val(amim_acmp);
            }
            $("#itcl_id").select2({width: 'resolve'});
            $("#itsg_id").select2({width: 'resolve'});
            $("#amim_runt").select2({width: 'resolve'});
            $("#amim_dunt").select2({width: 'resolve'});
            $("#amim_acmp").select2({width: 'resolve'});
            $("#cont_id").select2({width: 'resolve'});
        });

        var amim_fulld = document.getElementById("amim_full");
        //5MB
        const maxAllowedSize = 1 * 400 * 1024;
        amim_fulld.onchange = function () {
            if (this.files[0].size > maxAllowedSize) {
                alert("Image size is big!!! Max allowed size 400 KB");
                this.value = "";
            }
            ;
        };
        var image_icons = document.getElementById("image_icon");
        const maxAllowedSizedd = 1 * 200 * 1024;
        image_icons.onchange = function () {

            if (this.files[0].size > maxAllowedSizedd) {
                alert("Image size is big!!! Max allowed size 200 KB");
                this.value = "";
            }
            ;
        };

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/sku/edit.blade.php ENDPATH**/ ?>