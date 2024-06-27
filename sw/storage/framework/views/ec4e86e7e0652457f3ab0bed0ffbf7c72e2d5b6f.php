

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

                <div class="title_right">
                    
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itcg_id">Category
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="itcg_id" id="itcg_id"
                                            onchange="getSubCategory(this.value)">
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = $category_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cat->id); ?>"><?php echo e(ucfirst($cat->itcg_name)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">Sub Category
                                           
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="itsg_id" id="itsg_id"
                                            >

                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">Item Code
                                           
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                </div>
                            </div>

                            
                        </div>
                        <div  class="col-md-3 col-sm-3 col-xs-12">
                            <button type="submit" class="btn btn-success" onclick="filterData()">Search</button>
                            <a onclick="exportTableToCSV('sku_<?php echo date('Y_m_d'); ?>.csv')"
                                    class="btn btn-success">Export CSV File
                            </a>
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

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Unit</th>
                                    <th>Excise</th>
                                    <th>Vat</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
    <script type="text/javascript">
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        var user_name = $("#user_name").val();
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        }
        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");
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

        function filterData() {
            let item_code = $("#item_code").val();
            let itcg_id = $("#itcg_id").val();
            let itsg_id = $("#itsg_id").val();
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/mapping-sku",
                data: {
                    item_code: item_code,
                    itcg_id: itcg_id,
                    itsg_id: itsg_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    let html = '';
                    let count = 1;
                    for (let i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].amim_code + '</td>' +
                            '<td>' + data[i].amim_name + '</td>' +
                            '<td>' + data[i].itcg_name + '</td>' +
                            '<td>' + data[i].itsg_name + '</td>' +
                            '<td>' + data[i].amim_duft + '</td>' +
                            '<td>' + data[i].amim_pexc + '</td>' +
                            '<td>' + data[i].amim_pvat + '</td>' +
                            '<td class="lfcl">' + data[i].lfcl_name + '</td>';
                        html += "<td><a  href='<?php echo e(URL::to('/')); ?>/sku/" + data[i].id +'/edit'+"' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i>Edit</a>";
                        html += "<button  id="+data[i].id+" lfcl_id="+data[i].lfcl_id+" onclick='itemLfclChange(this)' class='btn btn-danger btn-xs'><i class='fa fa-pencil'></i>Actv/Inactv</button></td></tr>";
                            
                        count++;
                    }

                    $("#cont").append(html)


                },error:function(error){
                    swal.fire({
                        icon:"error",
                        text:"Something Went Wrong !!!",
                    })
                }
            });
        }
        function getSubCategory(cat_id) {
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/getSubCategory/"+cat_id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select</option>';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].id + '">' + data[i].itsg_name+'</option>';
                    }
                    $('#itsg_id').empty();
                    $('#itsg_id').append(html);
                    
                }
            });
        }
        function itemLfclChange(v) {
            let id=$(v).attr('id');
            let lfcl_id=$(v).attr('lfcl_id');
            $.ajax({
                type: "GET",
                url: "<?php echo e(URL::to('/')); ?>/itemLfclChange/"+id+"/"+lfcl_id,
                cache: false,
                dataType: "json",
                success: function (data) {

                    
                    swal.fire({
                        icon:'success',
                        text:'Item lifecycle changed successfully',
                    })
                    
                },
                error:function(error){
                    $('#ajax_load').css("display", "none");
                    swal.fire({
                        icon:'error',
                        text:'Something went wrong!!!',
                    })
                    
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Mapping/sku.blade.php ENDPATH**/ ?>