
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
            <strong>Edit Employee </strong>
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
            <?php if($permission->wsmu_crat): ?>
                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/employee/create')); ?>">Add New</a>
                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/employee/employeeHrisUpload')); ?>">Add HRIS</a>
                <a class="btn btn-success btn-sm"
                   href="<?php echo e(URL::to('employee/employeeUpload')); ?>">Upload</a>

                <a class="btn btn-success btn-sm"
                   href="<?php echo e(URL::to('get/employee/routeSearch/view')); ?>">Search Route</a>
                <a class="btn btn-success btn-sm"
                   href="<?php echo e(URL::to('employee/get/routeLike/view')); ?>">Route Like</a>
            <?php endif; ?>
            <div class="col-md-1 col-sm-1 col-xs-3" id="back_btn_div" style="float:right;">
               <button class="btn btn-warning" id="back_btn" onclick="searchDivHideShow()">Back</button>
           </div>
           <div class="col-md-2 col-sm-2 col-xs-8" style="float:right;" id="aemp_usr_div">
               <select class="select2" name="aemp_usr" id="aemp_usr"
                       onchange="getEmpDetails(this.value)" style="width:90%;">                                        
               </select>
              
           </div>

           <div class="col-md-2 col-sm-2 col-xs-12 form-group pull-right top_search" id="employee_search_div">
                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
               <div class="input-group">
                   <input type="text" class="form-control" name="search_text" placeholder="Place staff  id to edit"
                          name="aemp_usnm" id="aemp_usnm" >
                   <span class="input-group-btn">
                     <button class="btn btn-default" type="submit" onclick="getEmployeeUsnm()" id="find_user">Find</button>
                   </span>

               </div>
           </div>  
        </div>
      <div class="x_content">
        <form class="form-horizontal form-label-left">
           <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
           <div class="item form-group" style="margin-top:25px;">
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="usnm">User ID <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="usnm" class="form-control col-md-7 col-xs-6 "
                          data-validate-length-range="6" data-validate-words="2" name="email"
                          value="<?php echo e($employee->aemp_usnm); ?>"
                          placeholder="Code" required="required" type="text" >
                    <input id="emp_id" value="<?php echo e($employee->id); ?>" type="hidden">
               </div>
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Full Name <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="name" class="form-control col-md-7 col-xs-12"
                          data-validate-length-range="6" data-validate-words="2" name="name"
                          value="<?php echo e($employee->aemp_name); ?>"
                          placeholder="Name" required="required" type="text">
               </div>
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Ln Name
                   </label>
                   <div class="col-md-2 col-sm-2 col-xs-6">
                       <input id="ln_name" class="form-control col-md-7 col-xs-12 "
                              name="ln_name"
                              value="<?php echo e($employee->aemp_onme); ?>"
                              placeholder="Ln Name" type="text">
                   </div>
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Email
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="address" class="form-control col-md-7 col-xs-12 "
                          name="address"
                          value="<?php echo e($employee->aemp_emal); ?>"
                          placeholder="Email" type="email">
               </div>
              
           </div>

           <div class="item form-group">
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Manger ID <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="manager_id" class="form-control col-md-7 col-xs-12 "
                          data-validate-length-range="6" data-validate-words="2" name="manager_id"
                          value="<?php echo e($employee->manager()->aemp_usnm); ?>"
                          placeholder="manager id" required="required" type="text">
               </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Line Manger ID
                   <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="line_manager_id" class="form-control col-md-7 col-xs-12 "
                          data-validate-length-range="6" data-validate-words="2"
                          name="line_manager_id" value="<?php echo e($employee->lineManager()->aemp_usnm); ?>"
                          placeholder="line manager id" required="required" type="text">
            
               </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Mobile
                   </label>
                   <div class="col-md-2 col-sm-2 col-xs-6">
                       <input id="mobile" class="form-control col-md-7 col-xs-12 "
                              name="mobile"
                              value="<?php echo e($employee->aemp_mob1); ?>"
                              placeholder="Mobile" type="text">
                   </div>
                   <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Email CC
                       <span
                               class="required">*</span>
                   </label>
                   <div class="col-md-2 col-sm-2 col-xs-6">
                       <input id="email_cc" class="form-control col-md-7 col-xs-12 "
                              data-validate-length-range="6" data-validate-words="2"
                              name="email_cc"
                              value="<?php echo e($employee->aemp_emcc); ?>"
                              placeholder="email1@exmple.com" type="text"
                              step="any">
                   </div>
            </div>
           <div class="item form-group">
              <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Allowed Distance
                  <span
                          class="required">*</span>
              </label>
              <div class="col-md-2 col-sm-2 col-xs-6">
                  <input id="allowed_distance" class="form-control col-md-7 col-xs-12 "
                         name="allowed_distance" value="<?php echo e($employee->aemp_aldt); ?>"
                         placeholder="Allowed Distance" required="required" type="number"
                         step="any">
              </div>
              <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Personal Credit
                   Limit
                   <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="aemp_crdt" class="form-control col-md-7 col-xs-12 "
                          name="aemp_crdt" value="<?php echo e($employee->aemp_crdt); ?>"
                          placeholder="" required="required" type="number"
                          step="any">
               </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Customer Code
                   <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="site_id" class="form-control col-md-7 col-xs-12 "
                          name="site_id" value="<?php echo e($site_code); ?>"
                          placeholder="" required="required" type="number"
                          step="any">
               </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name"> Profile
                   Image <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <input id="input_img" class="form-control col-md-7 col-xs-12 "
                          data-validate-length-range="6" data-validate-words="2" name="input_img"
                          placeholder="Image" type="file"
                          step="1">
               </div>
           </div>
           <div class="item form-group">
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Designation
                   <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                   <select class="form-control select2" name="role_id" id="role_id" required>
                       <option value="<?php echo e($employee->role()->id); ?>"><?php echo e($employee->role()->edsg_name); ?></option>
                       <?php $__currentLoopData = $userRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <option value="<?php echo e($role->id); ?>"><?php echo e(ucfirst($role->edsg_name)); ?></option>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   </select>
               </div>
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">Role <span
                               class="required">*</span>
                   </label>
                   <div class="col-md-2 col-sm-2 col-xs-6">
                      <select class="form-control select2" name="master_role_id" id="master_role_id" required>
                            <option value="<?php echo e($employee->masterRole()->id); ?>"><?php echo e($employee->masterRole()->role_name); ?></option>
                            <?php $__currentLoopData = $masterRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $masterRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($employee->masterRole()->id!=$masterRole->id): ?>
                                    <option value="<?php echo e($masterRole->id); ?>"><?php echo e(ucfirst($masterRole->role_name)); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                   </div>
               <label class="control-label col-md-1 col-sm-1 col-xs-6" for="name">App Menu Group
                   <span
                           class="required">*</span>
               </label>
               <div class="col-md-2 col-sm-2 col-xs-6">
                    <select class="form-control select2" name="amng_id" id="amng_id" required>
                      <?php $__currentLoopData = $appMenuGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appMenuGroup1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($appMenuGroup1->id ==$employee->amng_id): ?>
                            <option value="<?php echo e($appMenuGroup1->id); ?>"><?php echo e(ucfirst($appMenuGroup1->amng_name)); ?></option>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $appMenuGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appMenuGroup1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($appMenuGroup1->id !=$employee->amng_id): ?>
                            <option value="<?php echo e($appMenuGroup1->id); ?>"><?php echo e(ucfirst($appMenuGroup1->amng_name)); ?></option>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
               </div>
           </div>
           <?php if(Auth::user()->country()->module_type==2): ?>
            <div class="item form-group">
                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="name">Nationality<span
                            ></span>
                </label>
                <div class="col-md-2 col-sm-2 col-xs-12">
                    <select class="form-control select2" name="cont_id" id="cont_id" required>
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
                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="name">Visa No
                    <span
                            ></span>
                </label>
                <div class="col-md-2 col-sm-2 col-xs-12">
                    <input id="visa_no" class="form-control col-md-7 col-xs-12" name="visa_no"
                            value="<?php echo e($ecmp?$ecmp->visa_no:''); ?>" type="text"
                            step="1">
                </div>
                <label class="control-label col-md-1 col-sm-1 col-xs-12" for="name">Expiry Date
                    <span
                            ></span>
                </label>
                <div class="col-md-2 col-sm-2 col-xs-12">
                    <input id="expr_date" class="form-control col-md-7 col-xs-12" name="expr_date"
                            value="<?php echo e($ecmp?$ecmp->expr_date:''); ?>"
                            step="1">
                </div>
            </div>
            <?php endif; ?>
           <div class="item form-group">
                <label class="control-label col-md-1 col-sm-1 col-xs-3" for="otml">Auto Email 
              </label>
               <div class="col-md-1 col-sm-1 col-xs-3">
                   <input <?php echo $employee->aemp_otml == "1" ? "checked" : "" ?> id="otml"
                          class="form-control"
                          name="auto_email" type="checkbox" 
                   style="height:25px;width:25px;">
               </div>
         
               <label class="control-label col-md-1 col-sm-1 col-xs-3" for="location_on">Live Location
               </label>
               <div class="col-md-1 col-sm-1 col-xs-3">
                   <input <?php echo $employee->aemp_lonl == "1" ? "checked" : "" ?> id="location_on"
                          class="form-control"
                          data-validate-length-range="6" data-validate-words="2"
                          name="location_on" type="checkbox"
                   style="height:25px;width:25px;">
               </div>
               <label class="control-label col-md-1 col-sm-1 col-xs-3" for="aemp_issl">Is Sales Person
               </label>
               <div class="col-md-1 col-sm-1 col-xs-3">
                   <input <?php echo $employee->aemp_issl == "1" ? "checked" : "" ?> id="aemp_issl"
                          class="form-control col-md-7 col-xs-12"
                          data-validate-length-range="6" data-validate-words="2"
                          name="aemp_issl" type="checkbox"
                   style="height:25px;width:25px;">
               </div>
                    <label class="control-label col-md-1  col-sm-1 col-xs-3" for="aemp_asyn">Is HRIS Sync
               </label>
               <div class="col-md-1 col-sm-1 col-xs-6">
                   <input <?php echo $employee->aemp_asyn == "Y" ? "checked" : "" ?> id="aemp_asyn"
                          class="form-control col-md-7 col-xs-12"
                          data-validate-length-range="6" data-validate-words="2"
                          name="aemp_asyn" type="checkbox"
                   style="height:25px;width:25px;">
               </div>
               
               <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2">
                  <button type="button" class="btn btn-dark btn-block" onclick="updateEmpInfo()"> Update Employee Info</button>
              </div>
               
           </div>
          <!-- <div class="ln_solid"></div>
          <div class="form-group">
              <div class="col-md-6 col-md-offset-3">
                  <button type="submit" class="btn btn-success"> Submit</button>
              </div>
          </div> -->
       </form>
      </div>
    </div>
