<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SalesWheel').' '.Auth::user()->country()->cont_name }}</title>
    <link href="{{ asset("theme/vendors/bootstrap/dist/css/bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{ asset("css/style.css")}}" rel="stylesheet">
    <link href="{{ asset("css/animate.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/css/jquery.multiselect.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/font-awesome/css/font-awesome.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/nprogress/nprogress.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/iCheck/skins/flat/green.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/select2/dist/css/select2.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/build/css/custom.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css")}}"
          rel="stylesheet">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="{{ asset("theme/vendors/select2-to-tree/src/select2totree.css")}}" rel="stylesheet">
    <script src="{{ asset("theme/vendors/jquery/dist/jquery.min.js")}}"></script>
    <script src="{{ asset("theme/vendors/js/jquery.multiselect.js")}}"></script>
    <script src="{{ asset("theme/vendors/bootstrap/dist/js/bootstrap.min.js")}}"></script>
    <script src="{{ asset("theme/vendors/select2-to-tree/src/select2totree.js")}}"></script>
    <script src="{{ asset("theme/vendors/select2/dist/js/select2.full.min.js")}}"></script>
    <script src="{{ asset("theme/vendors/moment/min/moment.min.js")}}"></script>
    <script src="{{ asset("theme/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset("theme/vendors/js/excel-bootstrap-table-filter-bundle.js")}}"></script>
    <!-- Datatables -->
    <link href="{{ asset("theme/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css")}}"
          rel="stylesheet">
    <link href="{{ asset("theme/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css")}}"
          rel="stylesheet">
    <link href="{{ asset("theme/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{ asset("theme/vendors/css/excel-bootstrap-table-filter-style.css")}}" rel="stylesheet">
    <script src="{{ asset("theme/build/js/sweetalert2@11.js")}}"></script>
    <style type="text/css">
        body{
            font-family:system-ui !important;
        }
        .left_col{
            background-color: #333333!important;
        }
        
	#add-promotion-div{
            display: flex;
            align-items:center;
            justify-content: right;
        }
        .disabled {
            pointer-events: none;
            cursor: default;
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

        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .rp_type_div{
            width: 100%;
            min-height:50px;
            padding:10px 0px 15px 10px;
            -webkit-box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21); 
            box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
            margin-bottom:15px;
        }
/* Leader board   */

    .leader-card{
        width: 100%;
        max-height:350px;
        padding:10px 0px 15px 10px;
        -webkit-box-shadow: -6px -2px 50px -11px rgba(0,0,0,0.14); 
        box-shadow: -6px -2px 50px -11px rgba(0,0,0,0.14);
        margin-bottom:15px;
    }
    ul.bar_tabs{
        margin:10px 0 5px !important;
        padding-left:20px!important;
    }
    .ld-container{
	    height:330px;
    }

    .leader-img{
        width:100%;
        max-height:60%;
        object-fit:contain;
        background-image:cover;
       
    }
    .progress{
        font-size:10px!important;
        height:10px!important;
    }
    .pg-content{
	    flex:2;
    }
    .pg-content_amnt{
        flex:4;
    }
    .pg-block{
        width: 100%;
        display: flex;
    }
    .pg-content-pg{
	    flex:6;	
    }
    ul.nav-tabs{
        margin:3px 0 5px !important;
        padding-left:20px!important;
    }
    #zonal-selection{
        margin-left:5px;
        margin-right:5px;
    }
    #selection-process{
        margin-top:50px;
        margin-bottom: 10px;
    }
    .pro{
        padding:5px;
    }
    /* leader board end here*/
	
	  .select2-selection--single{
        min-height:35px!important;
        border-radius:5px!important;
    } 
     #select2-acmp_id-container{
        line-height:20px;
    }
    table{
        font-family: Arial Narrow;
        font-size: 14px;
        color: black;
    }
    .select2-selection__rendered, .select2-results__options{
        color: black !important;
    }
	
	.in_tg{
        border-radius:5px;
    }
        #exTab1 .nav-pills > li.active>a{
        font-weight:bold;
        background-color:#169F85;
        color:white;
        margin-left:-5px; 
        
    }
    .traking_div_height{
        height:550px;
    }
    .dropdown-menu {
        max-height:180px;
        overflow-y:scroll; 
    }
    /* text blinking*/
