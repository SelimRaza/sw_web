

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
                            <strong>Trip Details</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <!-- start field -->
                        
                                <?php echo csrf_field(); ?>
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <!-- <div  class="col-md-12 col-sm-12 col-xs-12"> -->
                                <div class="form-row col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="dlrm_id">Depo<span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="dlrm_id"
                                                    id="dlrm_id">
                                                
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $dlrm_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dlrm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($dlrm->id); ?>"><?php echo e($dlrm->dlrm_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="sv_id">DM ID<span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <select class="form-control cmn_select2" name="sv_id"
                                                    id="sv_id">
                                                
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $sv_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($sv->id); ?>"><?php echo e($sv->id.'-'.$sv->aemp_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">SR ID<span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" id="sr_usnm" placeholder="Staff Id" name="staff_id">
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                <div class="form-row col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">Site Code(From)<span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" id="site_code" placeholder="Site Code" name="site_code">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">Site Code(To) <span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" id="site_code2" placeholder="Site Code" name="site_code_2">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">Order No<span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" id="ordm_ornm" placeholder="Order No" name="ordm_ornm">
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                <div class="form-row col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">Trip No<span class="required"></span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg" id="trip_no" placeholder="Trip No" name="ordm_ornm">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12" >
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">Start Date(Trip)<span class="required">*</span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg"
                                                    name="start_date"
                                                    id="start_date"  value="<?php echo date('Y-m-d'); ?>"
                                                    autocomplete="off"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                        <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                                for="start_date">End Date(Trip)<span class="required">*</span>
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="text" class="form-control in_tg"
                                                    name="end_date"
                                                    id="end_date" value="<?php echo date('Y-m-d'); ?>"
                                                    autocomplete="off"/>
                                        </div>
                                         
                                    </div>
                                </div>
                                <div class="form-row col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12" >
                                        <label class="control-label col-md-2 col-sm-2 col-xs-12"
                                                for="start_date">
                                        </label>
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <input type="radio" id="html" name="rpt_type" value="1" checked>
                                            <label for="html">Trip Summary</label>
                                            <input type="radio" id="lab1" name="rpt_type" value="2" style="margin-left:15px;"> 
                                            <label for="lab1">Summary & Details</label>
                                        </div>
                                    </div>                                           
                                </div>
                                <div class="form-row col-md-12 col-sm-12 col-xs-12">
                                            <!-- <a href="https://wa.me/60162271734" target="_blank"
                                                           class="request_report_check"><img src="<?php echo e(asset('/theme/image/whatsapp.png')); ?>"></a> -->
                                        <div class=" form-group col-md-4 col-sm-4">
                                            <a href="#" onclick="getRequestedReport()" style="text-decoration: underline;color:blue;"></a>
                                        </div>
                                        <div class=" form-group col-md-2 col-sm-2 col-xs-12 col-md-offset-6 col-sm-offset-6">
                                            <button class="btn btn-success btn-block" type="submit" onclick="filterData()">Show</button>
                                        </div> 
                                </div>

                            <!-- </div> -->
                        
                            <!-- end field -->
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


                            <div class="x_title">
                                <h3 style="text-align:center;">Trip Details</h3>
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="block_order_report" style="width:100%;overflow-x:auto;" style="height:600px;overflow: auto;">
                                <a href="#"
                                    onclick="exportTableToExcel(this,'TRIP_DETAILS_<?php echo date('Y_m_d'); ?>.xls','block_data')"
                                    class="btn btn-primary"
                                    id="employee_sales_traking_report_slgp" style="float:right;">Export
                                </a>
                                <table class="table table-responsive" id="block_data" border="1" style="overflow-x: auto; overflow-y:auto; border-collapse: collapse;">
                                    <thead id="tbl_head">
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
    <script type="text/javascript">
        $("#start_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });
        $("#end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true
        });
        $('.cmn_select2').select2();

        function filterData() {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var emp_id = $("#sr_usnm").val();
            var site_code = $("#site_code").val();
            var site_code2 = $("#site_code2").val();
            var ordm_ornm = $("#ordm_ornm").val();
            var trip_no = $("#trip_no").val();
            var sv_id = $("#sv_id").val();
            var dlrm_id = $("#dlrm_id").val();
            var rpt_type = $("input[type='radio'][name=rpt_type]:checked").val();
            var _token = $("#_token").val();
            if(site_code2==''){
                site_code2=site_code;
            }
            $('#tbl_head').empty();
            $('#cont').empty();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/report/trip-details",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    sr_usnm: emp_id,
                    site_code: site_code,
                    site_code2: site_code2,
                    ordm_ornm: ordm_ornm,
                    trip_no: trip_no,
                    rpt_type: rpt_type,
                    sv_id: sv_id,
                    dlrm_id: dlrm_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    if(rpt_type==20){
                        for(var i=0;i<data.length;i++){
                            html+='<tr><td colspan="15"></td></tr><tr>'+
                                    '<th></th>'+
                                    '<th> SL</th>'+
                                    '<th> TRIP_NO</th>'+
                                    '<th> TRIP_DATE</th>'+
                                    '<th> TOTL_INVOICE</th>'+
                                    '<th> DEPO_NAME</th>'+
                                    '<th> DM_ID</th>'+
                                    '<th> DM_NAME</th>'+
                                    '<th> ORDER_AMNT</th>'+
                                    '<th> INV_AMNT</th>'+
                                    '<th> RTAN_AMNT</th>'+
                                    '<th> DELI_AMNT</th>'+
                                    '<th> COLL_AMNT</th>'+
                                    
                                    '<th> CRED_AMNT</th>'+
                                    '<th> TRIP_STATUS</th></tr>';
                            html+='<tr><td></td>'+
                                    '<td>'+ count +'</td>'+
                                    '<td>'+ data[i].TRIP_NO +'</td>'+
                                    '<td>'+ data[i].TRIP_DATE +'</td>'+
                                    '<td>'+ data[i].C_INVOICE +'</td>'+
                                    '<td>'+ data[i].DLRM_NAME +'</td>'+
                                    '<td>'+ data[i].DM_CODE +'</td>'+
                                    '<td>'+ data[i].DM_NAME +'</td>'+
                                    '<td>'+ data[i].ORDD_AMNT +'</td>'+
                                    '<td>'+ data[i].INV_AMNT +'</td>'+
                                    '<td>'+ data[i].RTAN_AMNT +'</td>'+
                                    '<td>'+ data[i].DELV_AMNT +'</td>'+
                                    '<td>'+ data[i].COLLECTION_AMNT +'</td>'+
                                   
                                    '<td>'+ data[i].CRED_AMNT +'</td>'+
                                    '<td>'+ data[i].TRIP_STAT +'</td></tr><tr><td colspan="15"></td></tr>';
                            var details=data[i].INVOICE_DATA;

                            html+='<tr style="font-size:10px!important;">'+
                                    '<th> SL</th>'+
                                    '<th> ORDER_NO</th>'+
                                    '<th> ORDER_DATE</th>'+
                                    '<th> DELI_DATE</th>'+
                                    '<th> IBS_INVOICE</th>'+
                                    '<th> SR_ID</th>'+
                                    '<th> SR_NAME</th>'+
                                    '<th> OUTLET_CODE</th>'+
                                    '<th> ORDER_AMNT</th>'+
                                    '<th> INV_AMNT</th>'+
                                    '<th> DELI_AMNT</th>'+
                                    '<th> COLL_AMNT</th>'+
                                    '<th> RTAN_AMNT</th>'+
                                    '<th> CRED_AMNT</th>'+
                                    '<th> DELI_STATUS</th>'+
                                    '</tr>';
                            var count1=1;
                            for(var j=0;j<details.length;j++){
                                html+='<tr style="font-size:9px!important;">'+
                                        '<td>'+count1+'</td>'+
                                        '<td>'+details[j].ORDM_ORNM+'</td>'+
                                        '<td>'+details[j].ORDM_DATE+'</td>'+
                                        '<td>'+details[j].ORDM_DRDT+'</td>'+
                                        '<td>'+details[j].IBS_INVOICE+'</td>'+
                                        '<td>'+details[j].SR_ID+'</td>'+
                                        '<td>'+details[j].SR_NAME+'</td>'+
                                        '<td>'+details[j].SITE_CODE+'</td>'+
                                        '<td>'+details[j].ORDD_AMNT+'</td>'+
                                        '<td>'+details[j].INV_AMNT+'</td>'+
                                        '<td>'+details[j].DELV_AMNT+'</td>'+
                                        '<td>'+details[j].COLLECTION_AMNT+'</td>'+
                                        '<td>'+details[j].RTAN_AMNT+'</td>'+
                                        '<td>'+details[j].CRED_AMNT+'</td>'+
                                        '<td>'+details[j].DELIVERY_STATUS_NAME+'</td>'+
                                        '</tr>';
                                count1++;
                            }
                            count++;
                            }
                    }
                    else if(rpt_type==1){
                        html+='<tr>'+
                                    '<th> SL</th>'+
                                    '<th> TRIP_NO</th>'+
                                    '<th> TRIP_DATE</th>'+
                                    '<th> DEPO_NAME</th>'+
                                    '<th> DM_ID</th>'+
                                    '<th> DM_NAME</th>'+
                                    '<th> TOTAL_SR</th>'+
                                    '<th> TOTAL_INVOICE</th>'+
                                    '<th> NO_DELIVERY</th>'+
                                    '<th> ORDER_AMNT</th>'+
                                    '<th> INV_AMNT</th>'+
                                    '<th> RTAN_AMNT</th>'+
                                    '<th> DELI_AMNT</th>'+
                                    '<th> DISCOUNT</th>'+
                                    '<th> GRV-GD</th>'+
                                    '<th> GRV-BAD</th>'+
                                    '<th> COLL_AMNT</th>'+
                                    '<th> CASH</th>'+
                                    '<th> CHEQUE</th>'+
                                    '<th> ONLINE</th>'+
                                    
                                    '<th> CRED_AMNT</th>'+
                                    
                                    '<th> TRIP_STATUS</th></tr>';
                        for(var i=0;i<data.length;i++){
                            html+='<tr>'+
                                    '<td>'+ count +'</td>'+
                                    '<td>'+ data[i].TRIP_NO +'</td>'+
                                    '<td>'+ data[i].TRIP_DATE +'</td>'+
                                    '<td>'+ data[i].DEPOT_NAME +'</td>'+
                                    '<td>'+ data[i].DM_ID +'</td>'+
                                    '<td>'+ data[i].DM_NAME +'</td>'+
                                    '<td>'+ data[i].SR_NUM +'</td>'+
                                    '<td>'+ data[i].INVOICE_NUM +'</td>'+
                                    '<td>'+ data[i].DELI_NUM +'</td>'+
                                    '<td>'+ data[i].ORDD_AMNT +'</td>'+
                                    '<td>'+ data[i].INV_AMNT +'</td>'+
                                    '<td>'+ data[i].RTAN_AMNT +'</td>'+
                                    '<td>'+ data[i].DELV_AMNT +'</td>'+
                                    '<td>'+ data[i].DISCOUNT +'</td>'+
                                    '<td>'+ data[i].GD_GRV +'</td>'+
                                    '<td>'+ data[i].BAD_GRV +'</td>'+
                                    '<td>'+ data[i].COLLECTION_AMNT +'</td>'+
                                    '<td>'+ data[i].Cash +'</td>'+
                                    '<td>'+ data[i].Cheque +'</td>'+
                                    '<td>'+ data[i].Online +'</td>'+
                                    
                                    '<td>'+ data[i].CRED_AMNT +'</td>'+
                                    
                                    '<td>'+ data[i].TRIP_STAT +'</td></tr>';
                            count++;
                        }
                    }
                    else if(rpt_type==2){
                        html+='<tr>'+
                                    '<th> SL</th>'+
                                    '<th> TRIP_NO</th>'+
                                    '<th> TRIP_DATE</th>'+
                                    '<th> ORDER_NO</th>'+
                                    '<th> DEPO_NAME</th>'+
                                    '<th> DM_ID</th>'+
                                    '<th> DM_NAME</th>'+
                                    '<th> SR_ID</th>'+
                                    '<th> SR_NAME</th>'+
                                    '<th> SITE_CODE</th>'+
                                    '<th> SITE_NAME</th>'+
                                    '<th> ORDER_AMNT</th>'+
                                    '<th> INV_AMNT</th>'+
                                    '<th> RTAN_AMNT</th>'+
                                    '<th> DELI_AMNT</th>'+
                                    '<th> DISCOUNT</th>'+
                                    '<th> GRV-GD</th>'+
                                    '<th> GRV-BAD</th>'+
                                    '<th> COLL_AMNT</th>'+
                                    '<th> CASH</th>'+
                                    '<th> CHEQUE</th>'+
                                    '<th> ONLINE</th>'+
                                    
                                    '<th> CRED_AMNT</th>'+
                                    '<th> ORDER_STATUS</th>'+
                                    
                                    '<th> TRIP_STATUS</th></tr>';
                        for(var i=0;i<data.length;i++){
                            html+='<tr>'+
                                    '<td>'+ count +'</td>'+
                                    '<td>'+ data[i].TRIP_NO +'</td>'+
                                    '<td>'+ data[i].TRIP_DATE +'</td>'+
                                    '<td>'+ data[i].ORDM_ORNM +'</td>'+
                                    '<td>'+ data[i].DEPOT_NAME +'</td>'+
                                    '<td>'+ data[i].DM_ID +'</td>'+
                                    '<td>'+ data[i].DM_NAME +'</td>'+
                                    '<td>'+ data[i].AEMP_USNM +'</td>'+
                                    '<td>'+ data[i].AEMP_NAME +'</td>'+
                                    '<td>'+ data[i].SITE_CODE +'</td>'+
                                    '<td>'+ data[i].SITE_NAME +'</td>'+
                                    '<td>'+ data[i].ORDD_AMNT +'</td>'+
                                    '<td>'+ data[i].INV_AMNT +'</td>'+
                                    '<td>'+ data[i].RTAN_AMNT +'</td>'+
                                    '<td>'+ data[i].DELV_AMNT +'</td>'+
                                    '<td>'+ data[i].DISCOUNT +'</td>'+
                                    '<td>'+ data[i].GD_GRV +'</td>'+
                                    '<td>'+ data[i].BAD_GRV +'</td>'+
                                    '<td>'+ data[i].COLLECTION_AMNT +'</td>'+
                                    '<td>'+ data[i].Cash +'</td>'+
                                    '<td>'+ data[i].Cheque +'</td>'+
                                    '<td>'+ data[i].Online +'</td>'+
                                    
                                    '<td>'+ data[i].CRED_AMNT +'</td>'+                                   
                                    '<td>'+ data[i].ORDER_STAT +'</td>'+                                   
                                    '<td>'+ data[i].TRIP_STAT +'</td></tr>';
                            count++;
                        }
                    }
                    
                    $("#cont").append(html)
                },error:function(error){
                    console.log(error);
                    $('#ajax_load').css("display", "none");
                    
                }
            });
        }
        function exportTableToExcel(elem,filename, tableId){
            var BOM = "\uFEFF";
            var table=document.getElementById(tableId);
            var html = table.outerHTML;
            console.log(url);
            var url = 'data:application/vnd.ms-excel,' + encodeURI(BOM+html); // Set your html table into url 
            elem.setAttribute("href", url);
            $(elem).attr("download",filename);
            return false;
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/miscellaneous/TripSummary/trip_summary_report.blade.php ENDPATH**/ ?>