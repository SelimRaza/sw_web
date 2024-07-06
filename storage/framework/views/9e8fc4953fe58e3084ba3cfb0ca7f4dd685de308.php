

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
                            <strong> SKU</strong>
                        </li>
                    </ol>
                </div>

                
                <div class="title_right">
                    <a href="<?php echo e(URL::to('/sku-group-mapping')); ?>" class="btn btn primary" type="submit" style="text-decoration:underline;color:darkred;" target="_blank"> Sku-Group</a>
                    <a href="<?php echo e(URL::to('/sku-plmt-mapping')); ?>" class="btn btn primary" type="submit" style="text-decoration:underline;color:darkred;" target="_blank"> Sku-PriceList</a>
                </div>
                
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                            <div class="form-group col-md-4">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="itsg_id">Item Code
                                           
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                </div>
                            </div>

                            
                        </div>
                        <div  class="col-md-3 col-sm-3 col-xs-12">
                            <button type="submit" class="btn btn-success" onclick="filterData()">Search</button>
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
                <div class="col-md-12  col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <h3 class="text-center">SKU-Group</h3>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                   
                                        <tr>
                                            <th>SL</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Group Code</th>
                                            <th>Group Name</th>
                                            <th>Action</th>
                                        </tr>
                                    
                                    </thead>
                                    <tbody id="cont">
                                    <?php $__currentLoopData = $data1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($i+1); ?></td>
                                            <td><?php echo e($dt->amim_code); ?></td>
                                            <td><?php echo e($dt->amim_name); ?></td>
                                            <td><?php echo e($dt->slgp_code); ?></td>
                                            <td><span ><?php echo e($dt->slgp_name); ?></span></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12" id="plmt">
                                <h3 class="text-center">SKU-PriceList</h3>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>PriceList Code</th>
                                        <th>PriceList Name</th>
                                        <th>Price</th>
                                        <th>GRV</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont2">
                                    <?php $__currentLoopData = $data2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($i+1); ?></td>
                                            <td><?php echo e($dt->amim_code); ?></td>
                                            <td><?php echo e($dt->amim_name); ?></td>
                                            <td><?php echo e($dt->plmt_code); ?></td>
                                            <td></td>
                                            <td></td>
                                            <td><span ><?php echo e($dt->plmt_name); ?></span></td>
                                            <td></td>
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
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "<?php echo e(URL::to('/')); ?>/sku-mapping",
                data: {
                    item_code: item_code,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    let data1=data.data1;
                    let data2=data.data2;
                    $("#cont").empty();
                    $("#cont2").empty();
                    $('#ajax_load').css("display", "none");
                    let html = '';
                    let html2 = '';
                    let count = 1;
                    let count2 = 1;
                    for (let i = 0; i < data1.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data1[i].amim_code + '</td>' +
                            '<td>' + data1[i].amim_name + '</td>' +
                            '<td>' + data1[i].slgp_code + '</td>' +
                            '<td>' + data1[i].slgp_name + '</td>' +
                            '<td><button class="btn btn-danger btn-xs" id="'+data1[i].id+'" onclick="removeItemFromSlgp(this)">Remove</button></td>'+
                           '</tr>';
                            
                        count++;
                    }
                    for (let i = 0; i < data2.length; i++) {
                        html2 += '<tr>' +
                            '<td>' + count2 + '</td>' +
                            '<td>' + data2[i].amim_code + '</td>' +
                            '<td>' + data2[i].amim_name + '</td>' +
                            '<td>' + data2[i].plmt_code + '</td>' +
                            '<td>' + data2[i].plmt_name + '</td>' +
                            '<td>' + data2[i].pldt_tppr + '</td>' +
                            '<td>' + data2[i].pldt_tpgp + '</td>' +
                            '<td><button class="btn btn-danger btn-xs" id="'+data2[i].id+'" onclick="removeItemFromPriceList(this)">Remove</button>';
                        html2+= "<a target='_blank' href='<?php echo e(URL::to('/')); ?>/view/plmt-item/" + data2[i].amim_id + "/" + data2[i].plmt_id + "' class='btn btn-info btn-xs'><i class='fa fa-eye'>View</i>  </a></td></tr>";
                           
                            
                        count2++;
                    }

                    $("#cont").append(html)
                    $("#cont2").append(html2)


                },error:function(error){
                    console.log(error)
                    swal.fire({
                        icon:"error",
                        text:"Something Went Wrong !!!",
                    })
                }
            });
        }
       function removeItemFromPriceList(v){
            let id=$(v).attr('id');
            let obj=$(v).parent().parent();
            Swal.fire({
                icon:'question',
                title: 'Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                confirmButtonColor: "#C9302C",
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:"GET",
                    url: "<?php echo e(URL::to('/')); ?>/removeItemFromPriceList/"+id,
                    dataType: "json",
                    success:function(data){
                        obj.remove();
                    },
                    error:function(error){
                        swal.fire({
                            icon:'error',
                            text:'Something went wrong!!!'
                        })
                    }
                });
            } 
            })
            
       }
       function removeItemFromSlgp(v){
            let id=$(v).attr('id');
            let obj=$(v).parent().parent();
            Swal.fire({
                icon:'question',
                title: 'Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                confirmButtonColor: "#C9302C",
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:"GET",
                    url: "<?php echo e(URL::to('/')); ?>/removeItemFromSlgp/"+id,
                    dataType: "json",
                    success:function(data){
                        obj.remove();
                    },
                    error:function(error){
                        swal.fire({
                            icon:'error',
                            text:'Something went wrong!!!'
                        })
                    }
                });
            } 
            })
            
       }
    </script>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/Mapping/sku_mapping.blade.php ENDPATH**/ ?>