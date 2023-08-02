<!DOCTYPE html>
<html lang="en">
<head>
    <title>SPRO Sales Pro</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon1.ico">

    <!-- Google Font -->
    <!--<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">-->

    <!-- FontAwesome JS-->
    <script defer src="{{ asset("faq/assets/fontawesome/js/all.min.js")}}"></script>

    <!-- Plugins CSS -->
    <!--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.2/styles/atom-one-dark.min.css">-->

    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="{{ asset("faq/assets/css/theme.css")}}">

</head>

<body class="docs-page">
<header class="header fixed-top">
    <div class="branding docs-branding">
        <div class="container-fluid position-relative py-2">
            <div class="docs-logo-wrapper">
                <button id="docs-sidebar-toggler" class="docs-sidebar-toggler docs-sidebar-visible mr-2 d-xl-none" type="button">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="site-logo"><a class="navbar-brand" href=""><img class="logo-icon mr-2" src="https://images.sihirbox.com/tutorial/spro.svg" alt="logo"><span class="logo-text">SPRO<span class="text-alt">Docs</span></span></a></div>
            </div><!--//docs-logo-wrapper-->
            <div class="docs-top-utilities d-flex justify-content-end align-items-center">
                <div class="top-search-box d-none d-lg-flex">
                    <form class="search-form">
                        <input type="text" placeholder="Search the docs..." name="search" class="form-control search-input">
                        <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <a href="https://play.google.com/store/apps/details?id=com.pranrflgroup.spro" class="btn btn-primary d-none d-lg-flex">Download</a>
            </div><!--//docs-top-utilities-->
        </div><!--//container-->
    </div><!--//branding-->
</header><!--//header-->

<div class="docs-wrapper">
    <div id="docs-sidebar" class="docs-sidebar">
        <div class="top-search-box d-lg-none p-3">
            <form class="search-form">
                <input type="text" placeholder="Search the docs..." name="search" class="form-control search-input">
                <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <nav id="docs-nav" class="docs-nav navbar">
            <ul class="section-items list-unstyled nav flex-column pb-3">
                <li class="nav-item section-title"><a class="nav-link scrollto active" href="#section-1"><span class="theme-icon-holder mr-2"><i class="fas fa-map-signs"></i></span>Introduction</a></li>
                <li class="nav-item"><a class="nav-link scrollto" href="#item-1-1">Web</a></li>
                <li class="nav-item"><a class="nav-link scrollto" href="#item-1-2">Mobile</a></li>

            </ul>

        </nav><!--//docs-nav-->
    </div><!--//docs-sidebar-->
    <div class="docs-content">
        <div class="container">
            @yield('content')



            <footer class="footer">
                <div class="container text-center py-5">
                    <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
                    <small class="copyright"> Copyright ©2018 PRAN-RFL GROUP. All rights reserved. Developed by CS-MIS @ Sales
                        Automation Team
                    </small>
                </div>
            </footer>
        </div>
    </div>
</div><!--//docs-wrapper-->



<!-- Javascript -->
<script src="{{ asset("faq/assets/plugins/jquery-3.4.1.min.js")}}"></script>
<script src="{{ asset("faq/assets/plugins/popper.min.js")}}"></script>
<script src="{{ asset("faq/assets/plugins/bootstrap/js/bootstrap.min.js")}}"></script>


<!-- Page Specific JS -->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>-->
<script src="{{ asset("faq/assets/js/highlight-custom.js")}}"></script>
<script src="{{ asset("faq/assets/plugins/jquery.scrollTo.min.js")}}"></script>
<script src="{{ asset("faq/assets/plugins/lightbox/dist/ekko-lightbox.min.js")}}"></script>
<script src="{{ asset("faq/assets/js/docs.js")}}"></script>

</body>
</html>

