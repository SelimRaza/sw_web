

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
                            <a href="<?php echo e(URL::to('/price_list')); ?>">All Price List</a>
                        </li>
                        <li class="label-success">
                            <a href="<?php echo e(URL::to('/price_list/sku/'.$priceList->id)); ?>">Price List Item</a>
                        </li>
                        <li>
                            <strong>Edit Price List Item Edit</strong>
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
                            <h1>Price List</h1>
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
                                  action="<?php echo e(URL::to('price_list/sku_item_edit/'.$priceListDetails->id)); ?>"
                                  method="POST">
                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('POST')); ?>

                                <div class="col-md-6">

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">
                                            DP per CTN <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="pldt_dppr"
                                                   value="<?php echo e($priceListDetails->pldt_dppr*$priceListDetails->amim_duft); ?>"
                                                   placeholder="DP per CTN" step="any" required="required"
                                                   type="number">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">TP per
                                            CTN <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="pldt_tppr"
                                                   value="<?php echo e($priceListDetails->pldt_tppr*$priceListDetails->amim_duft); ?>"
                                                   placeholder="TP per
                                                        CTN" step="any" required="required"
                                                   type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CTN
                                            Size<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_duft"
                                                   min="1"
                                                   value="<?php echo e($priceListDetails->amim_duft); ?>"
                                                   placeholder="CTN Size"
                                                   required="required"
                                                   type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Short
                                            Name<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="pldt_snme"
                                                   value="<?php echo e($priceListDetails->pldt_snme); ?>"
                                                   placeholder="Short Name" required="required" type="text">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">DP GRV per
                                            CTN<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="pldt_dgpr"
                                                   value="<?php echo e($priceListDetails->pldt_dgpr*$priceListDetails->amim_duft); ?>"
                                                   placeholder="DP GRV per
                                                         CTN" step="any" required="required"
                                                   type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">TP
                                            GRV
                                            Per CTN<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="pldt_tpgp"
                                                   value="<?php echo e($priceListDetails->pldt_tpgp*$priceListDetails->amim_duft); ?>"
                                                   placeholder="TP
                                                        GRV
                                                        CTN Price" step="any" required="required"
                                                   type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">MRP Per CTN<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="pldt_mrpp"
                                                   value="<?php echo e($priceListDetails->pldt_mrpp*$priceListDetails->amim_duft); ?>"
                                                   placeholder="MRP Per CTN" step="any" required="required"
                                                   type="number">
                                        </div>
                                    </div>


                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Save</button>
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
    <script type="text/javascript">
        $('.date_pick').datetimepicker({format: 'HH:mm:ss'});
        $("#company_id").select2({width: 'resolve'});

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/PriceList/price_list_item_edit.blade.php ENDPATH**/ ?>