@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong> All Orders</strong>
                        </li>
                        <li>
                            <!-- <a href="{{ URL::to('/order/create')}}">Place Order</a> -->
                            
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
                        <strong>Success!</strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <a href="{{route('fmsorder.create')}}" class="btn btn-success">Place Order</a>
                        <div class="x_content">
                            <button class="btn btn-danger btn-sm"
                                    onclick="exportTableToCSV('order_list_<?php echo date('Y_m_d'); ?>.csv','datatabless')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    Order</b></button>
                            <table id="datatabless" class="table search-table font_color" data-page-length='50'>
                                <thead id="item_head">
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Order Date</th>
                                    <th>Order No</th>
                                    <th>SR Name</th>
                                    <th>Outlet Name</th>
                                    <th>Group</th>
                                    <th>Order Line</th>
                                    <th>Order Amount</th>
                                    <th>Order Status</th>
                                </tr>
                                </thead>
                                <tbody id="cont">
                                @foreach($data as $i=>$d)
                                <tr>
                                <td>{{$i+1}}</td>
                                <td>{{$d->ordm_date}}</td>
                                <td>{{$d->ordm_ornm}}</td>
                                <td>{{$d->aemp_usnm."-".$d->aemp_name}}</td>
                                <td>{{$d->site_code."-".$d->site_name}}</td>
                                <td>{{$d->slgp_name}}</td>
                                <td>{{$d->ordm_icnt}}</td>
                                <td>{{$d->ordm_amnt}}</td>
                                <td>{{$d->lfcl_name}}</td>
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
<script> 
</script>
@endsection