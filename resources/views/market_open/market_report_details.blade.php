@extends('theme.app')
@section('content')
    <style>
        .x_title {
            margin-bottom: 0px;
        }
        .x_panel {
            width: 100%;
            padding: 0px 8px;
            display: inline-block;
            border: 1px solid #E6E9ED;
            -webkit-column-break-inside: avoid;
            -moz-column-break-inside: avoid;
            column-break-inside: avoid;
            opacity: 1;
            transition: all .2s ease;
        }


    </style>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong><a href="{{url('/market_open')}}">All Market</a></strong>
                        </li>
                        <li class="active">
                            <strong>Market Report Details</strong>
                        </li>
                    </ol>
                </div>
                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                @if(Session::has('success'))
                    <div class="alert alert-success">
                        <strong></strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong></strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: Market Report Details:::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_content">
                                <div class="table-responsive">
                                <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                    <thead style="color: aliceblue;">
                                        <tr class="tbl_header">
                                            <th>SL</th>
                                            <th>District</th>
                                            <th>Thana</th>
                                            <th>Market</th>
                                            <th>Outlet</th>
                                            <th>Address</th>
                                            <th>Mobile</th>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1;?>
                                        @foreach($results as $result)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$result->dsct_name}}</td>
                                            <td>{{$result->than_name}}</td>
                                            <td>{{$result->mktm_name}}</td>
                                            <td>{{$result->site_name}}</td>
                                            <td>{{$result->site_adrs}}</td>
                                            <td>{{$result->site_mob1}}<br>{{$result->site_mob2}}</td>
                                            <td>{{$result->aemp_name}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <script type="text/javascript">

     $(document).ready(function()
     {
         $("tr:odd").css({
             "background-color":"#e8e5e5",
             "color":"#222"});
         $("tr:even").css({
             "background-color":"#edf7f7;",
             "color":"#222"});
         $(".tbl_header").css({
             "color":"#FFFFFF"});
     });

 </script>
@endsection