</div>
<div class="col-md-12 col-sm-12 col-xs-12 rp_type_div">
          <div class="col-xs-12 col-sm-5 col-md-5">
              <!-- <button class="btn btn-primary"   onclick="showCompany()">Company</button>
              <button class="btn btn-primary"   onclick="showGroup()">Group</button>
              <button class="btn btn-primary"   onclick="showDlrm()">Dealer</button>
              <button class="btn btn-primary"   onclick="showRpln()">Route Plan</button>
              <button class="btn btn-primary"   onclick="showZgsm()">Zone Group Supervisor Mapping</button> -->
              <div id="exTab1" class="container">	
                    <ul  class="nav nav-pills">
                            <li>
                                <a  href="#1a" data-toggle="tab"  onclick="showCompany()">Company </a>
                            </li>
                            <li><a href="#2a" data-toggle="tab"  onclick="showGroup()"> Group</a>
                            </li>
                            <li><a href="#3a" data-toggle="tab" onclick="showDlrm()"> Dealer</a>
                            </li>
                            <li><a href="#4a" data-toggle="tab" onclick="showRpln()" >
                                Route Plan</a>
                                <li><a href="#4a" data-toggle="tab"  onclick="showZgsm()" >Zone Group Mapping</a>
                            </li>
                    </ul>

                </div> 
          </div>
          <div class="col-xs-12 col-md-5 col-sm-5 col-md-offset-2 col-sm-offset-2">
            <div class="col-sm-12 col-md-4 col-xs-12">
              <!-- <?php if($permission->wsmu_read): ?>
                  <a href="<?php echo e(route('employee.show',$employee->id)); ?>"
                     class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                  </a>
              <?php endif; ?> -->

              <?php if($permission->wsmu_delt): ?>
                  <input class="btn btn-danger btn-sm" onclick="empActvInactv()" id="empBtn" value="<?php echo e($employee->lfcl_id==1?'Active':'Inactive'); ?>" type="button"></input>
              <?php endif; ?>
              <?php if($permission->wsmu_updt): ?>
              
                  <!-- <form style="display:inline"
                        action="employee/<?php echo e($employee->id); ?>/reset"
                        class="form-horizontal " method="POST">
                      <?php echo e(csrf_field()); ?>

                      <?php echo e(method_field("PUT")); ?>

                      <input class="btn btn-danger btn-sm " type="submit"
                             value="Pass Reset"
                             onclick="return ConfirmReset()">
                      </input>
                  </form> -->
                  <input class="btn btn-default btn-sm" onclick="empPassReset()"  value="Pass Reset" type="button"></input>
              <?php endif; ?>
             
          </div>
       </div>
