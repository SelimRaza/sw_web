<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo e(asset("theme/images/favicon.ico")); ?>" type="image/ico"/>

    <title><?php echo e(config('app.name', 'BS').' '.Auth::user()->country()->cont_name); ?></title>

    <!-- Bootstrap -->
    <link href="<?php echo e(asset("theme/vendors/bootstrap/dist/css/bootstrap.min.css")); ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo e(asset("theme/vendors/font-awesome/css/font-awesome.min.css")); ?>" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo e(asset("theme/vendors/nprogress/nprogress.css")); ?>" rel="stylesheet">
    <!-- iCheck -->
    <link href="<?php echo e(asset("theme/vendors/iCheck/skins/flat/green.css")); ?>" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="<?php echo e(asset("theme/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css")); ?>"
          rel="stylesheet">
    <!-- JQVMap -->
    <link href="<?php echo e(asset("theme/vendors/jqvmap/dist/jqvmap.min.css")); ?>" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="<?php echo e(asset("theme/vendors/bootstrap-daterangepicker/daterangepicker.css")); ?>" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo e(asset("theme/build/css/custom.min.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("theme/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css")); ?>"
          rel="stylesheet">
    <script src="<?php echo e(asset("theme/vendors/jquery/dist/jquery.min.js")); ?>"></script>
    <script src="<?php echo e(asset("theme/vendors/bootstrap/dist/js/bootstrap.min.js")); ?>"></script>
    <script src="<?php echo e(asset("theme/vendors/select2/dist/js/select2.full.min.js")); ?>"></script>
    <script src="<?php echo e(asset("theme/vendors/moment/min/moment.min.js")); ?>"></script>
    <script src="<?php echo e(asset("theme/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")); ?>"></script>
    <style type="text/css">
        body{
            font-family:system-ui !important;
        }
        .left_col{
            background-color: #333333!important;
        }
        .disabled {
            pointer-events: none;
            cursor: default;
        }
        .nav li.current-page{
            background-color: white!important;
            color:black!important;
        }

        .select2 {
            width: 100% !important;
        }

        #ajax_load {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1000;
            background-color: #808080;
            opacity: .6;
        }

        .ajax-loader {
            position: absolute;
            left: 50%;
            top: 50%;
            margin-left: -32px; /* -1 * image width / 2 */
            margin-top: -32px; /* -1 * image height / 2 */
            display: block;
        }
        #type{
            height:30px !important;
            width:160px!important;
            border-radius:15px;
        }
        #myModal{
            fontFamily:sans serif;
        }
        .pg_bar_percen{
            width:48%;
         }
        
    </style>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-82H28TLBFS"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-82H28TLBFS');
    </script>
</head>

<body class="nav-md">
<div id="ajax_load" style="display:none;">
    <img src="<?php echo e(asset("theme/production/images/gif-load.gif")); ?>" class="ajax-loader"/>
</div>
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class=" " style="border: 0;background-color:black!important;">
                    <a href="<?php echo e(URL::to('/')); ?>" class="site_title"><i class="fa fa-globe"></i>
                        <span><?php echo e(config('app.name', 'FMS').' '.Auth::user()->country()->cont_name); ?></span></a>

                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
            <?php echo $__env->make('theme.profile', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- /menu profile quick info -->

                <br/>

                <!-- sidebar menu -->
            <?php echo $__env->make('theme.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
            <?php echo $__env->make('theme.menu_footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
    <?php echo $__env->make('theme.top_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- /top navigation -->

        <!-- page content -->
    <?php echo $__env->yieldContent('content'); ?>

    <!-- /page content -->

        <!-- footer content -->
    <?php echo $__env->make('theme.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- /footer content -->
    </div>
</div>

<!-- FastClick -->
<script src="<?php echo e(asset("theme/vendors/fastclick/lib/fastclick.js")); ?>"></script>
<!-- NProgress -->
<script src="<?php echo e(asset("theme/vendors/nprogress/nprogress.js")); ?>"></script>
<!-- Chart.js -->
<script src="<?php echo e(asset("theme/vendors/Chart.js/dist/Chart.min.js")); ?>"></script>
<!-- echarts -->
<script src="<?php echo e(asset("theme/vendors/echarts/dist/echarts.min.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/echarts/map/js/world.js")); ?>"></script>
<!-- gauge.js -->
<script src="<?php echo e(asset("theme/vendors/gauge.js/dist/gauge.min.js")); ?>"></script>
<!-- bootstrap-progressbar -->
<script src="<?php echo e(asset("theme/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js")); ?>"></script>
<!-- iCheck -->
<script src="<?php echo e(asset("theme/vendors/iCheck/icheck.min.js")); ?>"></script>
<!-- Skycons -->
<script src="<?php echo e(asset("theme/vendors/skycons/skycons.js")); ?>"></script>
<!-- Flot -->
<script src="<?php echo e(asset("theme/vendors/Flot/jquery.flot.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/Flot/jquery.flot.pie.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/Flot/jquery.flot.time.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/Flot/jquery.flot.stack.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/Flot/jquery.flot.resize.js")); ?>"></script>
<!-- Flot plugins -->
<script src="<?php echo e(asset("theme/vendors/flot.orderbars/js/jquery.flot.orderBars.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/flot-spline/js/jquery.flot.spline.min.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/flot.curvedlines/curvedLines.js")); ?>"></script>
<!-- DateJS -->
<script src="<?php echo e(asset("theme/vendors/DateJS/build/date.js")); ?>"></script>
<!-- JQVMap -->
<script src="<?php echo e(asset("theme/vendors/jqvmap/dist/jquery.vmap.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/jqvmap/dist/maps/jquery.vmap.world.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js")); ?>"></script>
<!-- bootstrap-daterangepicker -->
<script src="<?php echo e(asset("theme/vendors/moment/min/moment.min.js")); ?>"></script>
<script src="<?php echo e(asset("theme/vendors/bootstrap-daterangepicker/daterangepicker.js")); ?>"></script>
<!-- Custom Theme Scripts -->
<script src="<?php echo e(asset("theme/build/js/custom.min.js")); ?>"></script>
<script src="<?php echo e(asset('theme/vendors/raphael/raphael.min.js')); ?>"></script>
<script src="<?php echo e(asset('theme/vendors/morris.js/morris.min.js')); ?>"></script>

<script>
    // $(document).ready(function () {
    //     startTime();
    //     $('#datatable').DataTable({
    //         dom: 'Bfrtip',
    //         bDestroy: 'true',
    //         buttons: [
    //             'copy',
    //             'excel',
    //             'csv',
    //             'pdf',
    //             'print'
    //         ]

    //     });

    // });

    function changeTimezone(date, ianatz) {
        var invdate = new Date(date.toLocaleString('en-US', {
            timeZone: ianatz
        }));
        var diff = invdate.getTime() - date.getTime();
        return new Date(date.getTime() + diff);
    }
    function startTime() {
        var there = new Date();
        var today = changeTimezone(there, "<?php echo e(Auth::user()->country()->cont_tzon); ?>");
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        m = checkTime(m);
        s = checkTime(s);
        h = checkTime(h);
        document.getElementById('txt').innerHTML =  h + ":" + m + ":" + s + " " + ampm;
        var t = setTimeout(startTime, 500);
    }
    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }
        ;  // add zero in front of numbers < 10
        return i;
    }

</script>
</body>
</html>
<?php /**PATH /home/bsolutio/public_html/saleswheel/resources/views/theme/app_all.blade.php ENDPATH**/ ?>