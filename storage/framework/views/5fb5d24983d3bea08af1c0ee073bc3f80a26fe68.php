<div class="profile clearfix">
  <div class="profile_pic">
      <?php if( Auth::user()->employee()->aemp_picn==''): ?>
          <img src="<?php echo e(asset("theme/production/images/img.jpg")); ?>" alt="..."
               class="img-circle profile_img">
      <?php else: ?>
          <img src="https://images.sihirbox.com/<?php echo e(Auth::user()->employee()->aemp_picn); ?>" alt="..."
               class="img-circle profile_img">
      <?php endif; ?>

  </div>
  <div class="profile_info">
    <?php if(Auth::guest()): ?>
      <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
    <?php else: ?>
      <span>Welcome,</span>
      <h2><?php echo e(Auth::user()->cont_name); ?></h2>
    <?php endif; ?>

  </div>
</div><?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/theme/profile.blade.php ENDPATH**/ ?>