<div id="btn_employee"></div>
</div>        
<div class="row" id="acmp_id">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <center><strong> :::Company::: </strong></center>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" class="form-horizontal form-label-left">

            <div class="row">

                <form style="display:inline"
                      action="#"
                      class="pull-xs-right5 card-link">
                  
                    
                    <table class="table table-striped projects">
                        <tbody>
                          <td></td>
                        <td>

                            <span class="required">Add To Company</span>
                        </td>
                        <td>
                           <!--  <input type="text" class="form-control" name="acmp_code"
                                   placeholder="Company Code" value="<?php echo e(old('acmp_code')); ?>"> -->
                            <select class="form-control" name="acmp_code" id="acmp_code" required>
                                <?php $__currentLoopData = $acmp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($acmp->acmp_code); ?>"><?php echo e(ucfirst($acmp->acmp_name)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>

                        <td>
                            <input class="btn btn-success" type="button" value="Add" onclick="addCompany()">
                        </td>
                        <td></td>
                        </tbody>
                    </table>
                </form>
                <table  class="table table-striped table-bordered"
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
                                <input type="text" class="form-control"
                                       value="<?php echo e($companyMapping1->acmp_name); ?>" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                       value="<?php echo e($companyMapping1->acmp_code); ?>" readonly>
                            </td>
                            <td>
                                <a href="#" id="<?php echo e($companyMapping1->id); ?>" class="btn btn-danger btn-xs" onclick="deleteCompany(this)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
<div id="btn_company"></div>
</div>
<div class="row" id="slgp_id">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <center><strong> :::Group::: </strong></center>


            <div class="clearfix"></div>
        </div>
        <div class="x_content" class="form-horizontal form-label-left">

            <div class="row">
                <form style="display:inline"
                      action="#"
                      class="pull-xs-right5 card-link">
                    <table class="table table-striped projects">
                        <tbody>
                        <td>

                            <span class="required">Add To Group</span>
                        </td>
                        <td>
                            <select class="form-control"name="slgp_code" id="slgp_code" required  onchange="getPriceList(this.value)">
                                <option value="">Select group</option>
                                <?php $__currentLoopData = $slgp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slgp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($slgp->slgp_code); ?>"><?php echo e(ucfirst($slgp->slgp_name)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="plmt_code" id="plmt_code" required>
                                <option value="">Select price list</option>
                                
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="zone_code" id="zone_code" required>
                                <option value="">Select zone</option>
                                <?php $__currentLoopData = $zone_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($zone->zone_code); ?>"><?php echo e(ucfirst($zone->zone_name)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <input id="btn_group" class="btn btn-success" type="button" value="Add" onclick='addSlgp()'>
                            
                        </td>

                        </tbody>
                    </table>
                </form>
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
                    <tbody  id="slgp_list_cont">
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
                            <td>
                            <a href="#" id="<?php echo e($salesGroupMapping1->id); ?>" class="btn btn-danger btn-xs" onclick="deleteSlgp(this)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<div id="btn_route"></div>
