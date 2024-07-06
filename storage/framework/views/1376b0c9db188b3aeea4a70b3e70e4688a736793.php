

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
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="x_panel">

                        <ul class="nav nav-pills">
                            <li>
                                <a href="#1a" data-toggle="tab" class="btn btn-default btn-xs" onclick="showProfile()">Profile </a>
                            </li>
                            <li>
                                <a href="#2a" data-toggle="tab" class="btn btn-default btn-xs" onclick="showCompany()">Company </a>
                            </li>
                            <li><a href="#3a" data-toggle="tab" class="btn btn-default btn-xs" onclick="showRpln()"> Route Plan</a></li>
                            <?php if($employee->role()->id!='1'): ?>
                                <li><a href="#4a" data-toggle="tab" class="btn btn-default btn-xs" onclick="showZgsm()">TSM Zone Mapping </a></li>
                            <?php endif; ?>
                            <li><a href="#5a" data-toggle="tab" class="btn btn-default btn-xs" onclick="showSRThana()">SR Thana Mapping</a>
                            </li>
                        </ul>
                    </div>

                    

                    <div class="x_panel" id="profile_id">
                            <div class="x_title">
                                <center><strong> :::Employee:::</strong></center>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <form class="form-horizontal form-label-left"
                                      action="<?php echo e(route('employee.update',$employee->id)); ?>"
                                      method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('PUT')); ?>

                                    <?php
                                    if ($site_code==''){
                                        $site_code=0;
                                    }
                                    ?>

                                    <div class="item form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">User ID <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" name="email"
                                                       value="<?php echo e($employee->aemp_usnm); ?>"
                                                       placeholder="Code" required="required" type="text">
                                                <input type="hidden" id="employee_h_id" value="<?php echo e($employee->id); ?>" />
                                                <input id="emp_id" value="<?php echo e($employee->id); ?>" type="hidden">

                                            </div>

                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Full Name <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" name="name"
                                                       value="<?php echo e($employee->aemp_name); ?>"
                                                       placeholder="Name" required="required" type="text">
                                            </div>

                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Ln Name
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="ln_name"
                                                       value="<?php echo e($employee->aemp_onme); ?>"
                                                       placeholder="Ln Name" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Mobile
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="mobile"
                                                       value="<?php echo e($employee->aemp_mob1); ?>"
                                                       placeholder="Mobile" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Line Manger ID
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="user_name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="line_manager_id" value="<?php echo e($employee->lineManager()->aemp_usnm); ?>"
                                                       placeholder="user_name" required="required" type="text">

                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Manger ID <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="user_name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" name="manager_id"
                                                       value="<?php echo e($employee->manager()->aemp_usnm); ?>"
                                                       placeholder="user_name" required="required" type="text">
                                            </div>
                                        </div>


                                    </div>
                                    <div class="item form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Designation
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="role_id" id="role_id" required>
                                                    <option value="<?php echo e($employee->role()->id); ?>"><?php echo e($employee->role()->edsg_name); ?></option>
                                                    <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($role->id); ?>"><?php echo e(ucfirst($role->edsg_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Role <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="master_role_id" id="master_role_id" required>
                                                    <option value="<?php echo e($employee->masterRole()->id); ?>"><?php echo e($employee->masterRole()->role_name); ?></option>
                                                    <?php $__currentLoopData = $masterRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($employee->masterRole()->id!=$masterRole->id): ?>
                                                            <option value="<?php echo e($masterRole->id); ?>"><?php echo e(ucfirst($masterRole->role_name)); ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Email
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="address"
                                                       value="<?php echo e($employee->aemp_emal); ?>"
                                                       placeholder="Email" type="text">
                                            </div>
                                        </div>

                                    </div>


                                    <div class="item form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Email CC
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="email_cc"
                                                       value="<?php echo e($employee->aemp_emcc); ?>"
                                                       placeholder="email1@exmple.com,email2@exmple.com" type="text"
                                                       step="any">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Allowed Distance
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <?php
                                            $emp_dis= $employee->aemp_aldt;
                                            // if ($emp_dis=='0'){
                                            //     if ($employee->cont_id ==2){
                                            //         $emp_dis=100;
                                            //     }
                                            //     if ($employee->cont_id ==5){
                                            //         $emp_dis=200;
                                            //     }
                                            // }


                                            ?>

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="allowed_distance" value="<?php echo e($emp_dis); ?>"
                                                       placeholder="Allowed Distance" required="required" type="number"
                                                       step="any">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Customer Code
                                                <span
                                                        class="required"></span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="site_id" value="<?php echo e($site_code); ?>"
                                                       placeholder="" required="required" type="number"
                                                       step="any">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="item form-group">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Personal Credit
                                                Limit
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       name="aemp_crdt" value="<?php echo e($employee->aemp_crdt); ?>"
                                                       placeholder="" required="required" type="number"
                                                       step="any">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left"> Profile
                                                Image <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <input id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" name="input_img"
                                                       placeholder="Image" type="file"
                                                       step="1">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">App Menu Group
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select class="form-control" name="amng_id" id="amng_id" required>
                                                    <option value="">Select</option>
                                                    <?php $__currentLoopData = $appMenuGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appMenuGroup1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($appMenuGroup1->id); ?>"><?php echo e(ucfirst($appMenuGroup1->amng_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(Auth::user()->country()->module_type==2): ?>
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Nationality<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control" name="cont_id" id="cont_id" required>
                                                        <option value="">Select</option>
                                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($ecmp): ?>
                                                                <option value="<?php echo e($cnt->id); ?>" <?php echo e($ecmp->cont_id==$cnt->id?'selected':''); ?>><?php echo e(ucfirst($cnt->cont_code.'-'.$cnt->cont_name)); ?></option>
                                                            <?php else: ?>
                                                                <option value="<?php echo e($cnt->id); ?>"><?php echo e(ucfirst($cnt->cont_code.'-'.$cnt->cont_name)); ?></option>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Visa No
                                                    <span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input id="name" class="form-control col-md-7 col-xs-12" name="visa_no"
                                                           value="<?php echo e($ecmp?$ecmp->visa_no:''); ?>" type="text"
                                                           step="1">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Expiry Date
                                                    <span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input id="expr_date" class="form-control col-md-7 col-xs-12" name="expr_date"
                                                           value="<?php echo e($ecmp?$ecmp->expr_date:''); ?>"
                                                           step="1">
                                                </div>
                                            </div>

                                        </div>
                                    <?php endif; ?>

                                    <div class="item form-group">
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="name" style="text-align: left">Auto Email
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input <?php echo $employee->aemp_otml == "1" ? "checked" : "" ?> id="name"
                                                       class="col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="auto_email" type="checkbox" style="height:25px;width:25px;"
                                                >


                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="name" style="text-align: left">Live Location
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">


                                                <input checked id="location_on"
                                                       class="form-control"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="location_on" type="checkbox"
                                                       style="height:25px;width:25px;">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="name" style="text-align: left">Sales Person
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input <?php echo $employee->aemp_issl == "1" ? "checked" : "" ?> id="aemp_issl"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="aemp_issl" type="checkbox"
                                                       style="height:25px;width:25px;">

                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <label class="control-label col-md-6 col-sm-6 col-xs-12" for="name" style="text-align: left">HRIS Sync
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">

                                                <input <?php echo $employee->aemp_asyn == "Y" ? "checked" : "" ?> id="aemp_asyn"
                                                       class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="aemp_asyn" type="checkbox"
                                                       style="height:25px;width:25px;">
                                            </div>
                                        </div>


                                    </div>


                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-success"> Save <i class="fa fa-check-circle" style="font-size: 15px;"></i> </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <div id="btn_employee"></div>
                    
                    
                    <div class="x_panel acmp_id">
                        <div class="x_title">
                            <center><strong> :::Company::: </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" class="form-horizontal form-label-left">
                            <form style="display:inline"
                                  action="<?php echo e(URL::to('add/empSlgp')); ?>"
                                  class="pull-xs-right5 card-link">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <select class="form-control" name="acmp_code" id="acmp_code"
                                                        onchange="getSlgp(this.value)" required>
                                                    <option value="">Select Company</option>
                                                    <?php $__currentLoopData = $acmp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($acmp->id); ?>"><?php echo e($acmp->acmp_code); ?>

                                                            - <?php echo e(ucfirst($acmp->acmp_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <input id="emp_id" name="emp_id" value="<?php echo e($employee->id); ?>" type="hidden">
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <select class="form-control" name="slgp_code" id="slgp_code"
                                                        onchange="getPriceList(this.value)">
                                                    <option value="">Select Group</option>
                                                    <?php $__currentLoopData = $slgp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slgp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($slgp->id); ?>"><?php echo e($slgp->slgp_code); ?>

                                                            - <?php echo e(ucfirst($slgp->slgp_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <select class="form-control" name="plmt_code" id="plmt_code" onchange="getExtraPriceList(this.value)">
                                                    <option value="">Select Price List</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:7px; margin-bottom: 7px;">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <select class="form-control" name="zone_code" id="zone_code"
                                                        >
                                                    <option value="">Select Zone</option>
                                                    <?php $__currentLoopData = $zone_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($zone->id); ?>"><?php echo e($zone->zone_code); ?>

                                                            - <?php echo e(ucfirst($zone->zone_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <input id="dlrm_idx" class="form-control col-md-7 col-xs-12"
                                                       name="dlrm_idx" value=""
                                                       placeholder="Enter Dealer Code" required="required" type="text"
                                                       step="any">

                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-12" id="div_extra_price_list">
                                                <input id="extra_price_list" class="form-control col-md-7 col-xs-12"
                                                       name="extra_price_list" value=""
                                                       placeholder="Enter Extra Price List Code" required="required" type="text">

                                            </div>

                                            <div class="col-md-1 col-sm-1 col-xs-12">
                                                <button id="btn_group" class="btn btn-success" type="button"
                                                        onclick='addSlgp()'>Save <i class="fa fa-check-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="x_panel acmp_id">
                        <div class="x_content" class="form-horizontal form-label-left">

                            <table class="table table-striped table-bordered"
                                   data-page-length='25'>
                                <thead>
                                <tr style="background-color: #2b4570; color: white;">

                                    <th> Company Name</th>
                                    <th> Company Code</th>
                                    <th> Action</th>
                                </tr>
                                </thead>
                                <tbody id="acmp_list_cont">
                                <?php $__currentLoopData = $companyMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $companyMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php echo e($companyMapping1->acmp_name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($companyMapping1->acmp_code); ?>

                                        </td>
                                        <td>
                                            <button id="<?php echo e($companyMapping1->id); ?>" class="btn btn-danger btn-xs"
                                               onclick="deleteCompany(this)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="x_panel acmp_id">
                        <div class="x_content" class="form-horizontal form-label-left">
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
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="slgp_list_cont">
                                <?php $__currentLoopData = $salesGroupMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesGroupMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php echo e($salesGroupMapping1->slgp_name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($salesGroupMapping1->slgp_code); ?>

                                        </td>
                                        <td>
                                            <?php echo e($salesGroupMapping1->plmt_name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($salesGroupMapping1->plmt_code); ?>

                                        </td>
                                        <td>
                                            <?php echo e($salesGroupMapping1->zone_name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($salesGroupMapping1->zone_code); ?>

                                        </td>
                                        <td>
                                            <button id="<?php echo e($salesGroupMapping1->id); ?>" class="btn btn-danger btn-xs"
                                                    onclick="deleteSlgp(this)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="x_panel acmp_id">
                        <div class="x_content" class="form-horizontal form-label-left">
                            <table id="data_table" class="table table-striped table-bordered"
                                   data-page-length='25'>
                                <thead>
                                <tr style="background-color: #2b4570; color: white;">
                                    <th>Depot Name</th>
                                    <th>Depot Code</th>
                                    <th>Company Name</th>
                                    <th>Company Code</th>
                                    <th>Base Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="dlrm_list_cont">
                                <?php $__currentLoopData = $depotMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php echo e($depotMapping1->dlrm_name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($depotMapping1->dlrm_code); ?>

                                        </td>
                                        <td>
                                            <?php echo e($depotMapping1->acmp_name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($depotMapping1->acmp_code); ?>

                                        </td>
                                        <td>
                                            <?php echo e($depotMapping1->base_name); ?>

                                        </td>
                                        <td>
                                            <button type="button" id="<?php echo e($depotMapping1->id); ?>" class="btn btn-danger btn-xs"
                                               onclick="deleteEmpDlr(this)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    

                    

                    <div class="x_panel srth_mapping">
                        <div class="x_title">
                            <center><strong> :::SR Thana Mapping::: </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" class="form-horizontal form-label-left">
                            <div class="row">
                                <form style="display:inline" method="post"
                                      action="<?php echo e(URL::to('add/addEmpThanaMapping/'.$employee->id)); ?>"
                                      class="pull-xs-right5 card-link">
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('POST')); ?>

                                    <div class="item form-group">
                                        <label class="control-label col-md-1 col-sm-1 col-xs-12" for="name">District <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <select class="form-control" name="District" id="dist_id" onchange="getThana(this.value)">
                                                <option value="">Select District</option>
                                                <?php $__currentLoopData = $dsct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dsct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($dsct->id); ?>"><?php echo e($dsct->dsct_code); ?>

                                                        - <?php echo e($dsct->dsct_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <label class="control-label col-md-1 col-sm-1 col-xs-12" for="name">Thana <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-3 col-sm-3 col-xs-12">
                                            <select class="form-control" name="than_id_mapping[]" id="than_id_mapping" required
                                                    multiple>
                                                <option value="">Select Thana</option>
                                                <?php $__currentLoopData = $than_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $than_list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($than_list->id); ?>"><?php echo e($than_list->than_code); ?>

                                                        - <?php echo e($than_list->than_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                            <button class="btn btn-success" type="button"
                                                    onclick="addSRThanaMapping()"> Save <i class="fa fa-check-circle"></i> </button>
                                        </div>
                                    </div>
                                </form>
                                <br/>
                            </div>
                        </div>
                    </div>
                    <div class="x_panel srth_mapping">
                        <div class="x_content" class="form-horizontal form-label-left">
                            <div class="row">
                                <form style="display:inline" method="post"
                                      action="#"
                                      class="pull-xs-right5 card-link">
                                    <button class="btn btn-danger" type="button" onclick="deleteSRThanaMapping()">Delete <i class="fa fa-trash"></i> <span
                                                class="fa fa-danger"></span></button>
                                    <table id="data_table" class="table table-striped table-bordered"
                                           data-page-length='25'>
                                        <thead>
                                        <tr style="background-color: #2b4570; color: white;">
                                            <th><input type="checkbox" id="group_all">Action</th>
                                            <th>User Code</th>
                                            <th>User Name</th>
                                            <th>Thana Code</th>
                                            <th>Thana Name</th>

                                        </tr>
                                        </thead>
                                        <tbody id="sr_thana_mapping_list_cont">
                                        <?php $__currentLoopData = $emp_than_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp_than_list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="srThanaID sub_chk"  name="srThanaId" id="srThanaId"
                                                           value="<?php echo e($emp_than_list->id); ?>"/>

                                                </td>
                                                <td>
                                                    <?php echo e($emp_than_list->aemp_usnm); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($emp_than_list->aemp_name); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($emp_than_list->than_code); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($emp_than_list->than_name); ?>

                                                </td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    

                    

                    <div class="x_panel rpln_id">
                        <div class="x_title">
                            <strong style="text-align: left"> Route Plan </strong>
                            <span style="float: right;"> <button class="btn btn-success" type="button" onclick="addEmpRoutePlan()">Save <i class="fa fa-check-circle"></i> </button> </span>
                            <span style="float: right;"> <button class="btn btn-warning" type="button" onclick="addEmpRouteRow()">Add Row <i class="fa fa-plus-circle"></i> </button> </span>
                            <span style="float: right;"> <button class="btn btn-primary" type="button" onclick="pageRefresh()">Refresh <i class="fa fa-refresh"></i> </button> </span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" class="form-horizontal form-label-left">
                            <div class="row">
                                <form style="display:inline" id="formData"
                                      action="<?php echo e(URL::to('add/empRoutePlan/'.$employee->id)); ?>"
                                      
                                      class="pull-xs-right5 card-link" method="POST">
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('POST')); ?>


                                    <table class="table table-bordered table-striped projects" id="td_rpln">
                                        <tbody>
                                        <?php
                                            $count_r = 1;
                                            $temp = 0;
                                        ?>
                                        <?php $__currentLoopData = $routePlanMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routePlanMapping13): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $route_day = $routePlanMapping13->rpln_day;

                                                $r_id = "day_name".$count_r;
                                                $count_r = $count_r+1;

                                            $temp = 1;
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="required">Add Route</span>

                                                </td>
                                                <td>
                                                    <select class="form-control" name="day_name" id="<?php echo $r_id; ?>"
                                                            required>
                                                        <option value="">Select</option>
                                                        <option value="Saturday" <?php if($route_day == "Saturday"){?> selected <?php } ?>>Saturday</option>
                                                        <option value="Sunday" <?php if($route_day == "Sunday"){?> selected <?php } ?>>Sunday</option>
                                                        <option value="Monday" <?php if($route_day == "Monday"){?> selected <?php } ?>>Monday</option>
                                                        <option value="Tuesday" <?php if($route_day == "Tuesday"){?> selected <?php } ?>>Tuesday</option>
                                                        <option value="Wednesday" <?php if($route_day == "Wednesday"){?> selected <?php } ?>>Wednesday</option>
                                                        <option value="Thursday" <?php if($route_day == "Thursday"){?> selected <?php } ?>>Thursday</option>
                                                        <option value="Friday" <?php if($route_day == "Friday"){?> selected <?php } ?>>Friday</option>

                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                           value="<?php echo e($routePlanMapping13->rout_code); ?>">
                                                </td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if ($temp == 1){ ?>
                                        <input type="hidden" id="rpln_row_count" value="<?php echo $count_r; ?>" />
                                        <?php } ?>
                                        <?php if ($temp == '0'){
                                        $count_r = 8;
                                        ?>
                                        <tr>
                                            <td>

                                                <span class="required">Add Route</span>
                                                <input type="hidden" id="rpln_row_count" value="<?php echo $count_r; ?>" />
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name1"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday" selected >Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name2"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday" selected>Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name3"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday" selected>Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>

                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name4"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday" selected>Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>

                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name5"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday" selected>Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name6"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday" selected>Thursday</option>
                                                    <option value="Friday">Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>

                                                <span class="required">Add Route</span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="day_name" id="day_name7"
                                                        required>
                                                    <option value="">Select</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday" selected>Friday</option>

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="rout_code[]" id="rout_code"
                                                       value="<?php echo e(old('rout_code')); ?>">
                                            </td>
                                        </tr>
                                        <?php } ?>



                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="x_panel rpln_id">
                        <div class="x_content" class="form-horizontal form-label-left">
                            <div class="row">
                                <form style="display:inline" method="post"
                                      action="#"
                                      class="pull-xs-right5 card-link">
                                    <button class="btn btn-danger" type="button" onclick="deleteEmpRoutePlan2()">Delete <i class="fa fa-trash"></i> </button>
                                    <table id="data_table" class="table table-bordered"
                                           data-page-length='25'>
                                        <thead>
                                        <tr style="background-color: #2b4570; color: white;">
                                            <th><input type="checkbox" id="select_all"/></th>
                                            <th>Day</th>
                                            <th>Route Name</th>
                                            <th>Route Code</th>
                                            <th>Base Name</th>

                                        </tr>
                                        </thead>
                                        <tbody id="route_list_cont">
                                        <?php $__currentLoopData = $routePlanMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routePlanMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="checkbox rpln_id" name="rpln_id" id="rpln_id"
                                                           value="<?php echo e($routePlanMapping1->rpln_id); ?>"/>
                                                </td>
                                                <td>
                                                    <?php echo e($routePlanMapping1->rpln_day); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($routePlanMapping1->rout_name); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($routePlanMapping1->rout_code); ?>

                                                </td>
                                                <td>
                                                    <?php echo e($routePlanMapping1->base_name); ?>

                                                </td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    

                    
                    <div class="x_panel zgsm_id">
                        <div class="x_title">
                            <center><strong> :::Zone Group Supervisor Mapping::: </strong></center>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" class="form-horizontal form-label-left">
                            <form style="display:inline" method="post"
                                  action="<?php echo e(URL::to('add/empZoneGroupMapping/'.$employee->id)); ?>"
                                  class="pull-xs-right5 card-link">
                                <?php echo e(csrf_field()); ?>

                                <?php echo e(method_field('POST')); ?>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="item form-group">
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <select class="form-control" name="acmp_code_zg" id="acmp_code_zg"
                                                        onchange="getSlgp_zg(this.value)" required>
                                                    <option value="">Select Company</option>
                                                    <?php $__currentLoopData = $acmp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($acmp->id); ?>"><?php echo e($acmp->acmp_code); ?>

                                                            - <?php echo e(ucfirst($acmp->acmp_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <select class="form-control" name="slgp_code_zg" id="slgp_code_zg" required>
                                                    <option value="">Select Group</option>
                                                    <?php $__currentLoopData = $slgp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slgp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($slgp->id); ?>"><?php echo e($slgp->slgp_code); ?>

                                                            - <?php echo e(ucfirst($slgp->slgp_name)); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <select class="form-control" name="zone_code" id="zone_codsde" required>
                                                    <option value="">Select Zone</option>
                                                    <?php $__currentLoopData = $zoneGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($zoneg->id); ?>"><?php echo e($zoneg->zone_code); ?>

                                                            - <?php echo e($zoneg->zone_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <button class="btn btn-success" type="button"
                                                        onclick="addZoneGroupMapping()"> Add <i class="fa fa-check-circle"></i> </button>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>
                    <div class="x_panel zgsm_id">
                        <div class="x_content" class="form-horizontal form-label-left">
                            <div class="row">
                                <table id="data_table" class="table table-striped table-bordered"
                                       data-page-length='25'>
                                    <thead>
                                    <tr style="background-color: #2b4570; color: white;">
                                        <th>Group Name</th>
                                        <th>Group Code</th>
                                        <th>Zone Name</th>
                                        <th>Zone Code</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="zone_group_mapping_list_cont">
                                    <?php $__currentLoopData = $zoneGroupMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneGroupMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php echo e($zoneGroupMapping1->slgp_name); ?>

                                            </td>
                                            <td>
                                                <?php echo e($zoneGroupMapping1->slgp_code); ?>

                                            </td>
                                            <td>
                                                <?php echo e($zoneGroupMapping1->zone_name); ?>

                                            </td>
                                            <td>
                                                <?php echo e($zoneGroupMapping1->zone_code); ?>

                                            </td>
                                            <td>
                                                <a href="#" id="<?php echo e($zoneGroupMapping1->id); ?>" class="btn btn-danger btn-xs"
                                                   onclick="deleteZoneGroupMapping(this)">Delete</a>
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
    <script>
        $(document).ready(function () {

            const amng_id = '<?php echo e($employee->amng_id); ?>';
            if (amng_id !== '') {
                $('#amng_id').val(amng_id);
            }
            $("#amng_id").select2({width: 'resolve'});
            $("#day_name").select2({width: 'resolve'});
            $("#role_id").select2({width: 'resolve'});
            $("#cont_id").select2({width: 'resolve'});
            $("#master_role_id").select2({width: 'resolve'});
            $("#slgp_codessd").select2({width: 'resolve'});
            $("#zone_codsde").select2({width: 'resolve'});
            $('#acmp_code').select2();
            $('#acmp_code1').select2();
            $('#slgp_code').select2();
            $('#zone_code').select2();
            $('#plmt_code').select2();
            //$('#dlrm_id').select2();
            $('#than_id_mapping').select2();
            $('#dist_id').select2();
            $('#slgp_code_zg').select2();
            $('#acmp_code_zg').select2();
            $('#expr_date').datepicker({
                    dateFormat: 'yy-mm-dd',
                    minDate: '0d',               
                    autoclose: 1,
                    showOnFocus: true
            });
            hide();
            $("#profile_id").show();
        });
        $('#group_all').on('click', function(e) {
            if($(this).is(':checked',true)){
                $(".sub_chk").prop('checked', true);
            } else {
                $(".sub_chk").prop('checked',false);
        }
        });

        function hide(){
            $('.acmp_id').hide();
            $('#slgp_id').hide();
            $('#dlrm_id').hide();
            $('#profile_id').hide();
            $('.rpln_id').hide();
            $('.zgsm_id').hide();
            $('.srth_mapping').hide();
            $('#div_extra_price_list').hide();
        }

        function showCompany(){
            hide();
            $(".acmp_id").show();
        }

        function showSRThana() {
            hide();
            //alert("test");
            $(".srth_mapping").show();
        }

        function showRpln(){
            hide();
            $(".rpln_id").show();
        }

        function showZgsm(){
            hide();
            $(".zgsm_id").show();
        }
        function showProfile() {
            hide();
            $("#profile_id").show();

        }


        function showExisting(zone_id) {
            var slgp_id = $('#slgp_codessd').val();
            var _token = $("#_token").val();

            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/json/load/existingEmployee",
                data: {
                    slgp_id: slgp_id,
                    zone_id: zone_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    var id="";
                    var aemp_code="";
                    var aemp_name="";
                    var message ="";
                    $('#ajax_load').css("display", "none");

                    for (var i = 0; i < data.length; i++) {
                        aemp_code = data[i]['aemp_usnm'];
                        aemp_name = data[i]['aemp_name'];
                        id = data[i]['id'];


                    }
                    if (id!=""){
                        message = "User exists in the same group and zone. User Code - " + aemp_code + ", User Name - " + aemp_name;
                        alert(message);
                        $('#tl_zmzg').attr('disabled','disabled');
                    }else{
                        $('#tl_zmzg').removeAttr('disabled');
                    }

                }

            });
        }

        function getSlgp(acmp_id){
            $.ajax({
                type:"get",
                url: "<?php echo e(URL::to('/')); ?>/get/empSlgp/"+acmp_id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#slgp_code').empty();
                    var html='<option value="">Select Group</option>';
                    for(var i=0;i<data.length;i++){
                        html+='<option value="'+data[i]['id']+'">'+data[i]['slgp_code'] + " - " + data[i]['slgp_name']+'</option>';
                    }
                    $('#slgp_code').append(html);

                }
            });
        }

        function getPriceList(slgp_code){
            $('#plmt_code').empty();
            $.ajax({
                type:"get",
                url: "<?php echo e(URL::to('/')); ?>/get/empSlgpPriceList/"+slgp_code,
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#plmt_code').empty();
                    //var html='<option value="">Select price</option>';
                    var html='';
                    for(var i=0;i<data.length;i++){
                        html+='<option value="'+data[i]['id']+'">'+data[i]['code'] + " - " +data[i]['name']+'</option>';
                    }
                    html+='<option value="extra_price_list"> Extra Price List </option>';
                    $('#plmt_code').append(html);

                }
            });
        }
        function getExtraPriceList(id) {
            if (id =="extra_price_list"){
                $('#div_extra_price_list').show();
            }else{
                $('#div_extra_price_list').hide();
            }
        }

        function getDlrmList() {
            //clearDate();  slgp_code zone_code
            var slgp_code = $("#slgp_code").val();
            var zone_code = $("#zone_code").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/load/get/empDealerListget",
                data: {
                    slgp_code: slgp_code,
                    zone_code: zone_code,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#dlrm_id").empty();

                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Dealer</option>';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">' + data[i].dlrm_code + " - " + data[i].dlrm_name + '</option>';
                    }
                    $("#dlrm_id").append(html);

                }
            });
        }
        function getThana(dist_id) {
            //clearDate();  slgp_code zone_code
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/json/get/market_open/thana_list",
                data: {
                    district_id: dist_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#than_id_mapping").empty();

                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select District</option>';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">' + data[i].than_code + " - " + data[i].than_name + '</option>';
                    }
                    $("#than_id_mapping").append(html);

                }
            });
        }

        function getSlgp_zg(acmp_id){
            $.ajax({
                type:"get",
                url: "<?php echo e(URL::to('/')); ?>/get/empSlgp/"+acmp_id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#slgp_code_zg').empty();
                    var html='<option value="">Select Group</option>';
                    for(var i=0;i<data.length;i++){
                        html+='<option value="'+data[i]['id']+'">'+data[i]['slgp_code'] + " - " + data[i]['slgp_name']+'</option>';
                    }
                    $('#slgp_code_zg').append(html);

                }
            });
        }

        // Group
        function addSlgp(){
            var id=$('#emp_id').val();
            var emp_usnm=$('#usnm').val();
            var acmp_code=$('#acmp_code').val();
            var slgp_code=$('#slgp_code').val();
            var plmt_code=$('#plmt_code').val();
            var zone_code=$('#zone_code').val();
            var dlrm_id=$('#dlrm_idx').val();
            var extra_price_list=$('#extra_price_list').val();
            var _token = $("#_token").val();
            if(emp_usnm==''){
                return confirm('Please enter staff id');
            }
            $.ajax({
                type:'POST',
                url: "<?php echo e(URL::to('/')); ?>/add/empSlgp",
                data: {
                    acmp_code: acmp_code,
                    emp_usnm: emp_usnm,
                    emp_id: id,
                    slgp_code:slgp_code,
                    extra_price_list:extra_price_list,
                    plmt_code:plmt_code,
                    zone_code:zone_code,
                    dlrm_id:dlrm_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#acmp_list_cont').empty();
                    $('#slgp_list_cont').empty();
                    $('#dlrm_list_cont').empty();
                    var html1="";
                    var html2="";
                    var html3="";
                    console.log(data);

                    for (var i = 0; i < data.companyMapping.length; i++) {
                        html1 += '<tr>' +
                            '<td>' + data.companyMapping[i]['acmp_name'] + '</td>' +
                            '<td>' + data.companyMapping[i]['acmp_code'] + '</td>' +
                            '<td><button id="'+data.companyMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteCompany(this)">'+"Delete"+ '</button></td>' +
                            '</tr>';
                    }

                    for (var i = 0; i < data.salesGroupMapping.length; i++) {
                        html2 += '<tr>' +
                            '<td>' + data.salesGroupMapping[i]['slgp_name'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['slgp_code'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['plmt_name'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['plmt_code'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['zone_name'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['zone_code'] + '</td>' +
                            '<td><button id="'+data.salesGroupMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteSlgp(this)">'+"Delete"+ '</button></td>' +
                            '</tr>';
                    }
                    for(var i=0;i<data.depotMapping.length;i++){
                        html3+='<tr>'+
                            '<td>'+data.depotMapping[i]['dlrm_name']+'</td>'+
                            '<td>'+data.depotMapping[i]['dlrm_code']+'</td>'+
                            '<td>'+data.depotMapping[i]['acmp_name']+'</td>'+
                            '<td>'+data.depotMapping[i]['acmp_code']+'</td>'+
                            '<td>'+data.depotMapping[i]['base_name']+'</td>'+
                            '<td><button id="'+data.depotMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteEmpDlr(this)">'+"Delete"+ '</button></td>' +
                            '</tr>';
                    }
                    if(data.message==0){
                        alert("Successfully Added!");
                    }
                    else if(data.message==1){
                        alert("Successfully Added!");
                    }
                    else if(data.message==3){
                        alert("You don't have permission!");
                    }
                    //$('#slgp_list_cont').append(html);
                    $('#acmp_list_cont').append(html1);
                    $('#slgp_list_cont').append(html2);
                    $('#dlrm_list_cont').append(html3);
                },error:function(error){
                    console.log(error);
                }
            });
        }

        function deleteCompany(v){

            var id=$(v).attr('id');
            var emp_id=$('#emp_id').val();
            var _token = $("#_token").val();
            $.ajax({
                type:'post',
                url:"<?php echo e(URL::to('/')); ?>/delete/empCompany/new",
                data:{
                    emp_id:emp_id,
                    id:id,
                    _token:_token
                },
                dataType:"json",
                success:function(data){
                    console.log(data);
                    //alert(data);
                    $('#acmp_list_cont').empty();
                    var html='';
                    for(var i=0;i<data.companyMapping.length;i++){

                        html += '<tr>' +
                            '<td>' + data.companyMapping[i]['acmp_name'] + '</td>' +
                            '<td>' + data.companyMapping[i]['acmp_code'] + '</td>' +
                            '<td><button type="button" id="' + data.companyMapping[i]['id'] + '" class="btn btn-danger btn-xs" onclick="deleteCompany(this)">' + "Delete" + '</button></td>' +
                            '</tr>';
                    }
                    $('#acmp_list_cont').append(html);
                    if(data.message==0){
                        alert("SR Company Mapping Delete Successfully!!!");
                    }
                    else if(data.message==1){
                        alert("Something went wrong!!!");
                    }

                },error:function(data){

                }
            });
        }

        function deleteSlgp(v){
            var id=$(v).attr('id');
            var emp_id=$('#emp_id').val();
            var _token = $("#_token").val();

            $.ajax({
                type: 'post',
                data: {
                    emp_usnm: emp_id,
                    id: id,
                    _token: _token
                },
                url: "<?php echo e(URL::to('/')); ?>/delete/empSlgp",
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#slgp_list_cont').empty();
                    //$('#ajax_load').css("display", "none");
                    var html = "";
                    console.log(data);
                    for (var i = 0; i < data.salesGroupMapping.length; i++) {
                        html += '<tr>' +
                            '<td>' + data.salesGroupMapping[i]['slgp_name'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['slgp_code'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['plmt_name'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['plmt_code'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['zone_name'] + '</td>' +
                            '<td>' + data.salesGroupMapping[i]['zone_code'] + '</td>' +
                            '<td><button type="button" id="' + data.salesGroupMapping[i]['id'] + '" class="btn btn-danger btn-xs" onclick="deleteSlgp(this)">' + "Delete" + '</button></td>' +
                            '</tr>';
                    }
                    $('#slgp_list_cont').append(html);

                }, error: function (error) {
                    console.log(error);
                }
            });
            alert("Sales Group Removed!");

        }

        function deleteEmpDlr(v) {
            var id = $(v).attr('id');
            var eid = $('#emp_id').val();
            $.ajax({
                type: 'get',
                url: "<?php echo e(URL::to('/')); ?>/delete/empDlr/" + id + "/" + eid,
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#dlrm_list_cont').empty();
                    var html = '';
                    for (var i = 0; i < data.depotMapping.length; i++) {
                        html += '<tr>' +
                            '<td>' + data.depotMapping[i]['dlrm_name'] + '</td>' +
                            '<td>' + data.depotMapping[i]['dlrm_code'] + '</td>' +
                            '<td>' + data.depotMapping[i]['acmp_name'] + '</td>' +
                            '<td>' + data.depotMapping[i]['acmp_code'] + '</td>' +
                            '<td>' + data.depotMapping[i]['base_name'] + '</td>' +
                            '<td><button type="button" id="' + data.depotMapping[i]['id'] + '" class="btn btn-danger btn-xs" onclick="deleteEmpDlr(this)">' + "Delete" + '</button></td>' +
                            '</tr>';
                    }

                    $('#dlrm_list_cont').append(html);

                }, error: function (error) {
                    console.log(error);
                }
            });


        }

        // sr thana mapping function
        function addSRThanaMapping(){
            var thana = $('#than_id_mapping').val();
            var than_id_mapping=JSON.stringify(thana);
            ///alert(than_id_mapping);
            var id=$('#emp_id').val();
            var _token=$('#_token').val();
            $.ajax({
                type:'post',
                url:"<?php echo e(URL::to('/')); ?>/add/addEmpThanaMapping",
                data:{
                    than_id_mapping:than_id_mapping,
                    id:id,
                    _token:_token
                },
                dataType:"json",
                success:function(data){
                    console.log(data);
                    //$('#than_id_mapping option:selected').removeAttr('selected');
                    $('#sr_thana_mapping_list_cont').empty();
                    var html='';
                    for(var i=0;i<data.empThanaList.length;i++){
                        html+='<tr>'+
                            '<td><input type="checkbox" class="srThanaID" name="srThanaId" id="srThanaId" value="' + data.empThanaList[i]['id']+ '" /></td>' +
                            '<td>'+data.empThanaList[i]['aemp_usnm']+'</td>'+
                            '<td>'+data.empThanaList[i]['aemp_name']+'</td>'+
                            '<td>'+data.empThanaList[i]['than_code']+'</td>'+
                            '<td>'+data.empThanaList[i]['than_name']+'</td>'+
                            '</tr>';
                    }
                    $('#sr_thana_mapping_list_cont').append(html);
                    if(data.message==0){
                        alert("SR Thana Mapping Added Successfully!");
                        //swal("Success!", "SR Thana Mapping Added Successfully!", "success");
                    }
                    else if(data.message==1){
                        alert("Somethin went wrong! Please try again");
                        //swal("Warning!", "Somethin went wrong! Please try again", "warning");
                    }

                },error:function(data){

                }
            });
        }
        function deleteSRThanaMapping(){

            var SlectedList = new Array();
            $("input.srThanaID:checked").each(function() {
                SlectedList.push($(this).val());
            });

            var than_id_mapping=JSON.stringify(SlectedList);

            var id=$('#emp_id').val();
            var _token=$('#_token').val();
            $.ajax({
                type:'post',
                url:"<?php echo e(URL::to('/')); ?>/delete/deleteEmpThanaMapping",
                data:{
                    than_id_mapping:than_id_mapping,
                    id:id,
                    _token:_token
                },
                dataType:"json",
                success:function(data){
                    $('#sr_thana_mapping_list_cont').empty();
                    var html='';
                    for(var i=0;i<data.empThanaList.length;i++){
                        html+='<tr>'+
                            '<td><input type="checkbox" class="srThanaID" name="srThanaId" id="srThanaId" value="' + data.empThanaList[i]['id']+ '" /></td>' +
                            '<td>'+data.empThanaList[i]['aemp_usnm']+'</td>'+
                            '<td>'+data.empThanaList[i]['aemp_name']+'</td>'+
                            '<td>'+data.empThanaList[i]['than_code']+'</td>'+
                            '<td>'+data.empThanaList[i]['than_name']+'</td>'+
                            '</tr>';
                    }
                    $('#sr_thana_mapping_list_cont').append(html);
                    if(data.message==0){
                        alert("SR Thana Mapping Delete Successfully!");
                    }
                    else if(data.message==1){
                        alert("Somethin went wrong! Please try again");
                    }

                },error:function(data){

                }
            });
        }

        // sr route plan function start
        function addEmpRoutePlan(){

            var rplnSlectedDay = new Array();

            var count=$("#rpln_row_count").val();
            var id_name = "";

            for (var j= 1; j< count; j++){
                id_name = "day_name"+j;
                rplnSlectedDay.push(document.getElementById(id_name).value);
            }
            console.log(rplnSlectedDay);
            var day_name=JSON.stringify(rplnSlectedDay);
            var rout_codes = $("input[name='rout_code[]']")
                .map(function(){return $(this).val();}).get();
            var rout_code=JSON.stringify(rout_codes);

            var id=$('#emp_id').val();
            var _token=$('#_token').val();

            $.ajax({
                type:'post',
                url:"<?php echo e(URL::to('/')); ?>/add/empRoutePlan/",
                data:{
                    day_name:day_name,
                    rout_code:rout_code,
                    id:id,
                    _token:_token
                },

                dataType:"json",
                success:function(data){

                    $('#route_list_cont').empty();
                    var html='';
                    for(var i=0;i<data.routePlanMapping.length;i++){
                        html+='<tr>'+
                            '<td><input type="checkbox" class="rpln_id" name="rpln_id" id="rpln_id" value="' + data.routePlanMapping[i]['rpln_id']+ '" /></td>' +
                            '<td>'+data.routePlanMapping[i]['rpln_day']+'</td>'+
                            '<td>'+data.routePlanMapping[i]['rout_name']+'</td>'+
                            '<td>'+data.routePlanMapping[i]['rout_code']+'</td>'+
                            '<td>'+data.routePlanMapping[i]['base_name']+'</td>'+
                            '</tr>';
                    }
                    $('#route_list_cont').append(html);
                    if(data.message==0){
                        alert("Route Plan Added Successfully!");
                    }
                    else if(data.message==1){
                        alert("Code doesn't Match!");
                    }

                },error:function(data){

                }
            });

        }
        function addEmpRouteRow() {
            var count_id = $('#rpln_row_count').val();
            var next_id = parseInt(count_id) + 1;
            var next_row_id ="day_name"+count_id;
            var rplnRow = "<tr>" +
                "<td><span class='required'>Add Route</span></td>" +
                "<td>" +
                '<select class="form-control" name="day_name" id="' + next_row_id + '" required>' +
                '<option value="">Select</option>' +
                '<option value="Saturday">Saturday</option>' +
                '<option value="Sunday">Sunday</option>' +
                '<option value="Monday">Monday</option>' +
                '<option value="Tuesday" >Tuesday</option>' +
                '<option value="Wednesday">Wednesday</option>' +
                '<option value="Thursday">Thursday</option>' +
                '<option value="Friday" >Friday</option>' +

                '</select>' +
                '</td>' +
                '<td>' +
                '<input type="text" class="form-control" name="rout_code[]" id="rout_code" value="">' +
                '</td>' +
                '</tr>';

            $("#td_rpln > tbody").append(rplnRow);
            $("#rpln_row_count").val(next_id);

            //alert(rplnRow);
        }

        function deleteEmpRoutePlan2(){

            var rplnSlectedList = new Array();
            $("input.rpln_id:checked").each(function() {
                rplnSlectedList.push($(this).val());
            });
            var count_id = $('#rpln_row_count').val();
            var rpln_id=JSON.stringify(rplnSlectedList);
            console.log(rpln_id);
            var id=$('#emp_id').val();
            var _token=$('#_token').val();
            $.ajax({
                type: 'post',
                url: "<?php echo e(URL::to('/')); ?>/delete/deleteRoutePlan/",
                data: {
                    rpln_id: rpln_id,
                    id: id,
                    _token: _token
                },
                dataType: "json",
                success: function (data) {

                    $('#route_list_cont').empty();
                    var html = '';
                    for (var i = 0; i < data.routePlanMapping.length; i++) {
                        html += '<tr>' +
                            '<td><input type="checkbox" class="rpln_id" name="rpln_id" id="rpln_id" value="' + data.routePlanMapping[i]['rpln_id'] + '" /></td>' +
                            '<td>' + data.routePlanMapping[i]['rpln_day'] + '</td>' +
                            '<td>' + data.routePlanMapping[i]['rout_name'] + '</td>' +
                            '<td>' + data.routePlanMapping[i]['rout_code'] + '</td>' +
                            '<td>' + data.routePlanMapping[i]['base_name'] + '</td>' +

                            '</tr>';
                    }
                    $('#route_list_cont').append(html);
                    $('input[type="text"]').val('');
                    $('#rpln_row_count').val(count_id);
                    /*$('#rout_code:input').each(function() {
                     $(this).val('');
                     });*/
                    if (data.message == 0) {
                        alert("Route Plan Removed Successfully!", {
                            icon: "success",
                        });

                    } else {
                        alert("Access Limited");
                    }

                }, error: function (error) {
                    console.log(error);
                }
            });
        }
        function pageRefresh(){
            location.reload();
        }
        //zone group mapping start
        function addZoneGroupMapping(){
            var slgp_code=$('#slgp_code_zg').val();
            var zone_code=$('#zone_codsde').val();
            var id=$('#emp_id').val();
            var _token=$('#_token').val();
            $.ajax({
                type:'post',
                url:"<?php echo e(URL::to('/')); ?>/add/empZoneGroupMapping/"+id,
                data:{
                    slgp_code:slgp_code,
                    zone_code:zone_code,
                    _token:_token
                },
                dataType:"json",
                success:function(data){
                    if(data.message !=-1){
                        $('#zone_group_mapping_list_cont').empty();
                        var html='';
                        for(var i=0;i<data.zoneGroupMapping.length;i++){
                            html+='<tr>'+
                                '<td>'+data.zoneGroupMapping[i]['slgp_name']+'</td>'+
                                '<td>'+data.zoneGroupMapping[i]['slgp_code']+'</td>'+
                                '<td>'+data.zoneGroupMapping[i]['zone_name']+'</td>'+
                                '<td>'+data.zoneGroupMapping[i]['zone_code']+'</td>'+
                                '<td><a href="#" id="'+data.zoneGroupMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteZoneGroupMapping(this)">'+"Delete"+ '</a></td>' +
                                '</tr>';
                        }
                        $('#zone_group_mapping_list_cont').append(html);
                        if(data.message==0){
                            alert("Zone group mapping added!");
                        }
                        else if(data.message==1){
                            alert("Access Limited!");
                        }
                    }else{
                        var dd = "";
                        dd = data.exists[0]['aemp_usnm'];
                        //alert(dd);
                        alert("Zone already associated " + dd + " with this employee!!!");
                        //console.log(data);
                    }
                },error:function(data){
                    console.log(data);
                    alert("Something went!");
                }
            });
        }
        function deleteZoneGroupMapping(v){
            var id=$(v).attr('id');
            var eid=$('#emp_id').val();

            $.ajax({
                type: 'get',
                url: "<?php echo e(URL::to('/')); ?>/delete/zoneGroupMapping/" + id + "/" + eid,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#zone_group_mapping_list_cont').empty();
                    var html = '';
                    for (var i = 0; i < data.zoneGroupMapping.length; i++) {
                        html += '<tr>' +
                            '<td>' + data.zoneGroupMapping[i]['slgp_name'] + '</td>' +
                            '<td>' + data.zoneGroupMapping[i]['slgp_code'] + '</td>' +
                            '<td>' + data.zoneGroupMapping[i]['zone_name'] + '</td>' +
                            '<td>' + data.zoneGroupMapping[i]['zone_code'] + '</td>' +
                            '<td><a href="#" id="' + data.zoneGroupMapping[i]['id'] + '" class="btn btn-danger btn-xs" onclick="deleteZoneGroupMapping(this)">' + "Delete" + '</a></td>' +
                            '</tr>';
                    }
                    $('#zone_group_mapping_list_cont').append(html);
                    if (data.message == 0) {
                        alert("Zone Group Mapping Removed!");
                    } else {
                        alert("Access Limited");
                    }

                }, error: function (error) {
                    console.log(error);
                }
            });

        }


        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                console.log(status);
                //change ".checkbox" checked status
            });
        });

        //zone group mapping end

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/edit.blade.php ENDPATH**/ ?>