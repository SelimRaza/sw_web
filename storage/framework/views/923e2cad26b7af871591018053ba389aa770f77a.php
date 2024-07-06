

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
                            <a href="<?php echo e(URL::to('block/maintainBlock')); ?>">Block Order </a>
                        </li>
                        <li class="active">
                            <strong>Over Due Block Release</strong>
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
                        <strong>Fail! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Over Due Block Release</h1>
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
                                  action="<?php echo e(URL::to('block/creditReleaseAction/'.$orderMaster->id)); ?>"
                                  method="post">
                                <?php echo e(csrf_field()); ?>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="usr">Outlet:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->Outlet_Name); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Order Id:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->Order_ID); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Address:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->Address); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Order Date:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->order_date); ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Order Amount:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->total_price); ?>" readonly>
                                            <input type="hidden" name="net_amount"
                                                   value="<?php echo e($orderMaster->total_price); ?>">
                                        </div>


                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="usr">Customer Code:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->customer_number); ?>"
                                                   readonly>
                                            <input
                                                    class="form-control" type="hidden" name="site_id"
                                                    value="<?php echo e($orderMaster->Site_ID); ?>">
                                        </div>


                                        <div class="form-group">
                                            <label for="usr">SR Name:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->sr_name); ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="usr">Manager name:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->manager_name); ?>" readonly>
                                            <input
                                                    class="form-control" type="hidden" name="sv_id"
                                                    value="<?php echo e($orderMaster->manager_code); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Payment Type:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="<?php echo e($orderMaster->payMode); ?>" readonly>
                                        </div>


                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="x_panel">

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <h4 style="color:#225fc1">Order Details</h4>
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                    <tr style="background-color: #2b4570; color: white;">

                                                        <th>Item Code</th>
                                                        <th>Item Description</th>
                                                        <th>O Qty(CS)</th>
                                                        <th>CTN SIZE</th>
                                                        <th>Rate</th>
                                                        <th>Default Disc</th>
                                                        <th>Promo Disc</th>
                                                        <th>Spl Disc</th>
                                                        <th>Total Disc</th>
                                                        <th>Amt</th>
                                                        <th>Net Amt</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $__currentLoopData = $orderLine; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $orderLine1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($orderLine1->Product_id); ?></td>
                                                            <td><?php echo e($orderLine1->Product_Name); ?></td>
                                                            <td><?php echo e($orderLine1->Product_Quantity/$orderLine1->ctn_size); ?></td>
                                                            <td><?php echo e($orderLine1->ctn_size); ?></td>
                                                            <td><?php echo e($orderLine1->Rate); ?></td>
                                                            <td><?php echo e($orderLine1->default_discount); ?></td>
                                                            <td><?php echo e($orderLine1->promo_discount); ?></td>
                                                            <td><?php echo e($orderLine1->Discount); ?></td>
                                                            <td><?php echo e($orderLine1->Discount+$orderLine1->default_discount+$orderLine1->promo_discount); ?> </td>
                                                            <td><?php echo e($orderLine1->Total_Item_Price); ?></td>
                                                            <td><?php echo e($orderLine1->Total_Item_Price-$orderLine1->Discount-$orderLine1->default_discount-$orderLine1->promo_discount); ?> </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="nav navbar-light panel_toolbox">
                                            <?php if($orderMaster->order_status==9): ?>
                                                <input
                                                        class="form-control" type="hidden" name="so_id"
                                                        value="<?php echo e($orderMaster->id); ?>">
                                                <input
                                                        class="form-control" type="hidden" name="ou_id"
                                                        value="<?php echo e($orderMaster->ou_id); ?>">
                                                <button type="submit" class="btn btn-success"
                                                        name="submit" value="release"
                                                >Release
                                                </button>
                                                <p class="btn btn-success" id="myBtn">Cancel</p>
                                                <div id="myModal" class="modal">
                                                    <div class="modal-content">
                                                        <span class="close">&times;</span>

                                                        <label for="usr">Cancel Reason:</label>
                                                        <select class="form-control" id="reject_id"
                                                                name="reject_id">
                                                            <?php $__currentLoopData = $cancelReason; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cancelReason1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value=<?php echo e($cancelReason1->id); ?>><?php echo e($cancelReason1->ocrs_name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                        <button type="submit" class="btn btn-success"
                                                                name="submit" value="cancel">Cancel
                                                        </button>
                                                    </div>

                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("myBtn");
        var span = document.getElementsByClassName("close")[0];
        btn.onclick = function () {
            modal.style.display = "block";
        }
        span.onclick = function () {
            modal.style.display = "none";
        }
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/blockOrder/credit_release.blade.php ENDPATH**/ ?>