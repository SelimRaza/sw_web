

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
                            <strong>New Reports</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right"></div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo e(csrf_token()); ?>">

                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                            <div class="form-group col-md-6">
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Select User</label>
                                    <select class="form-control cmn_select2" name="aemp_id" id="aemp_id">
                                        <option value="">Select user</option>
                                        <?php $__currentLoopData = $employeies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($employee->id); ?>"><?php echo e(ucfirst($employee->aemp_name)); ?>

                                                (<?php echo e($employee->aemp_usnm); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">

                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Select Year</label>
                                    <select class="form-control cmn_select2" name="year" id="year">
                                        <option value="">Select year</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = 0; $i < 2; $i++) {
                                            $year = $currentYear - $i;
                                            echo "<option value=\"$year\">$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Other filters -->
                        </div>
                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>
                            <button onclick="exportTableToCSV('activity_summary_report_<?php echo e(date('Y_m_d')); ?>.csv')"
                                    class="btn btn-success">Export CSV File
                            </button>
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
                        <div class="x_title">
                            <h1>User wise Order Tracking Report</h1>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                       aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="#">Settings 1</a></li>
                                        <li><a href="#">Settings 2</a></li>
                                    </ul>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");

            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                csv.push(row.join(","));
            }

            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile = new Blob([csv], {type: "text/csv"});
            var downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
        function filterData() {
            let site_code = $("#site_code").val();
            let acmp_id = $("#acmp_id").val();
            let aemp_id = $("#aemp_id").val();
            let year = $("#year").val();
            let slgp_id = $("#slgp_id").val();
            let otcg_id = $("#otcg_id").val();
            let chnl_id = $("#chnl_id").val();
            let scnl_id = $("#scnl_id").val();
            let _token = $("#_token").val();

            if (site_code == '' && slgp_id == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Please provide site code or select at least group!!!',
                });
                return false;
            }

            $('#ajax_load').css("display", "block");

            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/NewReport/filter3",
                data: {
                    site_code: site_code,
                    acmp_id: acmp_id,
                    aemp_id: aemp_id,
                    year: year,
                    slgp_id: slgp_id,
                    otcg_id: otcg_id,
                    chnl_id: chnl_id,
                    scnl_id: scnl_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");

                    let currentDate = new Date();
                    let currentYear = currentDate.getFullYear();
                    let actualCurrentMonth = currentDate.getMonth() + 1; // getMonth() returns 0-11, so add 1

                    // Determine the number of months to display
                    let displayMonths = (year == currentYear) ? actualCurrentMonth : 12;

                    // Generate headers dynamically based on the provided headers array from the backend
                    let headers = '';
                    for (let k = 0; k < data.headers.length; k++) {
                        headers += `<th>${data.headers[k]}</th>`;
                    }
                    $('.projects thead').html(`<tr>${headers}</tr>`);

                    // Generate rows dynamically based on the data array from the backend
                    let rowsHTML = '';
                    for (let i = 0; i < data.data.length; i++) {
                        rowsHTML += '<tr>';
                        rowsHTML += `<td>${data.data[i].sr_id}</td>`;
                        rowsHTML += `<td>${data.data[i].sr_name}</td>`;
                        rowsHTML += `<td>${data.data[i].outlet_code}</td>`;
                        rowsHTML += `<td>${data.data[i].outlet_name}</td>`;
                        rowsHTML += `<td>${data.data[i].item_code}</td>`;
                        rowsHTML += `<td>${data.data[i].item_name}</td>`;
                        // Quantity and Amount for each month
                        for (let j = 1; j <= displayMonths; j++) {
                            let month = j < 10 ? '0' + j : j; // Format month as '01', '02', ..., '12'
                            let qty = data.data[i][`Qty_${year}-${month}`] || 0;
                            let amt = data.data[i][`Amt_${year}-${month}`] || 0;
                            rowsHTML += `<td>${qty}</td>`;
                            rowsHTML += `<td>${amt}</td>`;
                        }
                        rowsHTML += '</tr>';
                    }
                    $('.projects tbody').html(rowsHTML);
                }
            });
        }

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/DataExport/index.blade.php ENDPATH**/ ?>