</div>
<div class="row" id="dlrm_id">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">

            <center><strong> :::Dealer::: </strong></center>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" class="form-horizontal form-label-left">

            <div class="row">
                <form style="display:inline"
                      action="#"
                      class="pull-xs-right5 card-link">
                    <table class="table table-striped projects">
                        <tbody>
                        <td>

                            <span class="required">Add To Depot</span>
                        </td>
                        <td>
                            <select class="form-control" name="acmp_code" id="acmp_code1" required>
                                <?php $__currentLoopData = $depot_acmp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($acmp->acmp_code); ?>"><?php echo e(ucfirst($acmp->acmp_name)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="dlrm_code" id="dlrm_code"
                                   placeholder="Depot Code" value="<?php echo e(old('dlrm_code')); ?>">
                        </td>
                        <td>
                            <input class="btn btn-success" type="button" value="Add" onclick="addEmpDlr()">
                        </td>

                        </tbody>
                    </table>
                </form>
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
                                <a href="#" id="<?php echo e($depotMapping1->id); ?>" class="btn btn-danger btn-xs" onclick="deleteEmpDlr(this)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
<div id="btn_dealer"></div>
</div>
<div class="row" id="rpln_id">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <center><strong> :::Route Plan::: </strong></center>


            <div class="clearfix"></div>
        </div>
        <div class="x_content" class="form-horizontal form-label-left">

            <div class="row">
                <form style="display:inline"
                      action="<?php echo e(URL::to('employee/aemp_rpln_add/'.$employee->id)); ?>"
                      class="pull-xs-right5 card-link" method="POST">
                    <?php echo e(csrf_field()); ?>

                    <?php echo e(method_field('POST')); ?>

                    <table class="table table-striped projects">
                        <tbody>
                        <td>

                            <span class="required">Add Route</span>
                        </td>
                        <td>
                            <select class="form-control" name="day_name" id="day_name"
                                    required>
                                <option value="">Select</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>

                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="rout_code" id="rout_code"
                                   value="<?php echo e(old('rout_code')); ?>">
                        </td>
                        <td>
                            <input class="btn btn-success" type="button" value="Add" onclick="addEmpRoutePlan()">
                        </td>

                        </tbody>
                    </table>
                </form>
                <table id="data_table" class="table table-striped table-bordered"
                       data-page-length='25'>
                    <thead>
                    <tr style="background-color: #2b4570; color: white;">
                        <th>Day</th>
                        <th>Route Name</th>
                        <th>Route Code</th>
                        <th>Base Name</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="route_list_cont">
                    <?php $__currentLoopData = $routePlanMapping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routePlanMapping1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
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
                            <td>
                                <a href="#" id="<?php echo e($routePlanMapping1->rpln_id); ?>" class="btn btn-danger btn-xs" onclick="deleteEmpRoutePlan(this)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
<div id="btn_route"></div>
</div>

