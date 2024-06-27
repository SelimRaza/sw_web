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
                        {{-- <li class="active">
                             <strong>All Mistiri Summary</strong>
                         </li>--}}
                        <li>
                            <a href="{{ URL::to('mistiri/report')}}"> <strong>All Mistiri Summary</strong></a>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Mistiri Report</h1>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
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

                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/mistiri/dataExport')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">Start
                                            Date
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                   name="start_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12 " for="name">End
                                            Date
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input type="text" class="form-control col-md-7 col-xs-12 date"
                                                   name="end_date"
                                                   value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                    </div>

                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" type="submit" class="btn btn-success">Download Report
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>

                                    </div>
                                </div>
                            </form>
                            {{-- <form action="{{ URL::to('/employee')}}" method="get"> --}}
                            <form action="{{ URL::to('mistiri/report')}}" method="get">
                                <div class="title_right">
                                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                                        <div class="input-group">

                                            <input type="text" class="form-control" name="search_text"
                                                   placeholder="Search for..."
                                                   value="{{$search_text}}">
                                            <span class="input-group-btn">
                                                       <button class="btn btn-default" type="submit">Go!</button>
                                                        </span>

                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
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
                            <h1>Mistiri Summary</h1>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
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
                            {{$mistiridata->appends(Request::only('search_text'))->links()}}
                            <table class="table table-striped projects" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>S/L</th>
                                    <th>Iss.Date</th>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>District</th>
                                    <th>Thana</th>
                                    <th>Bld.Grp</th>
                                    <th>DOB</th>
                                    <th>MOB</th>
                                    <th>NID</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($mistiridata as $index=>$data1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$data1->date_issue}}</td>
                                        <td>{{$data1->dlrp_frno}}</td>
                                        <td>{{$data1->dlrp_name}}</td>
                                        <td>Present:-{{$data1->dlrp_prad}}
                                            <br/>Permanent:-{{$data1->dlrp_pmad}}</td>
                                        <td>Present:-{{$data1->Present_Dist_Name}}
                                            <br/>Permanent:-{{$data1->Permanent_Dist_Name}}</td>
                                        <td>Present:-{{$data1->Present_Thana_Name}}
                                            <br/>Permanent:-{{$data1->Permanent_Thana_Name}}</td>
                                        <td>{{$data1->dlrp_bldg}}</td>
                                        <td>{{$data1->dlrp_edob}}</td>
                                        <td>{{$data1->dlrp_mobn}}</td>
                                        <td>{{$data1->dlrp_nidn}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="#"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                                <a href=" http://cardapi.sihirfms.com/Home/FrontView?id={{$data1->dlrp_frno}}"
                                                   class="btn btn-default btn-xs" target="_blank"><i class="fa fa-print"></i> Front
                                                </a>
                                                <a href=" http://cardapi.sihirfms.com/Home/BackView?id={{$data1->dlrp_frno}}"
                                                   class="btn btn-default btn-xs" target="_blank"><i class="fa fa-print"></i> Back
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="#"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif

                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('mistiri.destroy',$data1->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $data1->dlrp_aprv == 1 ? 'Approved' : 'Pending'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form>
                                            @endif
                                        </td>
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
            var x = confirm("Are you sure you want to Approved ?");
            if (x)
                return true;
            else
                return false;
        };
        $(document).ready(function () {
            $('.date').datetimepicker({format: 'YYYY-MM-DD'});
            $("select").select2({width: 'resolve'});
        });

    </script>
@endsection