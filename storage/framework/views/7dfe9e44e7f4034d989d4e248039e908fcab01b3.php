

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class=""  id="emp_page_append">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>Employee</strong>
                        </li>
                    </ol>
                </div>
                <form action="<?php echo e(URL::to('/employee')); ?>" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="<?php echo e($search_text); ?>">
                                <span class="input-group-btn">
                                  <button class="btn btn-default" type="submit">Go!</button>
                                </span>

                            </div>
                        </div>
                    </div>
                </form>
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
                <div class="col-md-12">
                    <div class="x_panel col-md-12">
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

                        </div>
                    </div>
                    <div class="x_content rp_type_div x_panel" id="emp_info">

                       
                    </div>
                </div>
            </div>
        
    <script type="text/javascript">
        $('#aemp_usr_div').hide();
        $('#back_btn_div').hide();
        $('.select2').select2();

        <?php if (isset($aemp_temp)){
            $temp_var = $aemp_temp;
        }else{
            $temp_var = "";
        }

        if ($temp_var!=''){?>
        getEmpDetails(<?php echo e($temp_var); ?>);
        console.log(<?php echo e($temp_var); ?>);
        <?php }
        ?>



        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function ConfirmReset() {
            var x = confirm("Are you sure you want to Reset?");
            if (x)
                return true;
            else
                return false;
        };
        function searchDivHideShow(){
            var x=document.getElementById('employee_search_div');

            if (x.style.display === "none") {
                x.style.display = "block";
                $('#aemp_usr_div').hide();
              } 
              else {
                x.style.display = "none";
                $('#aemp_usr_div').show();
              }
        }
        //mouse enter button click event
        $("#aemp_usnm").keyup(function(event) {
            if (event.keyCode === 13) {
                $("#find_user").click();
            }
        });
        //load employee user name into dropdown list
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
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/employee/new_index.blade.php ENDPATH**/ ?>