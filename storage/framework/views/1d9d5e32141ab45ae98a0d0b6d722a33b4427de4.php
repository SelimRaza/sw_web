

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="<?php echo e(URL::to('/')); ?>"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li >
                            <strong>All Thana</strong>
                        </li>
                    </ol>
                </div>
                <form action="<?php echo e(URL::to('/thana')); ?>" method="get">
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
                    <div class="x_panel">
                        <div class="x_title">
                            <?php if($permission->wsmu_crat): ?>

                                <a class="btn btn-success btn-sm" href="<?php echo e(URL::to('/thana/create')); ?>"><span
                                            class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                        New</b></a>

                                <button class="btn btn-danger btn-sm" onclick="exportTableToCSV('thana_master<?php echo date('Y_m_d'); ?>.csv','datatable')"
                                        style="float: right"><span
                                            class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                        File</b></button>

                            <?php endif; ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <?php echo e($thana->appends(Request::only('search_text'))->links()); ?>

                            <table id="datatable" class="table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Thana ID</th>
                                    <th>Name</th>
                                    
                                    <th>Code</th>
                                    <th>District</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $thana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $thana1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="tbl_body_gray">
                                        <td class="cell_left_border"><?php echo e($index+1); ?></td>
                                        <td><?php echo e($thana1->id); ?></td>
                                        <td><?php echo e($thana1->than_name); ?></td>
                                        <td><?php echo e($thana1->than_code); ?></td>
                                        <td><?php echo e($thana1->district()->dsct_name); ?></td>
                                        <td>
                                            <?php if($permission->wsmu_delt): ?>
                                                <form style="display:inline"
                                                      action="<?php echo e(route('thana.destroy',$thana1->id)); ?>"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input class="btn btn-round btn-xs"
                                                           style="color:white; background-color: <?php echo $thana1->lfcl_id == 1 ? '#06993a' : '#9f0e35'?>"
                                                           type="submit"
                                                           value="<?php echo $thana1->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge"
                                                      style="background-color: <?php echo $thana1->lfcl_id == 1 ? '#06993a' : '#9f0e35'?>"><?php echo $thana1->lfcl_id == 1 ? 'Active' : 'Inactive'?></span>
                                            <?php endif; ?>


                                        </td>
                                        <td>
                                            <?php if($permission->wsmu_read): ?>
                                                <a href="<?php echo e(route('thana.show',$thana1->id)); ?>"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View
                                                </a>&nbsp;|&nbsp;
                                            <?php endif; ?>
                                            <?php if($permission->wsmu_updt): ?>
                                                <a href="<?php echo e(route('thana.edit',$thana1->id)); ?>"
                                                   class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>

                                        </td>
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
    <script type="text/javascript">
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };


        function exportTableToCSV(filename,tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#'+tableId+'  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;
            csvFile = new Blob([csv], {type: "text/csv"});
            downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/master_data/thana/index.blade.php ENDPATH**/ ?>