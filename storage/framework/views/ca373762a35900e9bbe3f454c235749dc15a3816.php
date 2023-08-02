<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'FMS')); ?></title>
    <link href="<?php echo e(asset("theme/vendors/bootstrap/dist/css/bootstrap.min.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("theme/vendors/font-awesome/css/font-awesome.min.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("theme/vendors/nprogress/nprogress.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("theme/build/css/custom.min.css")); ?>" rel="stylesheet">
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-12">
            <div class="col-middle">
                <div class="text-center text-center">
                    <div class="error-number"><a href="<?php echo e(URL::to('/')); ?>" class="site_title"><i class="fa fa-globe"></i> <span><?php echo e(config('app.name', 'FMS')); ?></span></a></div>
                    <h1 class="error-number">404</h1>
                    <h2>Sorry but we couldn't find this page</h2>
                    <p>This page you are looking for does not exist</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->yieldContent('content'); ?>
<script src="<?php echo e(asset("theme/vendors/jquery/dist/jquery.min.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/bootstrap/dist/js/bootstrap.min.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/fastclick/lib/fastclick.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/nprogress/nprogress.js")); ?>"></script>
<script src="<?php echo e(asset("theme//build/js/custom.min.js")); ?>"></script>
 <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBGHAvVAeNHTkWUUnGsXpbA6AK3GWbFByg&callback=initMap&libraries=&v=weekly"
      async
    ></script>
</body>
</html>
<?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/theme/404.blade.php ENDPATH**/ ?>