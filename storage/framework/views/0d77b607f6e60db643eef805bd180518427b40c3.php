<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">

        <ul class="nav side-menu">
            <?php if(auth()->guard()->check()): ?>
            <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $status=false?>
                <?php $__currentLoopData = $item->get_user_submenu(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($subitem->wmnu_id == $item->id): ?>
                            <?php $status=true?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($status): ?>
                    <li>
                        <?php if($item->id==9 || $item->id==35): ?>
                            <?php if($item->id==9): ?>
                                <a href="<?php echo e(URL::to('/report')); ?>"><i class="<?php echo e($item->wmnu_icon); ?>"></i> <?php echo e($item->wmnu_name); ?> <span
                                    ></span></a>
                            <?php else: ?>
                                <!-- <a href="<?php echo e(URL::to('/e_report')); ?>"><i class="<?php echo e($item->wmnu_icon); ?>"></i> <?php echo e($item->wmnu_name); ?> <span
                                    ></span></a> -->
                            <?php endif; ?>
                        
                        <?php else: ?>

                        <a><i class="<?php echo e($item->wmnu_icon); ?>"></i> <?php echo e($item->wmnu_name); ?> <span
                                    class="fa fa-chevron-down"></span></a>
                        <?php if($item->get_user_submenu()->count()): ?>
                            <ul class="nav child_menu">
                                <?php $__currentLoopData = $item->get_user_submenu(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($subitem->wmnu_id == $item->id): ?>
                                        <li><a href="<?php echo e(URL::to('/')); ?>/<?php echo e($subitem->wsmn_wurl); ?>"><?php echo e($subitem->wsmn_name); ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>

                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

        </ul>
    </div>

</div><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/theme/menu.blade.php ENDPATH**/ ?>