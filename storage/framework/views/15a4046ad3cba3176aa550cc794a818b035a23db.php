<div class="top_nav">
    <div class="nav_menu">

        <nav>
            <div class="nav toggle">

                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
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
                                <a href="<?php echo e(URL::to('/employee/profileEdit/')); ?>"> Profile</a>
                            </li>
                            <li>
                                <a href="<?php echo e(URL::to('employee/'.Auth::user()->id.'/passChange')); ?>"> Password</a>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <span class="badge bg-red pull-right">50%</span>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li><a href="javascript:;">Help</a></li>
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
                    <li role="presentation" class="dropdown">
                        <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown"
                           aria-expanded="false">
                            <i class="fa fa-envelope-o"></i>
                            <span class="badge bg-green">6</span>
                        </a>
                        <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                            <li>
                                <a>
                                <span class="image"><img src="<?php echo e(asset("theme/production/images/img.jpg")); ?>"
                                                         alt="Profile Image"/></span>
                                    <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                                    <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                </a>
                            </li>
                            <li>
                                <a>
                                <span class="image"><img src="<?php echo e(asset("theme/production/images/img.jpg")); ?>"
                                                         alt="Profile Image"/></span>
                                    <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                                    <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                </a>
                            </li>
                            <li>
                                <a>
                                <span class="image"><img src="<?php echo e(asset("theme/production/images/img.jpg")); ?>"
                                                         alt="Profile Image"/></span>
                                    <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                                    <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                </a>
                            </li>
                            <li>
                                <a>
                                <span class="image"><img src="<?php echo e(asset("theme/production/images/img.jpg")); ?>"
                                                         alt="Profile Image"/></span>
                                    <span>
                          <span>John Smith</span>
                          <span class="time">3 mins ago</span>
                        </span>
                                    <span class="message">
                          Film festivals used to be do-or-die moments for movie makers. They were where...
                        </span>
                                </a>
                            </li>
                            <li>
                                <div class="text-center">
                                    <a>
                                        <strong>See All Alerts</strong>
                                        <i class="fa fa-angle-right"></i>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>


            </ul>
        </nav>
        <div>
            <h1 id="txt"><?php echo e(date("h:i:sA")); ?></h1></div>
            
          
    </div>
</div><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/theme/top_nav.blade.php ENDPATH**/ ?>