

<?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">
        <div class="">
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
                        <div class="x_content">
                         <?php if(auth()->guard()->check()): ?>
                                    <div class="col-md-2">
                                        <div class="menu_div" style=" background-color:#6491EA !important;color:white !important;">
                                            <a  href="<?php echo e(route('dashboard')); ?>" style="font-size:18px!important;color:white !important;">Dashboard<span
                                                    class=""></span></a>
                                        </div>
                                        
                                    </div>
                            <!-- <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-2">
                                        <div class="menu_div" style=" background-color:#6491EA !important;color:white !important;">
                                        <?php if($item->id==9): ?>
                                        <i class="<?php echo e($item->wmnu_icon); ?>"></i>
                                        <a href="<?php echo e(URL::to('/report')); ?>" style="font-size:18px!important;;color:white !important;"> <?php echo e($item->wmnu_name); ?> <span
                                                    ></span></a>
                                        <?php else: ?>
                                            
                                            <?php if(count($item->get_user_submenu())>0): ?>
                                            <i class="<?php echo e($item->wmnu_icon); ?>"></i>
                                                <a  href="#" style="font-size:18px!important;color:white !important;" onclick="openModal('<?php echo e($item->get_user_submenu()); ?>','<?php echo e($item->wmnu_name); ?>');"> <?php echo e($item->wmnu_name); ?> <span
                                                        class=""></span></a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </div>
                                        
                                    </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> -->
                            <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($item->get_user_submenu())>0): ?>
                                    <?php if($item->id==9): ?>
                                    <div class="col-md-2">
                                        <div class="menu_div" style=" background-color:#6491EA !important;color:white !important;">
                                    
                                        <i class="<?php echo e($item->wmnu_icon); ?>"></i>
                                        <a href="<?php echo e(URL::to('/report')); ?>" style="font-size:18px!important;;color:white !important;"> <?php echo e($item->wmnu_name); ?> <span
                                                    ></span></a>
                                        </div> 
                                    </div>
                                    <?php else: ?>
                                    <div class="col-md-2">
                                        <div class="menu_div" style=" background-color:#6491EA !important;color:white !important;">
                                    
                                        <i class="<?php echo e($item->wmnu_icon); ?>"></i>
                                                    <a  href="#" style="font-size:18px!important;color:white !important;" onclick="openModal('<?php echo e($item->get_user_submenu()); ?>','<?php echo e($item->wmnu_name); ?>');"> <?php echo e($item->wmnu_name); ?> <span
                                                            class=""></span></a>
                                        </div> 
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                            
                        </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Modal Title</h2>
            <div id="modal-data">
                <!-- Data will be displayed here -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalSubmen" role="dialog">
        <div class="modal-dialog" style="width:50%;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center" id="wmnu_name"></h4>
                </div>
                <div class="modal-body">
                    <div id="modalSubmen_body"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

</div>
<style>
/* Style the modal */


/* Rest of your CSS styles */
/* ... */


</style>
<script>
 function openModal1(data) {
    const modal = document.getElementById("myModal");
    const modalData = document.getElementById("modal-data");
    let html = '<ul class="nav child_menu">';
    var data =JSON.parse(data);
    for (let i = 0; i < data.length; i++) {
        var url = `<?php echo e(URL::to('/')); ?>/${data[i].wsmn_wurl}`;
        html += `<li><a href="${url}">${data[i].wsmn_name}</a></li>`;
    }
    html += '</ul>';
    $('#modal-data').html(html);
    modal.style.display = "block";
}
function openModal(data,menu_name) {
    console.log(data)
    $("#modalSubmen").modal({backdrop: false});
    $('#modalSubmen').modal('show');
    $('#wmnu_name').html(menu_name);
    let html = '';
    var data =JSON.parse(data);
    for (let i = 0; i < data.length; i++) {
        var url = `<?php echo e(URL::to('/')); ?>/${data[i].wsmn_wurl}`;
        html += `<ol><img src="<?php echo e(asset('/theme/arrow.png')); ?>"/> <a href="${url}" target="_blank"> ${data[i].wsmn_name}</a></ol>`;
    }
    html += '';
    console.log(html)
    $('#modalSubmen_body').html(html);
}




</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/index.blade.php ENDPATH**/ ?>