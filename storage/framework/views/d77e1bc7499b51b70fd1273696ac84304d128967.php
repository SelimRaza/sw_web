

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
                            <strong>Show Employee</strong>
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
                            <h1>Employee </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">
                                <form class="form-horizontal form-label-left"
                                      action="<?php echo e(route('employee.update',$employee->id)); ?>"
                                      method="post">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('PUT')); ?>


                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Designation
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="<?php echo e($employee->role_name); ?>"
                                                   placeholder="Code" required="required" type="text">

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Manger <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="<?php echo e($employee->manager_name); ?>"
                                                   placeholder="Code" required="required" type="text">

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Line Manger
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="<?php echo e($employee->line_manager_name); ?>"
                                                   placeholder="Code" required="required" type="text">

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="email"
                                                   value="<?php echo e($employee->email); ?>"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Full Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="name"
                                                   value="<?php echo e($employee->name); ?>"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Name
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="ln_name"
                                                   value="<?php echo e($employee->ln_name); ?>"
                                                   placeholder="Ln Name" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="address"
                                                   value="<?php echo e($employee->address); ?>"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email CC
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="email_cc"
                                                   value="<?php echo e($employee->email_cc); ?>"
                                                   placeholder="email1@exmple.com,email2@exmple.com" type="text"
                                                   step="any">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Auto Email
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="<?php echo $employee->auto_email == "1" ? "Yes" : "No" ?>"
                                                   name="auto_email" type="text"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Live
                                            Location
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="<?php echo $employee->location_on == "1" ? "Yes" : "No" ?>"
                                                   name="auto_email" type="text"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="mobile"
                                                   value="<?php echo e($employee->mobile); ?>"
                                                   placeholder="Mobile" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Allowed
                                            Distance <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   name="allowed_distance" value="<?php echo e($employee->allowed_distance); ?>"
                                                   placeholder="Allowed Distance" required="required" type="number"
                                                   step="any">
                                        </div>
                                    </div>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Company </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">

                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th> Company Name</th>
                                        <th> Company Code</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    <?php $__currentLoopData = $companyMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($companyMapping1->acmp_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($companyMapping1->acmp_code); ?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Group </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">
                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Group Name</th>
                                        <th>Group code</th>
                                        <th>Price List</th>
                                        <th>Price Code</th>
                                        <th>Zone Name</th>
                                        <th>Zone Code</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    <?php $__currentLoopData = $salesGroupMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesGroupMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($salesGroupMapping1->slgp_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($salesGroupMapping1->slgp_code); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($salesGroupMapping1->plmt_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($salesGroupMapping1->plmt_code); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($salesGroupMapping1->zone_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($salesGroupMapping1->zone_code); ?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Dealer </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">

                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Depot Name</th>
                                        <th>Depot Code</th>
                                        <th>Company name</th>
                                        <th>Base name</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    <?php $__currentLoopData = $depotMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($depotMapping1->dlrm_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($depotMapping1->dlrm_code); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($depotMapping1->acmp_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($depotMapping1->base_name); ?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Route </h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">
                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Day</th>
                                        <th>Route Name</th>
                                        <th>Route code</th>
                                        <th>Base Name</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    <?php $__currentLoopData = $routePlanMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routePlanMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($routePlanMapping1->rpln_day); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($routePlanMapping1->rout_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($routePlanMapping1->rout_code); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($routePlanMapping1->base_name); ?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Zone Group Supervisor Mapping</h1>
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
                        <div class="x_content" class="form-horizontal form-label-left">

                            <div class="row">

                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Group Name</th>
                                        <th>Group Code</th>
                                        <th>Route Name</th>
                                        <th>Route Code</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">
                                    <?php $__currentLoopData = $zoneGroupMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneGroupMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($zoneGroupMapping1->slgp_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($zoneGroupMapping1->slgp_code); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($zoneGroupMapping1->zone_name); ?>" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       value="<?php echo e($zoneGroupMapping1->zone_code); ?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/show.blade.php ENDPATH**/ ?>