<div class="row" id="zgsm_id">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title">
            <center><strong> :::Zone Group Supervisor Mapping::: </strong></center>

            <div class="clearfix"></div>
        </div>
        <div class="x_content" class="form-horizontal form-label-left">

            <div class="row">
                <form style="display:inline"
                      action="#"
                      class="pull-xs-right5 card-link">
                    <table class="table table-striped projects">
                        <tbody>
                        <td>

                            <span class="required">Add Zone Group Mapping</span>
                        </td>

                        <td>

                            <select class="form-control" name="slgp_code" id="slgp_codessd" required>
                                <option value="">Select Group</option>
                                <?php $__currentLoopData = $slgp_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slgp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($slgp->id); ?>"><?php echo e($slgp->slgp_code); ?>

                                        - <?php echo e($slgp->slgp_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>


                        </td>
                        <td>

                            <select class="form-control" name="zone_code" id="zone_codsde" required>
                                <option value="">Select Zone</option>
                                <?php $__currentLoopData = $zoneGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($zoneg->id); ?>"><?php echo e($zoneg->zone_code); ?>

                                        - <?php echo e($zoneg->zone_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                        </td>
                        <td>
                            <input class="btn btn-success" type="button"
                                   value="Add" onclick="addZoneGroupMapping()">
                        </td>

                        </tbody>
                    </table>
                </form>
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
                            <td>
                                <a href="#" id="<?php echo e($zoneGroupMapping1->id); ?>" class="btn btn-danger btn-xs" onclick="deleteZoneGroupMapping(this)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
<div id="btn_premission"></div>
</div>
</div>
    </div>
<script type="text/javascript" src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    $SIDEBAR_MENU = $('#sidebar-menu')
    $(document).ready(function(){
        setTimeout(function() { 
            $('#menu_toggle').click();
        }, 1);
    });
    $('#expr_date').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: '0d',               
        autoclose: 1,
        showOnFocus: true
    });
  $('#aemp_usr_div').hide();
  $('#back_btn_div').hide();
  $('.select2').select2();
  $('#acmp_code').select2();
  $('#acmp_code1').select2();
  $('#slgp_code').select2();
  $('#zone_code').select2();
  $('#plmt_code').select2();
  $('#day_name').select2();
  $('#slgp_codessd').select2();
  $('#zone_codsde').select2();
  hide();
  function hide(){
    $('#acmp_id').hide();
    $('#slgp_id').hide();
    $('#dlrm_id').hide();
    $('#rpln_id').hide();
    $('#zgsm_id').hide();
  }
  function showCompany(){
   hide();
    $("#acmp_id").show();
  }
  function showGroup(){
   hide();
    $("#slgp_id").show();
  }
  function showDlrm(){
   hide();
    $("#dlrm_id").show();
  }
  function showCompany(){
   hide();
    $("#acmp_id").show();
  }
  function showRpln(){
   hide();
    $("#rpln_id").show();
  }
  function showZgsm(){
   hide();
    $("#zgsm_id").show();
  }
 $("#aemp_usnm").keyup(function(event) {
          if (event.keyCode === 13) {
              $("#find_user").click();
          }
});
 function getEmployeeUsnm(){
  var empId=$('#aemp_usnm').val();
  var _token = $("#_token").val();
  if(empId==''){
      return confirm('Please enter staff id');
  }
  if(empId.length<4){
      return confirm('Enter atleast four digit of staff id');
  }
  $.ajax({
      type:'POST',
      url: "<?php echo e(URL::to('/')); ?>/load/employeeUsnm",
      data: {
          empId: empId,
          _token: _token
      },
      cache: false,
      dataType: "json",
      success: function (data) {
          $('#employee_search_div').hide();
          $('#aemp_usr').empty();
          console.log(data);
          var html= '<option value="">Select Employee</option>';
          console.log(data.emp_list.length);
           for(var i = 0; i < data.emp_list.length; i++){
                      html +='<option value="'+data.emp_list[i]["id"]+'">' + data.emp_list[i]['aemp_usnm']+"-"+data.emp_list[i]['aemp_name'] + '</option>';
           }
          console.log(html);
          $('#aemp_usr_div').show();
          $('#aemp_usr').append(html);
          $('#back_btn_div').show();
      },error:function(error){
          console.log(error);
      }
  });
}
function getEmpDetails(id){
console.log(id);
var _token = $("#_token").val();
$('#ajax_load').css("display", "block");
$.ajax({
       type:'GET',
       url: "<?php echo e(URL::to('load/employeeData')); ?>/"+id,
       cache: false,
       success: function (data) {
        $('#ajax_load').css("display", "none");
        $('#emp_page_append').empty();
        $('#emp_page_append').append(data);
          
       },error:function(error){
           console.log(error);
       }
   });
}
// Company,group,Dealar,Rpln,zonemapping area
//Add  company
function addCompany(){
    var emp_usnm=$('#usnm').val();
    var acmp_code=$('#acmp_code').val();
    var _token = $("#_token").val();
    if(emp_usnm==''){
      return confirm('Please enter staff id');
    }
    $.ajax({
      type:'POST',
      url: "<?php echo e(URL::to('/')); ?>/add/empCompany",
      data: {
        emp_usnm: emp_usnm,
        acmp_code:acmp_code,
        _token: _token
      },
      cache: false,
      dataType: "json",
      success: function (data) {
        $('#acmp_list_cont').empty();
        $('#slgp_code').empty();
        $('#slgp_codessd').empty();
        $('#acmp_code1').empty();
        var html="";
        var slgp='<option value="">Select Group</option>';
        console.log(data);
        var depot_acmp='<option value="">Select Company</option>';
        for (var i = 0; i < data.companyMapping.length; i++) {
            html += '<tr>' +
                '<td>' + data.companyMapping[i]['acmp_name'] + '</td>' +
                '<td>' + data.companyMapping[i]['acmp_code'] + '</td>' +
                '<td><a href="#" id="'+data.companyMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteCompany(this)">'+"Delete"+ '</a></td>' +
                '</tr>';
            depot_acmp+='<option value="'+data.companyMapping[i]['acmp_code']+'">'+data.companyMapping[i]['acmp_name'] + '</option>';
                
        }
        for (var i = 0; i < data.slgp_list.length; i++) {
            slgp +='<option value="'+data.slgp_list[i]['slgp_code']+'">'+data.slgp_list[i]['slgp_name']+'</option>';
        }
        if(data.message==0){
            swal("Success!", "Company Added!", "success");
        }
        else if(data.message==1){
            swal("Warning!", "This company already exists!", "warning");
        }
        else if(data.message==3){
            swal("Warning!", "You don't have permission!", "warning");
        }      
        $('#acmp_list_cont').append(html);
        $('#slgp_code').append(slgp);
        $('#slgp_codessd').append(slgp);
        $('#acmp_code1').append(depot_acmp);
        console.log(slgp);
      },error:function(error){
          console.log(error);
      }
  });
}
function deleteCompany(v){
    var id=$(v).attr('id');
    var emp_usnm=$('#usnm').val();
    var _token = $("#_token").val();
    console.log(id);
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this !!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    })
    .then((willDelete) => {
    if (willDelete) {
        $.ajax({
      type:'post',
      data: {
        emp_usnm: emp_usnm,
        id:id,
        _token: _token
      },
      url: "<?php echo e(URL::to('/')); ?>/delete/empCompany",
      cache: false,
      dataType: "json",
      success: function (data) {
        $('#acmp_list_cont').empty();
        //$('#ajax_load').css("display", "none");
        var html="";
        $('#slgp_code').empty();
        $('#slgp_codessd').empty();
        $('#acmp_code1').empty();
        var slgp='<option value="">Select Group</option>';
        var depot_acmp='<option value="">Select Company</option>';
        for (var i = 0; i < data.slgp_list.length; i++) {
            slgp +='<option value="'+data.slgp_list[i]['slgp_code']+'">'+data.slgp_list[i]['slgp_name']+'</option>';
        }
        for (var i = 0; i < data.companyMapping.length; i++) {
            html += '<tr>' +
                '<td>' + data.companyMapping[i]['acmp_name'] + '</td>' +
                '<td>' + data.companyMapping[i]['acmp_code'] + '</td>' +
                '<td><a href="#" id="'+data.companyMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteCompany(this)">'+"Delete"+ '</a></td>' +
                '</tr>';
                depot_acmp+='<option value="'+data.companyMapping[i]['acmp_code']+'">'+data.companyMapping[i]['acmp_name'] + '</option>';
        }
        $('#acmp_list_cont').append(html);
        $('#slgp_code').append(slgp);
        $('#slgp_codessd').append(slgp);
        $('#acmp_code1').append(depot_acmp);
       
      },error:function(error){
          console.log(error);
      }
    });
        swal("oh! Company Removed!", {
        icon: "success",
        });
    } else {
        swal("Everything is ok.!");
    }
    });
    

}
// Group
function addSlgp(){
    var emp_usnm=$('#usnm').val();
    var slgp_code=$('#slgp_code').val();
    var plmt_code=$('#plmt_code').val();
    var zone_code=$('#zone_code').val();
    var _token = $("#_token").val();
    if(emp_usnm==''){
      return confirm('Please enter staff id');
    }
    $.ajax({
      type:'POST',
      url: "<?php echo e(URL::to('/')); ?>/add/empSlgp",
      data: {
        emp_usnm: emp_usnm,
        slgp_code:slgp_code,
        plmt_code:plmt_code,
        zone_code:zone_code,
        _token: _token
      },
      cache: false,
      dataType: "json",
      success: function (data) {
        $('#slgp_list_cont').empty();
        var html="";
        console.log(data);
        for (var i = 0; i < data.salesGroupMapping.length; i++) {
            html += '<tr>' +
                '<td>' + data.salesGroupMapping[i]['slgp_name'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['slgp_code'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['plmt_name'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['plmt_code'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['zone_name'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['zone_code'] + '</td>' +
                '<td><a href="#" id="'+data.salesGroupMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteSlgp(this)">'+"Delete"+ '</a></td>' +
                '</tr>';
        }
        if(data.message==0){
            swal("Success!", "Sales Group Added!", "success");
        }
        else if(data.message==1){
            swal("Warning!", "Already exists!", "warning");
        }
        else if(data.message==3){
            swal("Warning!", "You don't have permission!", "warning");
        }      
        $('#slgp_list_cont').append(html);
      },error:function(error){
          console.log(error);
      }
  });
}
function deleteSlgp(v){
    var id=$(v).attr('id');
    var emp_usnm=$('#usnm').val();
    var _token = $("#_token").val();
    console.log(id);
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this !!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    })
    .then((willDelete) => {
    if (willDelete) {
        $.ajax({
      type:'post',
      data: {
        emp_usnm: emp_usnm,
        id:id,
        _token: _token
      },
      url: "<?php echo e(URL::to('/')); ?>/delete/empSlgp",
      cache: false,
      dataType: "json",
      success: function (data) {
        $('#slgp_list_cont').empty();
        //$('#ajax_load').css("display", "none");
        var html="";
        console.log(data);
        for (var i = 0; i < data.salesGroupMapping.length; i++) {
            html += '<tr>' +
                '<td>' + data.salesGroupMapping[i]['slgp_name'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['slgp_code'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['plmt_name'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['plmt_code'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['zone_name'] + '</td>' +
                '<td>' + data.salesGroupMapping[i]['zone_code'] + '</td>' +
                '<td><a href="#" id="'+data.salesGroupMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteSlgp(this)">'+"Delete"+ '</a></td>' +
                '</tr>';
        }
        $('#slgp_list_cont').append(html);
       
      },error:function(error){
          console.log(error);
      }
     });
        swal("oh! Sales Group Removed!", {
        icon: "success",
        });
    } else {
        swal("Everything is ok.!");
    }
    });
    

}
// Group on select feed pricelist dropdown
function getPriceList(slgp_code){
    $.ajax({
        type:"get",
        url: "<?php echo e(URL::to('/')); ?>/get/empSlgpPriceList/"+slgp_code,
        cache: false,
        dataType: "json",
        success: function (data) {
            console.log(data);
            $('#plmt_code').empty();
            var html='<option value="">Select price</option>';
            for(var i=0;i<data.length;i++){
                html+='<option value="'+data[i]['code']+'">'+data[i]['name']+'</option>';
            }
            $('#plmt_code').append(html);

        }
    });
}
// Save Employee Info
function updateEmpInfo(){
    var email=$('#usnm').val();
    var id=$('#emp_id').val();
    var name=$('#name').val();
    var ln_name=$('#ln_name').val();
    var mobile=$('#mobile').val();
    var address=$('#address').val();
    var location_on=$('#location_on').is(':checked')?1:0;
    var aemp_issl=$('#aemp_issl').is(':checked')?1:0;
    var auto_email=$('#otml').is(':checked')?1:0;
    var aemp_asyn=$('#aemp_asyn').is(':checked')?'Y':'N';
    var email_cc=$('#email_cc').val();
    var role_id=$('#role_id').val();
    var master_role_id=$('#master_role_id').val();
    var manager_id=$('#manager_id').val();
    var line_manager_id=$('#line_manager_id').val();
    var input_img=$('#input_img').val();
    var allowed_distance=$('#allowed_distance').val();
    var site_id=$('#site_id').val();
    var aemp_crdt=$('#aemp_crdt').val();
    var amng_id=$('#amng_id').val();
    var _token=$('#_token').val();
    var cont_id=$('#cont_id').val();
    var visa_no=$('#visa_no').val();
    var expr_date=$('#expr_date').val();
    $.ajax({
        type:'post',
        url:"<?php echo e(URL::to('/')); ?>/updateEmpInfo/"+id,
        data:{
            email:email,
            name:name,
            ln_name:ln_name,
            mobile:mobile,
            address:address,
            location_on:location_on,
            email_cc:email_cc,
            role_id:role_id,
            master_role_id:master_role_id,
            manager_id:manager_id,
            line_manager_id:line_manager_id,
            input_img:input_img,
            allowed_distance:allowed_distance,
            site_id:site_id,
            aemp_crdt:aemp_crdt,
            aemp_issl:aemp_issl,
            aemp_asyn:aemp_asyn,
            amng_id:amng_id,
            auto_email:auto_email,
            cont_id:cont_id,
            visa_no:visa_no,
            expr_date:expr_date,
            _token:_token
        },
        success:function(data){
            swal("Success!", "Employee Info Updated Successfully!", "success");
        },error:function(error){
           console.log(error);
           swal("Warning!", "Something went wrong!", "warning");
        }
    });
}
// Add Dealar 
function addEmpDlr(){
    var acmp_code=$('#acmp_code1').val();
    var dlrm_code=$('#dlrm_code').val();
    var id=$('#emp_id').val();
    var _token=$('#_token').val();
    if(dlrm_code ==""){
        return confirm("Please provide depot code");
    }
    $.ajax({
        type:"POST",
        url:"<?php echo e(URL::to('/')); ?>/add/empDlrm/"+id,
        data:{
            acmp_code:acmp_code,
            dlrm_code:dlrm_code,
            _token:_token
        },
        dataType:"json",
        success:function(data){
            console.log(data);
            $('#dlrm_list_cont').empty();
            var html='';
            for(var i=0;i<data.depotMapping.length;i++){
                html+='<tr>'+
                        '<td>'+data.depotMapping[i]['dlrm_name']+'</td>'+
                        '<td>'+data.depotMapping[i]['dlrm_code']+'</td>'+
                        '<td>'+data.depotMapping[i]['acmp_name']+'</td>'+
                        '<td>'+data.depotMapping[i]['acmp_code']+'</td>'+
                        '<td>'+data.depotMapping[i]['base_name']+'</td>'+
                        '<td><a href="#" id="'+data.depotMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteEmpDlr(this)">'+"Delete"+ '</a></td>' +
                        '</tr>';
            }
            $('#dlrm_list_cont').append(html);
            if(data.message==0){
                swal("Success!", "Depot Added Successfully!", "success");
            }
            else if(data.message==1){
                swal("Warning!", "Already exist Or Code doesn't Match!", "warning");
            }
            else{
                swal("Opps!", "Already exist Or Code doesn't Match!", "danger");
            }
            console.log(data);

        },error:function(data){
            console.log(data)
        }
    });
}
function deleteEmpDlr(v){
    var id=$(v).attr('id');
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this !!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    })
    .then((willDelete) => {
    if (willDelete) {
        $.ajax({
      type:'get',
      url: "<?php echo e(URL::to('/')); ?>/delete/empDlr/"+id,
      cache: false,
      dataType: "json",
      success: function (data) {
        $('#dlrm_list_cont').empty();
            var html='';
            for(var i=0;i<data.depotMapping.length;i++){
                html+='<tr>'+
                        '<td>'+data.depotMapping[i]['dlrm_name']+'</td>'+
                        '<td>'+data.depotMapping[i]['dlrm_code']+'</td>'+
                        '<td>'+data.depotMapping[i]['acmp_name']+'</td>'+
                        '<td>'+data.depotMapping[i]['acmp_code']+'</td>'+
                        '<td>'+data.depotMapping[i]['base_name']+'</td>'+
                        '<td><a href="#" id="'+data.depotMapping[i]['id']+'" class="btn btn-danger btn-xs" onclick="deleteEmpDlr(this)">'+"Delete"+ '</a></td>' +
                        '</tr>';
            }
            $('#dlrm_list_cont').append(html);
       
      },error:function(error){
          console.log(error);
      }
     });
        swal("Depot Removed Successfully!", {
        icon: "success",
        });
    } else {
        swal("Everything is ok.!");
    }
    });  

}
// Add Route Plan
function addEmpRoutePlan(){
    var day_name=$('#day_name').val();
    var rout_code=$('#rout_code').val();
    var id=$('#emp_id').val();
    var _token=$('#_token').val();
    $.ajax({
        type:'post',
        url:"<?php echo e(URL::to('/')); ?>/add/empRoutePlan/"+id,
        data:{
            day_name:day_name,
            rout_code:rout_code,
            _token:_token
        },
        dataType:"json",
        success:function(data){
            $('#route_list_cont').empty();
            var html='';
            for(var i=0;i<data.routePlanMapping.length;i++){
                html+='<tr>'+
                        '<td>'+data.routePlanMapping[i]['rpln_day']+'</td>'+
                        '<td>'+data.routePlanMapping[i]['rout_name']+'</td>'+
                        '<td>'+data.routePlanMapping[i]['rout_code']+'</td>'+
                        '<td>'+data.routePlanMapping[i]['base_name']+'</td>'+
                        '<td><a href="#" id="'+data.routePlanMapping[i]['rpln_id']+'" class="btn btn-danger btn-xs" onclick="deleteEmpRoutePlan(this)">'+"Delete"+ '</a></td>' +
                        '</tr>';
            }
            $('#route_list_cont').append(html);
            if(data.message==0){
                swal("Success!", "Route Plan Added Successfully!", "success");
            }
            else if(data.message==1){
                swal("Warning!", "Code doesn't Match!", "warning");
            }
           
        },error:function(data){

        }
    });
}
// Delete Route Plan

function deleteEmpRoutePlan(v){
    var id=$(v).attr('id');
    var eid=$('#emp_id').val();
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this !!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    })
    .then((willDelete) => {
    if (willDelete) {
        $.ajax({
      type:'get',
      url: "<?php echo e(URL::to('/')); ?>/delete/empRoutePlan/"+id+"/"+eid,
      cache: false,
      dataType: "json",
      success: function (data) {
        $('#route_list_cont').empty();
        var html='';
        for(var i=0;i<data.routePlanMapping.length;i++){
            html+='<tr>'+
                    '<td>'+data.routePlanMapping[i]['rpln_day']+'</td>'+
                    '<td>'+data.routePlanMapping[i]['rout_name']+'</td>'+
                    '<td>'+data.routePlanMapping[i]['rout_code']+'</td>'+
                    '<td>'+data.routePlanMapping[i]['base_name']+'</td>'+
                    '<td><a href="#" id="'+data.routePlanMapping[i]['rpln_id']+'" class="btn btn-danger btn-xs" onclick="deleteEmpRoutePlan(this)">'+"Delete"+ '</a></td>' +
                    '</tr>';
        }
        $('#route_list_cont').append(html);
        if(data.message==0){
            swal("Route Plan Removed Successfully!", {
            icon: "success",
            });
        }else{
            swal("Access Limited", {
            icon: "danger",
            });
        }
       
      },error:function(error){
          console.log(error);
      }
     });
       
    } else {
        swal("Everything is ok.!");
    }
    });  

}
// Add Zone Group Mapping
function addZoneGroupMapping(){
    var slgp_code=$('#slgp_codessd').val();
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
                swal("Success!", "Zone group mapping added!", "success");
            }
            else if(data.message==1){
                swal("Warning!", "Access Limited!", "warning");
            }
           }else{
               swal("Zone already associated with this employee:-");
               console.log(data);
           }
        },error:function(data){
            console.log(data);
            swal("Warning!", "Something went!", "warning");
        }
    });
}
// Delete Zone Group Mapping

