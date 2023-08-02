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
                            <strong>All Market</strong>
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
                        <div class="x_title" style="text-transform: uppercase;text-align: center;">
                            <div class="row">
                                 <div class="col-sm-1"><h1 style="font-size: 24px"><a href="{{ URL::to('/market_open/create')}}"><button class="btn btn-success btn-sm">Add New</button></a></h1></div>
                                 <div class="col-sm-8 col-sm-offset-1"><h1 style="font-size: 24px">Market List</h1></div>
                            </div>
                        </div>
                        <!-- {{$markets->appends(Request::only('search_text'))->links()}} -->
                        <div class="x_content">
                            <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                <thead>
                                    <tr class="tbl_header">
                                        <th>SL</th>
                                        <th>District</th>
                                        <th>Thana</th>
                                        <th>Ward</th>
                                        <th>Market</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1;?> 
                                    @foreach($markets as $market)
                                       <tr>
                                         <td>{{$i++}}</td>
                                         <td>{{$market->district}}</td>
                                         <td>{{$market->thana}}</td>
                                         <td>{{$market->ward}}</td>
                                         <td>{{$market->market_code}}/{{$market->market_name}}</td>
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
    <script type="text/javascript">
        function ConfirmDelete() {
            
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection