

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">
                            <strong>Site Mapping</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">
                <?php if($permission->wsmu_crat): ?>
                    <a href="<?php echo e(URL::to('/bulk/route/site')); ?>" style="color:darkred;font-weight:bold;" target="_blank"><i class="fa fa-upload"></i> Upload</a>
                <?php endif; ?>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div >
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                            <div class="form-group col-md-5 col-sm-5 col-xs-12">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="rout_id">Route
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="rout_id" id="rout_id"
                                            >
                                        <option value="">Select </option>
                                        <?php $__currentLoopData = $rout_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($rout->id); ?>"><?php echo e('('.$rout->rout_code.') '.ucfirst($rout->rout_name)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-5 col-sm-5 col-xs-12">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="site_code">Site Code
                                           
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="text" class="form-control in_tg" name="site_code" id="site_code">
                                </div>
                            </div>
                            
                        </div>
                        <div  class="col-md-2 col-sm-2 col-xs-12">
                            <?php if($permission->wsmu_vsbl): ?>
                                <button type="submit" class="btn btn-primary" onclick="filterData()">Search</button>
                            <?php endif; ?>
                            <?php if($permission->wsmu_crat): ?>
                            <button type="submit" class="btn btn-success"  onclick="addSiteToRoute()" >Add</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h3 class="text-center">Site Mapping</h3>
                                <?php if($permission->wsmu_delt): ?>
                                <button  class="btn btn-danger" type="submit" onclick="deleteSiteFromRoute()">Delete</button>
                                <?php endif; ?>
                                <table class="table table-striped table-bordered">
                                    <thead>                                  
                                        <tr>
                                            <th><input type="checkbox" id="site_all">  All</th>
                                            <th>SL</th>
                                            <th>Route Code</th>
                                            <th>Rout Name</th>
                                            <th>SV Name</th>
                                            <th>SR Name</th>
                                            <th>Site Code</th>
                                        </tr>
                                    
                                    </thead>
                                    <tbody id="cont">
                                    
                                    </tbody>
                                </table>
                            </div>
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
        $('#site_all').on('click', function(e) {
            if($(this).is(':checked',true)){
            $(".single_site").prop('checked', true);
            } else {
            $(".single_site").prop('checked',false);
            }

        });
        
        function filterData() {
            let rout_id = $("#rout_id").val();      
            let site_code = $("#site_code").val();      
            let _token = $("#_token").val();
            if(rout_id =="" && site_code ==""){
                swal.fire({
                    icon:'warning',
                    text:'Please select route /site'
                });
                return false;
            }
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/site-mapping",
                data: {
                    rout_id: rout_id,
                    site_code: site_code,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    let html = '';
                    let count = 1;
                    for (let i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td><input type="checkbox" value="'+data[i].rsmp_id+'" class="single_site" name="single_site"></td>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].rout_code + '</td>' +
                            '<td>' + data[i].rout_name + '</td>' +
                            '<td>' + data[i].sv_usnm+'-'+ data[i].sv_name+ '</td>' +
                            '<td>' + data[i].aemp_usnm+'-'+ data[i].aemp_name+ '</td>' +
                            '<td>' + data[i].site_code + '</td>' +
                           '</tr>';
                            
                        count++;
                    }
                   $("#cont").append(html)
                },error:function(error){
                    $('#ajax_load').css("display", "none");
                    console.log(error)
                    swal.fire({
                        icon:"error",
                        text:"Something Went Wrong !!!",
                    })
                }
            });
        }

        function deleteSiteFromRoute(){
            let rmsite = [];
            let _token =$('#_token').val();
            $.each($("input[name='single_site']:checked"), function(){
                rmsite.push($(this).val());
            });
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/remove/site/rsmp",
                data: {
                    rmsite:rmsite,
                   _token:_token,
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                    $("input[name='single_site']:checked").parent().parent().remove();
                    Swal.fire({
                    icon:'success',
                    text: 'Site removed from Route!',
                    });
                },
                error: function(data) {
                    Swal.fire({
                    icon:'error',
                    text: 'Something Went Wrong!!!',
                    })
                    console.log(data);

                }
            });
        }

        function addSiteToRoute(){
            let rout_id=$('#rout_id').val();
            let site_code=$('#site_code').val();
            if(rout_id =="" || site_code==""){
                Swal.fire({
                            icon:'warning',
                            text: 'Route And Site Code Both Required',
                 });
                 return false;
            }
            $.ajax({
                type: "get",
                url: "<?php echo e(URL::to('/')); ?>/add/site/rsmp/"+rout_id+"/"+site_code,
                
                cache: false,
                dataType: "json",
                success: function(data) {
                    if(data==1){
                        Swal.fire({
                            icon:'success',
                            text: 'Site added to this Route!',
                        });
                    }
                    else if(data==2){
                        Swal.fire({
                            icon:'warning',
                            text: 'Already Exists!!',
                        });
                    }
                    else{
                        Swal.fire({
                            icon:'warning',
                            text: 'Site Code is not valid',
                        });
                    }
                    
                },
                error: function(data) {
                    Swal.fire({
                    icon:'error',
                    text: 'Something Went Wrong!!!',
                    })
                    console.log(data);

                }
            });
        }
        
       
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Mapping/Site/index.blade.php ENDPATH**/ ?>