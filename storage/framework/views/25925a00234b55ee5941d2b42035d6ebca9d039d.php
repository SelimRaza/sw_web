

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
                            <strong>  </strong>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row"></div>
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
                <div class="col-md-12" style="min-height:400px;">
                    <div class="x_panel">
                        <div class="x_content">
                        <div class="col-md-12">
                            <div>
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itsg_id"> Holiday Date                                                
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control in_tg" name="start_date" id="start_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="h_atyp">Holiday Type
                                            </label>
                                            <div class="col-md-6 col-sm-6  col-xs-12">
                                                <select class="form-control in_tg" id="h_atyp" name="h_atyp">
                                                        <option value="7">Off Day</option>
                                                        <option value="6">Gvt Holiday</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    
                                        <div class="form-group col-md-12  col-sm-12 col-xs-12">
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                            <?php if($permission->wsmu_crat==1): ?>
                                                <button type="submit" class="btn btn-default" onclick="addHoliday()">Add Holiday</button>
                                            <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <?php if($permission->wsmu_crat==1): ?>
                                                <button class="btn btn-warning" onclick="adjustAllHoliday()">Holiday Leave Adjustment</button>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group col-md-12 col-sm-12 col-xs-12" style="display:none;" id="attn_load">
                                                <img src="<?php echo e(asset('theme/image/attn_load.gif')); ?>" >
                                            </div>
                                            
                                    </div>
                                    
                                </div>
                                    
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tableDiv">
                    <div class="x_panel">

                        <div class="x_content">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="overflow: auto;">
                                <div align="right">

                                    <button onclick="exportTableToCSV('activity_summary_report_<?php echo date('Y_m_d'); ?>.csv','tableDiv')"
                                            class="btn btn-warning">Export CSV File
                                    </button>
                                </div>
                                <table id="datatablesa" class="table table-bordered table-responsive"
                                        style="overflow-x: auto;"
                                        data-page-length='100'>
                                    <thead>
                                    <tr class="">
                                        
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Holiday Type</th>
                                        <th>Created Date</th>
                                        <th>Action</th>
                                        
                                    </tr>

                                    </thead>
                                    <tbody id="cont">
                                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>1</td>
                                                <td><?php echo e($d->h_date); ?></td>
                                                <td><?php echo e($d->atyp_name); ?></td>
                                                <td><?php echo e($d->created_at); ?></td>
                                                <td><a class="btn btn-danger btn-xs" onclick="removeHoliday(this)" id="<?php echo e($d->id); ?>">Delete</a></td>
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
</div>

    <script type="text/javascript">
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        $('#start_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: '-3m',
            autoclose: 1,
            showOnFocus: true
        });
      
        function addHoliday(){
            $('#attn_load').show();
            let start_date=$('#start_date').val();
            let h_atyp=$('#h_atyp').val();
            let _token=$('#_token').val();
            if(start_date =='' || h_atyp==''){
                alert("Please fill all input field");
                return false;
            }
            $.ajax({
                type:"POST",
                url: "<?php echo e(URL::to('/')); ?>/emp/holiday/",
                data:{
                    start_date:start_date,
                    h_atyp:h_atyp,
                    _token:_token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    //location.reload();
                    console.log(data)
                    $('#attn_load').hide();
                    var html='';
                    for(let i=0;i<data.length;i++){
                        html+='<tr>'+
                                '<td>'+(i+1)+'</td>'+
                                '<td>'+data[i].h_date+'</td>'+
                                '<td>'+data[i].atyp_name+'</td>'+
                                '<td>'+data[i].created_at+'</td>'+
                                '<td><a class="btn btn-danger btn-xs" onclick="removeHoliday(this)" id="'+data[i].id+'">Delete</a></td>'+
                                '</tr>';
                    }
                    $('#cont').empty();
                    $('#cont').append(html);
                    swal.fire({
                        icon:'success',
                        text:'Holiday Added Successfully',
                    })
                },
                error:function(error){
                    $('#attn_load').hide();
                    console.log(error);
                    swal.fire({
                        icon:'warning',
                        text:'Something Went Wrong!!',
                    })
                }
            });
        }
        function removeHoliday(v){
            let id=$(v).attr('id');
           
            $.ajax({
                type:"GET",
                url: "<?php echo e(URL::to('/')); ?>/emp/holiday/remove/"+id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    if(data==1){
                        $(v).parent().parent().remove();
                        swal.fire({
                        icon:'success',
                        text:'Holiday Removed',
                        });
                    }
                    else{
                        swal.fire({
                        icon:'warning',
                        text:'You do not have permission!!',
                        });
                    }
                    
                },
                error:function(error){
                    console.log(error);
                }
            });
        }

        function adjustAllHoliday(){
            $('#attn_load').show();
            $.ajax({
                type:'GET',
                url: "<?php echo e(URL::to('/')); ?>/emp/holiday/adjustment/",
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#attn_load').hide();
                    if(data==1){
                        swal.fire({
                        icon:'success',
                        text:'Leave Adjustment Succeed',
                        });
                    }
                    else{
                        swal.fire({
                        icon:'warning',
                        text:'Something Went Wrong!!',
                        });
                    }
                },
                error:function(error){
                    console.log(error);
                }
            });
        }
        
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/Attendance/Holiday/create.blade.php ENDPATH**/ ?>