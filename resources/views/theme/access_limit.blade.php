<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FMS') }}</title>
    <link href="{{ asset("theme/vendors/bootstrap/dist/css/bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/font-awesome/css/font-awesome.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/nprogress/nprogress.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/build/css/custom.min.css")}}" rel="stylesheet">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-12">
            <div class="col-middle">
                <div class="text-center text-center">
                    <div class="error-number"><a href="{{ URL::to('/')}}" class="site_title"><i class="fa fa-globe"></i>
                            <span>{{ config('app.name', 'FMS') }}</span></a></div>
                    <h1 class="error-number">Access Limited</h1>
                    <h2>Sorry but we couldn't find Your Access on this page</h2>
                    <p>Please Contact your line manager to get access</p>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset("theme/vendors/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ asset("theme/vendors/bootstrap/dist/js/bootstrap.min.js")}}"></script>
<script src="{{ asset("theme/vendors/fastclick/lib/fastclick.js")}}"></script>
<script src="{{ asset("theme/vendors/nprogress/nprogress.js")}}"></script>
<script src="{{ asset("theme//build/js/custom.min.js")}}"></script>
</body>
</html>
