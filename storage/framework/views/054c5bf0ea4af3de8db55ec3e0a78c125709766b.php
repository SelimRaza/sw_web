

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
                            <strong>All Collection</strong>
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
                                <h1>Collection Maintain</h1>

                                <ul class="nav navbar-left panel_toolbox">

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
                                        <th>S/L</th>
                                        <th>Collection Id</th>
                                        <th>Collection Code</th>
                                        <th>Outlet name</th>
                                        <th>Collection by</th>
                                        <th>Verify by</th>
                                        <th>Compnay</th>
                                        <th>Date</th>
                                        <th>Verify Time</th>
                                        <th>Trip Id</th>
                                        <th>Cheque No</th>
                                        <th>Cheque Date</th>
                                        <th>Amount</th>
                                        <th>On Account</th>
                                        <th>Status</th>
                                        <th>Action</th>
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
                url: "<?php echo e(URL::to('/')); ?>/collection/filterCollection",
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
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].payment_id + '</td>' +
                            '<td>' + data[i].collection_code + '</td>' +
                            '<td>' + data[i].outlet_name + '</td>' +
                            '<td>' + data[i].collected + '</td>' +
                            '<td>' + data[i].verified_by + '</td>' +
                            '<td>' + data[i].ou_name + '</td>' +
                            "<td>" + data[i].payment_date + "</td>" +
                            "<td>" + data[i].verify_date_time + "</td>" +
                            "<td>" + data[i].trip_id + "</td>" +
                            "<td>" + data[i].cheque_no + "</td>" +
                            "<td>" + data[i].cheque_date + "</td>" +
                            "<td>" + data[i].amount + "</td>" +
                            "<td>" + data[i].on_account + "</td>" +
                            "<td>" + data[i].status + "</td>";
                        if(data[i].status_id===12){
                            html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/collection/verify/" +data[i].payment_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Collection Verify </a></td>";
                        }
                        if(data[i].status_id===1){
                            html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/collection/chequeVerify/" +data[i].payment_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> PDC Verify </a></td>";
                        }
                        if(data[i].status_id===37){
                            html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/collection/onAccountVerify/" +data[i].payment_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> On Account Matching </a></td>";
                        }
                        html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/printer/collectionPrint/" + data[i].cont_id + "/" + data[i].collection_code + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Collection Print </a></td>";
                        html += "<td><a target='_blank' href='<?php echo e(URL::to('/')); ?>/collection/maintainView/"  +data[i].payment_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Collection View </a></td>";

                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/collection/collection_maintain.blade.php ENDPATH**/ ?>