

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
                            <strong>Edit Employee</strong>
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
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="<?php echo e(route('employee.store')); ?>"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <?php echo e(csrf_field()); ?>


                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Designation <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="role_id" id="role_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($role->id); ?>"><?php echo e(ucfirst($role->edsg_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Role <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="master_role_id" id="master_role_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $masterRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($masterRole->id); ?>"><?php echo e(ucfirst($masterRole->role_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Designation <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="master_role_id" id="master_role_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $masterRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($masterRole->id); ?>"><?php echo e(ucfirst($masterRole->role_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Role <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="role_id" id="role_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($role->id); ?>"><?php echo e(ucfirst($role->edsg_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>




                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Manager ID
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="manager_id" value="<?php echo e(old('manager_id')); ?>"
                                               placeholder="user_name" required="required" type="text">
                                       
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Line Manager
                                        ID
                                        <span
                                                class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="line_manager_id" value="<?php echo e(old('line_manager_id')); ?>"
                                               placeholder="user_name" required="required" type="text">
                                       
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-6 col-xs-12 col-md-offset-1">
                                            <label class="control-label" for="name">Manager ID <span class="required">*</span></label>
                                            <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="manager_id" value="<?php echo e(old('manager_id')); ?>" placeholder="User Name" required="required" type="text">
                                        </div>

                                        <div class="col-md-5 col-sm-6 col-xs-12">
                                            <label class="control-label" for="name">Line Manager ID <span class="required">*</span></label>
                                            <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="line_manager_id" value="<?php echo e(old('line_manager_id')); ?>" placeholder="User Name" required="required" type="text">
                                        </div>
                                    </div>
                                </div> -->

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Manager ID <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="manager_id" value="<?php echo e(old('manager_id')); ?>" placeholder="User Name" required="required" type="text">

                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Line Manager ID <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="line_manager_id" value="<?php echo e(old('line_manager_id')); ?>" placeholder="User Name" required="required" type="text">

                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Full Name <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name" value="<?php echo e(old('name')); ?>"
                                               placeholder="Full Name" required="required" type="text">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Last Name <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="ln_name" value="<?php echo e(old('ln_name')); ?>"
                                               placeholder="Ln Name" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">User ID <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="email" value="<?php echo e(old('email')); ?>"
                                               placeholder="User Name" required="required" type="text">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Email <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="address" value="<?php echo e(old('address')); ?>"
                                               placeholder="email@eacmple.com" type="email">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Email CC <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="email_cc"
                                               placeholder="email1@exmple.com,email2@exmple.com" type="text" value="<?php echo e(old('email_cc')); ?>"
                                               step="any">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Mobile <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="mobile" value="<?php echo e(old('mobile')); ?>"
                                               placeholder="Mobile" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Menu Group <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="amng_id" id="amng_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $appMenuGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appMenuGroup1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($appMenuGroup1->id); ?>"><?php echo e(ucfirst($appMenuGroup1->amng_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Profile Image <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="input_img" value="<?php echo e(old('input_img')); ?>"
                                               placeholder="Image" type="file"
                                               step="1">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Allowed Distance <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="allowed_distance" value="0"
                                               placeholder="Allowed Distance" required="required" type="number" value="<?php echo e(old('allowed_distance')); ?>"
                                               step="any">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Outlet Code <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="outlet_code" name="outlet_code" class="form-control col-md-7 col-xs-12"
                                                data-validate-length-range="6" data-validate-words="2"  value="<?php echo e(old('outlet_code')); ?>"
                                                placeholder="Outlet Code"  type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Credit Limit <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_crdt" value="0"
                                               placeholder="Amount" required="required" type="number" value="<?php echo e(old('aemp_crdt')); ?>"
                                               step="any">
                                    </div>
                                    <?php if(Auth::user()->country()->module_type==2): ?>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Nationality <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" name="cont_id" id="cont_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($cnt->id); ?>"><?php echo e(ucfirst($cnt->cont_code.'-'.$cnt->cont_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php if(Auth::user()->country()->module_type==2): ?>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Visa Number <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12" name="visa_no"
                                               placeholder="Visa Number" type="text"
                                               step="1">
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Expiry Date <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="expr_date" class="form-control col-md-7 col-xs-12" name="expr_date"
                                                value="<?php echo date('Y-m-d');?>"
                                               step="1">
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Auto Email <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"  <?php echo e(old('auto_email') == 'on' ? 'checked' : ''); ?>

                                               name="auto_email" type="checkbox"
                                        >
                                    </div>
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Live Location <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"  <?php echo e(old('location_on') == 'on' ? 'checked' : ''); ?>

                                               data-validate-length-range="6" data-validate-words="2"
                                               name="location_on" type="checkbox"
                                        >
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">Sales Person <span class="required">*</span></label>
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               <?php echo e(old('aemp_issl') == 'on' ? 'checked' : ''); ?> data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_issl" type="checkbox"
                                        >
                                    </div>
                                   
                                </div>

                                    
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">User ID <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="email" value="<?php echo e(old('email')); ?>"
                                               placeholder="User Name" required="required" type="text">
                                    </div>
                                </div> -->
                                

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Full Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="name" value="<?php echo e(old('name')); ?>"
                                               placeholder="Full Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ln Name
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="ln_name" value="<?php echo e(old('ln_name')); ?>"
                                               placeholder="Ln Name" type="text">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="address" value="<?php echo e(old('address')); ?>"
                                               placeholder="email@eacmple.com" type="email">
                                    </div>
                                </div> -->


                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Email CC

                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="email_cc"
                                               placeholder="email1@exmple.com,email2@exmple.com" type="text" value="<?php echo e(old('email_cc')); ?>"
                                               step="any">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Auto Email
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"  <?php echo e(old('auto_email') == 'on' ? 'checked' : ''); ?>

                                               name="auto_email" type="checkbox"
                                        >
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Live Location
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"  <?php echo e(old('location_on') == 'on' ? 'checked' : ''); ?>

                                               data-validate-length-range="6" data-validate-words="2"
                                               name="location_on" type="checkbox"
                                        >
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Is Sales Person
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               <?php echo e(old('aemp_issl') == 'on' ? 'checked' : ''); ?> data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_issl" type="checkbox"
                                        >
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mobile
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="mobile" value="<?php echo e(old('mobile')); ?>"
                                               placeholder="Mobile" type="text">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Allowed Distance
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="allowed_distance" value="0"
                                               placeholder="Allowed Distance" required="required" type="number" value="<?php echo e(old('allowed_distance')); ?>"
                                               step="any">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Customer id
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="site_id" value="0"
                                               placeholder="Site Id" required="required" type="number" value="<?php echo e(old('site_id')); ?>"
                                               step="any">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Outlet Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="outlet_code" name="outlet_code" class="form-control col-md-7 col-xs-12"
                                                data-validate-length-range="6" data-validate-words="2"  value="<?php echo e(old('outlet_code')); ?>"
                                                placeholder="Outlet Code"  type="text">
                                    </div>
                                </div> -->

                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Personal Credit Limit
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               name="aemp_crdt" value="0"
                                               placeholder="Amount" required="required" type="number" value="<?php echo e(old('aemp_crdt')); ?>"
                                               step="any">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Profile
                                        Image<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="input_img" value="<?php echo e(old('input_img')); ?>"
                                               placeholder="Image" type="file"
                                               step="1">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">App Menu Group <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="amng_id" id="amng_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $appMenuGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appMenuGroup1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($appMenuGroup1->id); ?>"><?php echo e(ucfirst($appMenuGroup1->amng_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div> -->
                                <!-- <?php if(Auth::user()->country()->module_type==2): ?> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Nationality<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="cont_id" id="cont_id" required>
                                            <option value="">Select</option>
                                            <?php $__currentLoopData = $country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($cnt->id); ?>"><?php echo e(ucfirst($cnt->cont_code.'-'.$cnt->cont_name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Visa No
                                        <span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12" name="visa_no"
                                               placeholder="Visa Number" type="text"
                                               step="1">
                                    </div>
                                </div> -->
                                <!-- <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Expiry Date
                                        <span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="expr_date" class="form-control col-md-7 col-xs-12" name="expr_date"
                                                value="<?php echo date('Y-m-d');?>"
                                               step="1">
                                    </div>
                                </div> -->
                                <!-- <?php endif; ?> -->
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success"> Submit</button>
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
            $("#role_id").select2({width: 'resolve'});
            $("#master_role_id").select2({width: 'resolve'});
            $("#amng_id").select2({width: 'resolve'});
            $("#cont_id").select2({width: 'resolve'});
            const role_id = '<?php echo e(old('role_id')); ?>';
            const master_role_id = '<?php echo e(old('master_role_id')); ?>';
            const amng_id = '<?php echo e(old('amng_id')); ?>';
            if(role_id !== '') {
                $('#role_id').val(role_id);
            }
            if(master_role_id !== '') {
                $('#master_role_id').val(master_role_id);
            }
            if(amng_id !== '') {
                $('#amng_id').val(amng_id);
            }
            $('#expr_date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '0d',               
                    autoclose: 1,
                    showOnFocus: true
            });
        });


    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/create.blade.php ENDPATH**/ ?>