

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
                            <strong>All Target</strong>
                        </li>
                        <li class="label-success">
                            <a href="<?php echo e(URL::to('/target/upload')); ?>"> Target Upload</a>
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
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="usr">Month:</label>
                                        <input type="text" class="form-control" name="trgt_date"
                                               id="spbm_date"
                                               value="<?php echo date('Y-m'); ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="usr">Manager Id:</label>
                                        <input id="user_name" class="form-control col-md-7 col-xs-6"
                                               data-validate-length-range="6"
                                               data-validate-words="2" name="manager"
                                               value="<?php echo e(old('manager')); ?>"
                                               placeholder="user_name" required="required"
                                               type="text">
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
                                <h1>Maintain Target</h1>
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
                                        <th>Manager Id</th>
                                        <th>Manager name</th>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <th>Month Name</th>
                                        <th>QTY CTN</th>
                                        <th>Value</th>
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
        $('#spbm_date').datetimepicker({format: 'YYYY-MM'});


        function filterData() {
            var trgt_date = $("#spbm_date").val();
            var manager = $("#user_name").val();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/filterTarget",
                data: {
                    trgt_date: trgt_date,
                    manager: manager,
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
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].supervisor_name + '</td>' +
                            '<td>' + data[i].year + '</td>' +
                            '<td>' + data[i].month_id + '</td>' +
                            '<td>' + data[i].month_name + '</td>' +
                            "<td>" + data[i].initial_target_in_ctn + "</td>" +
                            "<td>" + data[i].initial_target_in_value + "</td>";
                        html += "<td><a  href='<?php echo e(URL::to('/')); ?>/target/bySalesMan/" + data[i].supervisor_id + "/" + data[i].year +"/" + data[i].month_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> View By SR </a>";
                        html += "<a  href='<?php echo e(URL::to('/')); ?>/target/byItem/" + data[i].supervisor_id + "/" + data[i].year + "/" + data[i].month_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> View By Item </a>";
                        html += "<a  href='<?php echo e(URL::to('/')); ?>/target/byCategory/" + data[i].supervisor_id + "/" + data[i].year +"/" + data[i].month_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> View By Category </a>";
                        html += '<a href="#" onclick="removeTarget('+ data[i].supervisor_id+','+ data[i].year+','+ data[i].month_id+')" class="btn btn-danger btn-xs"><i class="fa fa-pencil"></i>'+'Remove'+'</a>';

                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                },error:function(error){
                    console.log(error);
                }
            });
        }

        function removeTarget (sp_id,year,month){
            var manager = $("#user_name").val();
            Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
               
                $.ajax({
                type: "get",
                url: "<?php echo e(URL::to('/')); ?>/target/remove/"+sp_id+'/'+year+'/'+month+'/'+manager,
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
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].supervisor_name + '</td>' +
                            '<td>' + data[i].year + '</td>' +
                            '<td>' + data[i].month_id + '</td>' +
                            '<td>' + data[i].month_name + '</td>' +
                            "<td>" + data[i].initial_target_in_ctn + "</td>" +
                            "<td>" + data[i].initial_target_in_value + "</td>";
                        html += "<td><a  href='<?php echo e(URL::to('/')); ?>/target/bySalesMan/" + data[i].supervisor_id + "/" + data[i].year +"/" + data[i].month_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> View By SR </a>";
                        html += "<a  href='<?php echo e(URL::to('/')); ?>/target/byItem/" + data[i].supervisor_id + "/" + data[i].year + "/" + data[i].month_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> View By Item </a>";
                        html += "<a  href='<?php echo e(URL::to('/')); ?>/target/byCategory/" + data[i].supervisor_id + "/" + data[i].year +"/" + data[i].month_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> View By Category </a>";
                        html += '<a href="#" onclick="removeTarget('+ data[i].supervisor_id+','+ data[i].year+','+ data[i].month_id+')" class="btn btn-danger btn-xs"><i class="fa fa-pencil"></i>'+'Remove'+'</a>';

                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)
                    Swal.fire(
                        'Deleted!',
                        'Target Removed',
                        'success'
                    )


                },error:function(error){
                    console.log(error);
                }
            });
               
            }

            })
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/Target/maintain_target.blade.php ENDPATH**/ ?>