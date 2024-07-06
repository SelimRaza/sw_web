

<?php $__env->startSection('content'); ?>

    <div class="animate form login_form">
        <section class="login-container" >
            <form class="form-horizontal form-box" method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo e(csrf_field()); ?>

                <?php if(Session::has('message')): ?>
                    <div class="alert alert-danger">
                        <strong><?php echo e(Session::get('message')); ?></strong>
                    </div>
                <?php endif; ?>
                <h1>Verification</h1>
                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                    <input id="email" type="text" class="form-control" name="email" value="<?php echo e(old('email')); ?>"
                           placeholder="Username" required autofocus>
                    <?php if($errors->has('email')): ?>
                        <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                    <?php endif; ?>
                </div>
                <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                    <input id="password" type="password" class="form-control" name="password" placeholder="Password"
                           required>
                    <?php if($errors->has('password')): ?>
                        <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                    <?php endif; ?>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary submit" style="font-family:Cursive;"> Login</button></hr>
                    <!-- <a href="<?php echo e(route('privacy_policy')); ?>" >Privacy Policy</a> -->

                </div>
                <div class="clearfix"></div>

                <div class="separator">

                    <div class="clearfix"></div>
                    <br/>


                </div>
            </form>
        </section>
    </div>
    <!-- Messenger Chat Plugin Code -->
    <div id="fb-root"></div>

    <!-- Your Chat Plugin code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "103333711879697");
      chatbox.setAttribute("attribution", "biz_inbox");
    </script>

    <!-- Your SDK code -->
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v17.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('theme.app_out', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/test/sw/resources/views/auth/login.blade.php ENDPATH**/ ?>