.blink {
 animation: blinkMe 2s linear infinite;
}
@keyframes blinkMe {
 0% {
  opacity: 0;
 }
 50% {
  opacity: 1;
 }
 100% {
  opacity: 0;
 }
}
.loader {
    border: 16px solid forestgreen; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 100px;
    height: 100px;
    animation: spin 2s linear infinite;
}
/* Overwrite Style */

.tbl_header{
    background-color:#6491EA!important;
    color: white !important;
}
.btn-success {
    background: #6491EA!important;
    border: 1px solid #169F85;
    color:#fff!important;
}
.left_col {
    background-color: #142862!important;
}
.scroll-view{
    width:100% !important;
}
.main_container{
    background-color:#163172!important;
}
.nav.side-menu>li.active>a{
    background:none;
}
#exTab1 .nav-pills > li.active>a{
    background-color:#6491EA!important;
}
.nav.side-menu>li.active, .nav.side-menu>li.current-page{
            border-right:5px solid yellow;
}
.breadcrumb{
    padding:8px 33px !important;

}
.page-title .title_right {
    width:53%!important;
}
.page-title .title_right .pull-right{
    margin:0;
}
.btn-primary{
    background-color:#4c9173 !important;
}
.btn-info{
    background-color:orange !important;
}
.btn-danger{
    background-color:#9f1e49 !important;
}

/* OverWrite Style End */
.menu_div{
    width: 100%;
    min-height:100px;
    padding:10px 0px 15px 10px;
    -webkit-box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21); 
    box-shadow: 1px 1px 5px 2px rgba(0,0,0,0.21);
    margin-bottom:15px;
}

.row {
    margin-right: 9px;
    margin-left: 9px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    footer{
        margin-left:0 !important;
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

<body>
<div id="ajax_load" style="display:none;">
    <img src="{{ asset("theme/production/images/gif-load.gif")}}" class="ajax-loader"/>
</div>
<div class="container body">
    @include('theme.top_nav_menu')
    @yield('content')
    @include('theme.footer')

</div>
<script src="{{ asset("theme/vendors/fastclick/lib/fastclick.js")}}"></script>
<script src="{{ asset("theme/vendors/nprogress/nprogress.js")}}"></script>
<script src="{{ asset("theme/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js")}}"></script>
<script src="{{ asset("theme/vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js")}}"></script>
<script src="{{ asset("theme/build/js/custom.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net/js/jquery.dataTables.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-buttons/js/dataTables.buttons.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-buttons/js/buttons.flash.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-buttons/js/buttons.html5.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-buttons/js/buttons.print.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-responsive/js/dataTables.responsive.min.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js")}}"></script>
<script src="{{ asset("theme/vendors/datatables.net-scroller/js/datatables.scroller.min.js")}}"></script>
<script src="{{ asset("theme/vendors/jszip/dist/jszip.min.js")}}"></script>
<script src="{{ asset("theme/vendors/pdfmake/build/pdfmake.min.js")}}"></script>
<script src="{{ asset("theme/vendors/pdfmake/build/vfs_fonts.js")}}"></script>
<script src="{{ asset("js/html-table-search.js")}}"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
{{--<script src="{{ asset("theme/vendors/jquery.confirm.js")}}"></script>--}}
<!-- <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBT24LpOLk1qOrZrGwARNM_Jnkxwwdu20&callback=initMap&libraries=&v=weekly"
      async
></script> -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAUz9b1JjhtFMPkg4scrdW2uAbLfGyc3d4" async></script>
<script>
    $(document).ready(function () {
        startTime();
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            bDestroy: 'true',
            buttons: [
                'copy',
                'excel',
                'csv',
                'pdf',
                'print'
            ]

        });

    });
    function changeTimezone(date, ianatz) {
        var invdate = new Date(date.toLocaleString('en-US', {
            timeZone: ianatz
        }));
        var diff = invdate.getTime() - date.getTime();
        return new Date(date.getTime() + diff);
    }
    function startTime() {
        var there = new Date();
        var today = changeTimezone(there, "{{Auth::user()->country()->cont_tzon}}");
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
<script>
    $('#langOpt').multiselect({
        columns: 1,
        placeholder: 'Select Languages'
    });

    $('#langOpt2').multiselect({
        columns: 1,
        placeholder: 'Select Languages',
        search: true
    });

    $('#langOpt3').multiselect({
        columns: 1,
        placeholder: 'Select Languages',
        search: true,
        selectAll: true
    });

    $('#langOptgroup').multiselect({
        columns: 4,
        placeholder: 'Select Languages',
        search: true,
        selectAll: true
    });
</script>
</body>
</html>