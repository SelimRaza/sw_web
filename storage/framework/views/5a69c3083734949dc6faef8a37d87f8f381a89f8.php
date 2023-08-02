

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
                            <strong>All Order</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Date Range</label>

                                    <div class="input-group date-picker input-daterange">


                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>

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
                        <form id="demo-form2" data-parsley-validate
                              class="form-horizontal form-label-left"
                              action="<?php echo e(URL::to('order_report/pushToRoutePlan')); ?>" enctype="multipart/form-data"
                              method="post">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('POST')); ?>


                            <div class="x_title">
                                <h1>Maintain Order</h1>

                                <ul class="nav navbar-left panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        <button type="submit" class="btn btn-success" name="sr_attendence">Push for
                                            RoutePlan
                                        </button>
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
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select_all"/></th>
                                        <th>S/L</th>
                                        <th>Order Id</th>
                                        <th>Order Amount</th>
                                        <th>Order Date</th>
                                        <th>Order Date Time</th>
                                        <th>User Name</th>
                                        <th>Emp Name</th>
                                        <th>Outlet id</th>
                                        <th>Outlet Code</th>
                                        <th>Outlet Name</th>
                                        <th>Order Type</th>
                                        <th>Status</th>
                                        <th colspan="3">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">

                                    </tbody>
                                </table>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
        $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                //change ".checkbox" checked status
            });
        });

        $('.checkbox').change(function () { //".checkbox" change
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false
            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });

        function filterData() {
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            var emp_id = $("#emp_id").val();
            var _token = $("#_token").val();
            console.log(start_date + end_date);
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/order_report/filterOrderSummary",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    emp_id: emp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    //onsole.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;

                    for (var i = 0; i < data.length; i++) {
                        var readonly1 = '';
                        if (data[i].status_id != 1) {
                            readonly1 = 'disabled readonly'
                        }
                        html += '<tr>' +
                            "<td><input " + readonly1 + "  class='checkbox' type='checkbox' name='so_id[]' value='" + data[i].so_id + "'></td>" +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].order_id + '</td>' +
                            '<td>' + data[i].order_amount + '</td>' +
                            '<td>' + data[i].order_date + '</td>' +
                            '<td>' + data[i].order_date_time + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].emp_name + '</td>' +
                            "<td>" + data[i].site_id + "</td>" +
                            "<td>" + data[i].site_code + "</td>" +
                            "<td>" + data[i].site_name + "</td>" +
                            "<td>" + data[i].order_type + "</td>" +
                            "<td>" + data[i].status_name + "</td>";
                        html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/printer/order/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Order Print </a></td>";
                        if (data[i].status_id == 11) {
                            html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/printer/salesInvoice/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Sales Invoice Print </a></td>";
                        }
                        if(data[i].status_id==1){
                            html+='<td><a  href="#" class="btn btn-danger btn-xs" value="'+data[i].order_id+'" onclick="cancelOrder(this)"><i class="fa fa-pencil"></i> Cancel Order </a></td>';
                        }
                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
        function cancelOrder(order_ornm){
           
           var id=$(order_ornm).attr('value');
           var start_date=$('#start_date').val();
           var end_date=$('#end_date').val();

           Swal.fire({
            title: 'Are you sure?',
            text: "About cancelling this Order!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes,'
            }).then((result) => {
            if (result.isConfirmed) {
                $('#ajax_load').css("display", "block");
            $.ajax({
                type:"GET",
                url:"<?php echo e(URL::to('/')); ?>/cancelOrder/"+id+'/'+start_date+'/'+end_date,
                cache:"false",
                success:function(data){
                    console.log(data);
                    $('#ajax_load').css("display", "none");                     
                    $("#cont").empty();
                    var html = '';
                    var count = 1;

                    for (var i = 0; i < data.length; i++) {
                        var readonly1 = '';
                        if (data[i].status_id != 1) {
                            readonly1 = 'disabled readonly'
                        }
                        html += '<tr>' +
                            "<td><input " + readonly1 + "  class='checkbox' type='checkbox' name='so_id[]' value='" + data[i].so_id + "'></td>" +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].order_id + '</td>' +
                            '<td>' + data[i].order_amount + '</td>' +
                            '<td>' + data[i].order_date + '</td>' +
                            '<td>' + data[i].order_date_time + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].emp_name + '</td>' +
                            "<td>" + data[i].site_id + "</td>" +
                            "<td>" + data[i].site_code + "</td>" +
                            "<td>" + data[i].site_name + "</td>" +
                            "<td>" + data[i].order_type + "</td>" +
                            "<td>" + data[i].status_name + "</td>";
                        html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/printer/order/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Order Print </a></td>";
                        if (data[i].status_id == 11) {
                            html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/printer/salesInvoice/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Sales Invoice Print </a></td>";
                        }
                        if(data[i].status_id==1){
                            html+='<td><a  href="#" class="btn btn-danger btn-xs" value="'+data[i].order_id+'" onclick="cancelOrder(this)"><i class="fa fa-pencil"></i> Cancel Order </a></td>';
                        }
                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)
                },
                error:function(error){
                    $('#ajax_load').css("display", "none");
                   
                    console.log(error);
                }
            });

            Swal.fire(
            'Order Cancelled',
            'success'
            )
            }
            })


           
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/Depot/OrderReport/order_summary.blade.php ENDPATH**/ ?>