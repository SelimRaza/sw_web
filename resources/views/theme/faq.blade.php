<!DOCTYPE html>
<html lang="en">
<head>
    <title>SPRO Sales Pro</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon1.ico">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- FontAwesome JS-->
    <script defer src="{{ asset("faq/assets/fontawesome/js/all.min.js")}}"></script>

    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="{{ asset("faq/assets/css/theme.css")}}">

</head>

<body>
<header class="header fixed-top">
    <div class="branding docs-branding">
        <div class="container-fluid position-relative py-2">
            <div class="docs-logo-wrapper">
                <div class="site-logo"><a class="navbar-brand" href=""><img class="logo-icon mr-2"
                                                                                      src="https://images.sihirbox.com/tutorial/spro.svg"
                                                                                      alt="logo"><span
                                class="logo-text">SPRO<span class="text-alt">Docs</span></span></a></div>
            </div><!--//docs-logo-wrapper-->
            <div class="docs-top-utilities d-flex justify-content-end align-items-center">
                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.pranrflgroup.spro"
                   class="btn btn-primary d-none d-lg-flex">Download</a>
            </div><!--//docs-top-utilities-->
        </div><!--//container-->
    </div><!--//branding-->
</header><!--//header-->


<div class="page-header theme-bg-dark py-5 text-center position-relative">
    <div class="theme-bg-shapes-right"></div>
    <div class="theme-bg-shapes-left"></div>
    <div class="container">
        <h1 class="page-heading single-col-max mx-auto">SPRO</h1>
        <div class="page-intro single-col-max mx-auto">Sales Pro Sales Automation Software</div>
        <div class="main-search-box pt-3 d-block mx-auto">
            <form class="search-form w-100">
                <input type="text" placeholder="Search the docs..." name="search" class="form-control search-input">
                <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</div><!--//page-header-->
<div class="page-content">
    <div class="container">
        <div class="docs-overview py-5">
            <div class="row justify-content-center">
                @yield('content')



            </div><!--//row-->
        </div><!--//container-->
    </div><!--//container-->
</div><!--//page-content-->


<footer class="footer">

    <div class="footer-bottom text-center py-5">
        <small class="copyright"> Copyright ©2018 PRAN-RFL GROUP. All rights reserved. Developed by CS-MIS @ Sales
            Automation Team
        </small>
    </div>

</footer>

<!-- Javascript -->
<script src="{{ asset("faq/assets/plugins/jquery-3.4.1.min.js")}}"></script>
<script src="{{ asset("faq/assets/plugins/popper.min.js")}}"></script>
<script src="{{ asset("faq/assets/plugins/bootstrap/js/bootstrap.min.js")}}"></script>

</body>
</html>

