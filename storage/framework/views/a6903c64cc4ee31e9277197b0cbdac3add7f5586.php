

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
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itsg_id">Start Date                                                
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input type="text" class="form-control in_tg" name="start_date" id="start_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" for="end_date">End Date
                                            </label>
                                            <div class="col-md-6 col-sm-6  col-xs-12">
                                                <input type="text" class="form-control in_tg" name="end_date" id="end_date" autocomplete="off" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="aemp_usnm">Staff Id
                                                        
                                                </label>
                                                <div class="col-md-6 col-sm-6  col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="aemp_usnm" id="aemp_usnm">
                                                </div>
                                        </div>
                                    
                                        <div class="form-group col-md-12  col-sm-12 col-xs-12">
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <button type="submit" class="btn btn-default" onclick="processAttendance()">Run Process</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
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
            maxDate: new Date(),
            autoclose: 1,
            showOnFocus: true
        });
        $("#end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });
        function closeWindow(){
            window.close();
        }
        function processAttendance(){
            $('#attn_load').show();
            let start_date=$('#start_date').val();
            let end_date=$('#end_date').val();
            let aemp_usnm=$('#aemp_usnm').val();
            let _token=$('#_token').val();
            $.ajax({
                type:"POST",
                url: "<?php echo e(URL::to('/')); ?>/emp/attn_process",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    aemp_usnm:aemp_usnm,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data)
                    $('#attn_load').hide();
                    swal.fire({
                        icon:'success',
                        text:'Attendace Processed Successfully',
                    })
                },
                error:function(error){
                    $('#attn_load').hide();
                    console.log(error);
                    swal.fire({
                        icon:'warning',
                        text:'Found Issue..',
                    })
                }
            });
        }
        function removeItem(v){
            $(v).parent().parent().remove();
        }
        
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/Attendance/Process/create.blade.php ENDPATH**/ ?>