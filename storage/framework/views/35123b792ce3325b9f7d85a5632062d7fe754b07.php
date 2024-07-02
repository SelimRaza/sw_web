

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/companySiteMapping')); ?>"><i class="fa fa-home"></i> Back</a>
                        </li>
                        <li class="active">
                            <strong>Credit Edit</strong>
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
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Site Credit Adjust</h1>
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

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Group</th>
                                    <th>Price List</th>
                                    <th>Site Code</th>
                                    <th>Credit Limit</th>
                                    <th>Limit Day</th>
                                    <th>Credit Limit Type</th>
                                    <th>Payment Type</th>
                                </tr>
                                </thead>
                                <tbody id="cont">
                                    <form class="form-control" method="post" action="<?php echo e(URL::to('credit-adjust')); ?>">
                                        <?php echo e(csrf_field()); ?>

                                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><input type="hidden" name="stcm_id[]" value="<?php echo e($dt->stcm_id); ?>"><?php echo e($dt->acmp_name); ?></td>
                                            <td><?php echo e($dt->slgp_name); ?></td>
                                            <td><?php echo e($dt->plmt_name); ?></td>
                                            <td><?php echo e($dt->site_code); ?></td>
                                            <td><input type="number" class="form-control in_tg" name="stcm_limt[]" value="<?php echo e($dt->stcm_limt); ?>"></td>
                                            <td><input type="number" class="form-control in_tg" name="stcm_days[]" value="<?php echo e($dt->stcm_days); ?>"></td>
                                            <td>
                                                <select class="form-control cmn_select2" name="stcm_isfx[]"
                                                        id="stcm_isfx">
                                                    <?php if($dt->stcm_isfx==1): ?>
                                                    <option value="1" selected>Fixed</option>
                                                    <option value="0">Variable</option>
                                                    <?php else: ?>
                                                    <option value="0" selected>Variable</option>
                                                    <option value="1">Fixed</option>
                                                    <?php endif; ?>
                                                    
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control cmn_select2" name="optp_id[]"
                                                        id="optp_id">
                                                    <?php if($dt->optp_id==1): ?>
                                                    <option value="1" selected>Cash</option>
                                                    <option value="2">Credit</option>
                                                    <?php else: ?>
                                                    <option value="2" selected>Credit</option>
                                                    <option value="1" >Cash</option>
                                                    <?php endif; ?>
                                                    
                                                </select>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td colspan="8"><button type="submit" class=" btn btn-success" style="float:right;">Update</button></td>
                                        </tr>
                                    </form>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(".cmn_select2").select2({width: 'resolve'});
        var user_name = $("#user_name").val();
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/CompanySiteMapping/credit_edit.blade.php ENDPATH**/ ?>