function deleteZoneGroupMapping(v){
    var id=$(v).attr('id');
    var eid=$('#emp_id').val();
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this !!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
    })
    .then((willDelete) => {
    if (willDelete) {
        $.ajax({
      type:'get',
      url: "<?php echo e(URL::to('/')); ?>/delete/zoneGroupMapping/"+id+"/"+eid,
      cache: false,
      dataType: "json",
      success: function (data) {
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
            swal("Zone Group Mapping Removed!", {
            icon: "success",
            });
        }else{
            swal("Access Limited", {
            icon: "danger",
            });
        }
       
      },error:function(error){
          console.log(error);
      }
     });
       
    } else {
        swal("Everything is ok.!");
    }
    });  

}
// Employee Active Inactive
function empActvInactv(){
    var id=$('#emp_id').val();
   // return confirm("Are you sure???");
    $.ajax({
        type:'get',
        url:"<?php echo e(URL::to('/')); ?>/empActvInactv/"+id,
        dataType:"json",
        success:function(data){
           
            var text='';
            if(data.emp==1){
                text='Active';
            }else{
                text='Inactive';
            }
            document.getElementById("empBtn").value =text;
            if(data.message==0){
                swal("Success","Employee life cycle updated successfully","success");
            }else{
                swal("Danger","Access limited","danger");
            }
        },error:function(error){
            swal("Warning!", "Something went wrong!!", "warning");
            console.log(error);
            
        }

    });
}
function empPassReset(){
    var id=$('#emp_id').val();
    $.ajax({
        type:'get',
        url:"<?php echo e(URL::to('/')); ?>/empPassReset/"+id,
        dataType:"json",
        success:function(data){
            console.log(data);
            if(data==0){
                swal("Success","Employee Pass Reset successfully","success");
            }else{
                swal("Danger","Access limited","warning");
            }
        },error:function(error){
            swal("Warning!", "Something went wrong!!", "warning");
            console.log(error);
            
        }

    });
}
</script><?php /**PATH /home1/test/sw/resources/views/master_data/employee/new_edit.blade.php ENDPATH**/ ?>