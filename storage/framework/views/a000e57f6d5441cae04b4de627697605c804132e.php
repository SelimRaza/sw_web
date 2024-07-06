<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>

                        <li class="active">
                            <strong>Thana Employee</strong>
                        </li>
                        <?php if($permission->wsmu_crat): ?>
                            <li class="label-success">
                                <a href="<?php echo e(URL::to('/ThanSR/depotEmployeeMappingUploadFormat')); ?>">Thana Employee
                                    Format</a>
                            </li>
                        <?php endif; ?>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <?php if(Session::has('success')): ?>
                    <div class="alert alert-success">
                        <strong>Success! </strong><?php echo e(Session::get('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(Session::has('danger')): ?>
                    <div class="alert alert-danger">
                        <strong>Alert! </strong><?php echo e(Session::get('danger')); ?>

                    </div>
                <?php endif; ?>
                <div class="col-md-12">

                    <?php if($permission->wsmu_updt): ?>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                       
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                   role="button"
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
                                        <br/>
                                        <form id="demo-form2" data-parsley-validate
                                              class="form-horizontal form-label-left"
                                              action="<?php echo e(URL::to('/ThanSR/depotEmployeeMappingUpload')); ?>"
                                              enctype="multipart/form-data"
                                              method="post">
                                            <?php echo e(csrf_field()); ?>

                                            <?php echo e(method_field('POST')); ?>

                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                       for="first-name">File
                                                    <span class="required">*</span>
                                                </label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <input id="name" class="form-control col-md-7 col-xs-12"
                                                           data-validate-length-range="6"
                                                           data-validate-words="2"
                                                           name="import_file"
                                                           placeholder="Shop List file" type="file"
                                                           step="1">
                                                </div>
                                            </div>
                                            <div class="ln_solid"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <button type="submit" class="btn btn-success">Upload
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3>SR Thana Mapping Info</h3>
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
                              action="<?php echo e(URL::to('/ThanSR/dataExportThanSRMappingInfotData')); ?>"
                              method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <?php echo e(csrf_field()); ?>

                            <div class="row">

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                        <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="slgp_id" id="slgp_id"
                                                required>
                                            <?php $__currentLoopData = $slgp_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slgp_data1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($slgp_data1->slgp_id); ?>"><?php echo e(ucfirst($slgp_data1->slgp_name)."(".$slgp_data1->slgp_code.")"); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Zone

                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="zone_id" id="zone_id">
                                            <option value="0">Select Zone</option>
                                            <?php $__currentLoopData = $zone_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone_data1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($zone_data1->zone_id); ?>"><?php echo e(ucfirst($zone_data1->zone_name)."(".$zone_data1->zone_code.")"); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
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

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3>SR Thana Mapping</h3>
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
                              action="<?php echo e(URL::to('ThanSR/mapping')); ?>"
                              method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <?php echo e(csrf_field()); ?>

                            <div class="row">

                                <div class="row">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Employee Code
                                        </label>
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <input id="aemp_code" name="aemp_code" class="form-control col-md-7 col-xs-12" placeholder="Enter Employee Code">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thana Code
                                        </label>
                                        <div class="col-md-5 col-sm-5 col-xs-12">
                                            <input id="than_code" name="than_code" class="form-control col-md-7 col-xs-12" placeholder="Enter Thana Code">
                                        </div>
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

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3>SR Thana Mapping Delete</h3>
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
                        <form class="form-horizontal form-label-left">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <?php echo e(csrf_field()); ?>

                            <div class="row">
                                <div class="item form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Staff ID
                                </label>
                                    <div class="col-md-5 col-sm-5 col-xs-12">
                                        <input id="staff_code" name="staff_code" class="form-control col-md-7 col-xs-12">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-1">
                                       <input type="button" class="form-control btn btn-success btn-md" value="View" onclick="getUserThana()">
                                    </div>
                                </div>  
                            </div>
                        </form>
                    </div>
                    <!-- <div class="col-sm-3"><input type="search" placeholder="Search..." class="form-control search-input input-sm search" id="search"></div> -->
                    <div class="col-sm-5"></div>
                    <div class="x_content">
                        <table class="table table-striped projects" id="employee_table">
                            <thead style="background-color: #f0ecec;">
                                <tr>
                                    <th>SL</th>
                                    <th>Thana</th>
                                    <th>SR</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="userthana">
                                 
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#emp_id").select2({width: 'resolve'});
        $("#acmp_id").select2({width: 'resolve'});
        $("select").select2({width: 'resolve'});

        function getUserThana(){

          var staff_code=$('#staff_code').val();
          if(staff_code==""){
             
            alert("Please Enter Right Staff ID..!!");

          }else{

            var url = "<?php echo e(url('/json/get/sr_wise/thana/list')); ?>/"+staff_code;
            $.get(url, function(data) {

                var rows = ''; 
                var p=1;
                $.each(data, function (key, value) {
                       rows = rows + '<tr>';
                       rows = rows + '<td>' + p++ + '</td>';
                       rows = rows + '<td>' + value.thana_code+'-'+value.thana_name + '</td>';
                       rows = rows + '<td>' + value.user_code+'-'+value.user_name + '</td>';
                       rows = rows + '<td>' + '<input type="button" class="btn btn-danger btn-xs" value="Delete" id="'+ value.id +'" onclick="return deleteSRFromThana(this.id)">' + '</td>';
                       rows = rows + '</tr>';
                });
                $("#userthana").html(rows);      
                    
            });

          }
           
        } 

        function deleteSRFromThana(id){

          var x = confirm("Are you sure you want to delete?");
          if(x){

             $.ajax({

                type: "GET",
                url: "<?php echo e(url('/json/delete/sr_wise/thana/list')); ?>/"+id,
                success: function (value) {
                      
                   if(value['0']="Success"){

                     document.getElementById('employee_table').deleteRow(id);
                     alert("Delete Successful...!!");  
                   }   
                     
                }

            });
                
         }

     }       
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Depot/Depot/thansr_mapping.blade.php ENDPATH**/ ?>