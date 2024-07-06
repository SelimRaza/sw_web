<div class="top_nav" style="margin-left: 19px;
margin-right: 19px;">
    <div class="nav_menu" style=" background-color:#6491EA!important;color:white !important;">

        <nav>
            <div class="nav toggle">
                <!-- <a id="menu_toggle"><i class="fa fa-bars"></i></a> -->
                <a  href="<?php echo e(url('/home')); ?>" style="color:white !important;">Home</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <?php if(Auth::guest()): ?>
                    <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
                <?php else: ?>
                    <li class="">
                        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                           aria-expanded="false">
                            <?php if( Auth::user()->employee()->aemp_picn==''): ?>
                                <img src="<?php echo e(asset("theme/production/images/img.jpg")); ?>" alt=""><?php echo e(Auth::user()->employee()->aemp_name); ?>

                            <?php else: ?>
                                <img src="https://images.sihirbox.com/<?php echo e(Auth::user()->employee()->aemp_picn); ?>"
                                     alt=""><?php echo e(Auth::user()->employee()->aemp_name); ?>

                            <?php endif; ?>
                            <span class=" fa fa-angle-down"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-usermenu pull-right">
                            <li>
                                <a href="<?php echo e(URL::to('/employee/profileEdit/')); ?>">My Profile</a>
                            </li>
                            <li>
                                <a href="<?php echo e(URL::to('employee/'.Auth::user()->id.'/passChange')); ?>">Change Password</a>
                            </li>
                            <li><a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i
                                            class="fa fa-sign-out pull-right"></i> Log Out</a>
                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST"
                                      style="display: none;">
                                    <?php echo e(csrf_field()); ?>

                                </form>
                            </li>
                        </ul>
                    </li>
                    
                <?php endif; ?>


            </ul>
        </nav>
        <div>
            <!-- <h1 id="txt" class="text-center" style="text-align:center!important;"><?php echo e(date("h:i:sA")); ?></h1> -->
            
            
        </div>
            
          
    </div>
</div><?php /**PATH /home1/test/sw/resources/views/theme/top_nav_menu.blade.php ENDPATH**/ ?>