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
                        <li class="label-success">
                            <a href="{{ URL::to('/target')}}">All Target</a>
                        </li>
                        <li class="active">
                            <strong>Target By SR</strong>
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
                        <strong>Success! </strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Alert! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">

                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                               role="button"
                                               aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#">Settings 1</a>
                                                </li>
                                                <li><a href="#">Settings 2</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <br/>
                                    <table class="table table-striped projects">
                                        <thead>
                                        <tr>
                                            <th>S/L</th>
                                            <th>User Name</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>Month Name</th>
                                            <th>Item Name</th>
                                            <th>Subcategory Name</th>
                                            <th>Category Name</th>
                                            <th>Target CTN</th>
                                            <th>Target Value</th>
                                            <th style="width: 20%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($bySrdata as $index=>$bySrdata1)
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td>{{$bySrdata1->supervisor_name}}</td>
                                                <td>{{$bySrdata1->year}}</td>
                                                <td>{{$bySrdata1->month}}</td>
                                                <td>{{$bySrdata1->month_name}}</td>
                                                <td>{{$bySrdata1->item_name}}</td>
                                                <td>{{$bySrdata1->subcategory}}</td>
                                                <td>{{$bySrdata1->category}}</td>
                                                <td>{{$bySrdata1->initial_target_in_ctn}}</td>
                                                <td>{{$bySrdata1->initial_target_in_value}}</td>


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
        $("#emp_id").select2({width: 'resolve'});

    </script>
@endsection