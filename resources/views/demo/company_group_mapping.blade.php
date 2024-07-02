@extends('theme.app')
@section('content')
    <style>
        .modal-backdrop {

            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 0;
            background-color: #000;
        }
        .modal-header {
            padding: 0px;
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
                        {{--<li class="label-success">--}}
                            {{--<a href="{{ URL::to('/market_open')}}"></a>--}}
                        {{--</li>--}}
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
                        <strong>Error! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <center><strong> ::: Company Zone Mapping :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/load/company/gorup')}}"
                                  method="get" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Company
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="company_id" id="company_id" required onchange="jsonGetCompanyGroup()">
                                            <option value="">Select</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{$company->acmp_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Group
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="group_id" id="group_id">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-5 col-xs-12"></div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button type="submit" class="btn btn-success btn-sm">Show</button>
                                </div>
                            </form>

                        </div>
                        @if(!empty($results))
                        <div class="x_content">
                            <table id="datatable" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Company Name</th>
                                    <th>Group Name</th>
                                    <th>Emp Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1;?>
                                   @if(!empty($results))
                                   @foreach($results as $result)
                                   <tr>
                                       <td>{{$i++}}</td>
                                       <td>{{$result->acmp_name}}</td>
                                       <td>{{$result->slgp_name}}</td>
                                       <td><span class="badge badge-secondary">{{$result->aemp_usnm}}</span>  <span class="badge badge-secondary">{{$result->aemp_name}}</span></td>
                                       <td>
                                           <a href="{{url('/employee/group_permission',$result->aemp_id)}}"><button class="btn btn-success btn-xs">Show</button></a>
                                       </td>
                                   </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @endif

                        @if(!empty($result2))
                            <div class="x_content">
                                <table id="datatable" class="table table-bordered projects" data-page-length='50'>
                                    <thead>
                                    <tr class="tbl_header">
                                        <th>SL</th>
                                        <th>Company Name</th>
                                        <th>Group Name</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1;?>
                                    @if(!empty($result2))
                                        @foreach($result2 as $result)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{$result->acmp_name}}</td>
                                                <td>{{$result->slgp_name}}</td>
                                                <td>
                                                    <a href="{{url('/show/gorup/user',$result->slgp_id)}}"><button class="btn btn-success btn-xs">Show</button></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });

        function jsonGetCompanyGroup(){

            var company_id=$('#company_id').val();
            $.ajax({
                type: "GET",
                url: "{{URL::to('/')}}/json/load/company/group_name",
                data: {

                    company_id: company_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    var $el = $('#group_id');
                    if(!data){
                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("---"));
                        $el.selectpicker('destroy');
                    }else{

                        $el.html('');
                        $el.append($("<option></option>").attr("value", "").text("Select"));
                        $.each(data, function(key,value) {

                            $el.append($("<option></option>").attr("value", value.id).text(value.slgp_name));
                        });
                        $el.selectpicker('refresh');
                    }

                }
            });
        }
    </script>
@endsection