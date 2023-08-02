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
                            <strong>All Employee</strong>
                        </li>
                    </ol>
                </div>
                <form action="{{ URL::to('/employee')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="{{$search_text}}">
                                <span class="input-group-btn">
                      <button class="btn btn-default" type="submit">Go!</button>
                    </span>

                            </div>
                        </div>
                    </div>
                </form>
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
                            <div class="item form-group">
                                @if($permission->wsmu_crat)
                                    <a class="btn btn-success btn-sm" href="{{ URL::to('/employee/create')}}">Add
                                        New</a>
                                    <a class="btn btn-success btn-sm"
                                       href="{{ URL::to('/employee/employeeHrisUpload')}}">Add HRIS</a>
                                    <a class="btn btn-success btn-sm"
                                       href="{{ URL::to('employee/employeeUpload')}}">Upload</a>
                                    <a class="btn btn-success btn-sm"
                                       href="{{ URL::to('get/employee/routeSearch/view')}}">Search Route</a>
                                    <a class="btn btn-success btn-sm"
                                       href="{{ URL::to('employee/get/routeLike/view')}}">Route Like</a>
                                @endif
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_title">
                            <div class="item form-group">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                           for="name" style="text-align: left">Company<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control" name="acmp_id" id="acmp_id"
                                                onchange="sfsafsa(this.value)">
                                            <option value="">Select Company</option>
                                            @foreach($acmp as $acmpList)
                                                <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                    - {{$acmpList->acmp_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                           style="text-align: left">Group<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control" name="slgp_id" id="slgp_id">
                                            <option value="">Select Group</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="item form-group">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                           style="text-align: left">Zone<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control" name="zone_id" id="zone_id">
                                            <option value="">Select Zone</option>
                                            @foreach($zone as $zoneList)
                                                <option value="{{$zoneList->id}}">{{$zoneList->zone_code}}
                                                    - {{$zoneList->zone_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <button id="send" type="button" style="margin-top: 22px; margin-left:10px;"
                                            class="btn btn-success"
                                            onclick="getReport()"><span class="fa fa-search"
                                                                        style="color: white;"></span> <b>Search</b>
                                    </button>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">
                            {{$employees->appends(Request::only('search_text'))->links()}}
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="exportTableToCSV('employee_list_<?php echo date('Y_m_d'); ?>.csv','datatables')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>

                            <table id="datatables" class="table table-bordered table-responsive font_color search-table"
                                   data-page-length='100' style="width: 100%;">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th style="width: 10%">User Id</th>
                                    <th>User Name</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Designation</th>
                                    <th>Manager</th>
                                    <th>Mobile Access</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$employee->id}}</td>
                                        <td>{{$employee->aemp_usnm}}</td>
                                        <td>{{$employee->aemp_name}}</td>
                                        <td>{{$employee->aemp_mob1}}</td>
                                        {{--<td>{{$employee->aemp_emal}}</td>--}}
                                        <td>{{$employee->role_name}}</td>
                                        <td>{{$employee->edsg_name}}</td>
                                        <td>{{$employee->aemp_mngr}} - {{$employee->mnrg_name}}</td>
                                        {{--<td>
                                            <ul class="list-inline">
                                                <li>
                                                    @if($employee->aemp_picn!='')
                                                        <img src="https://images.sihirbox.com/{{$employee->aemp_picn}}"
                                                             class="avatar" alt="Avatar">
                                                    @endif
                                                </li>

                                            </ul>
                                        </td>--}}
                                        <td>{{$employee->amng_name}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('employee.show',$employee->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('employee.edit',$employee->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                            <!-- <form style="display:inline"
                                                      action="{{route('employee.destroy',$employee->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $employee->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
                                                    </input>
                                                </form> -->
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <form style="display:inline"
                                                      action="employee/{{$employee->id}}/reset"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field("PUT")}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Pass Reset"
                                                           onclick="return ConfirmReset()">
                                                    </input>
                                                </form>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <!-- end project list -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">


        $(document).ready(function () {
            $("select").select2({width: 'resolve'});

            $('table.search-table').tableSearch({
                searchPlaceHolder: 'Search Text'
            });
        });
        $("#report_div").hide();

        function sfsafsa(acmp_id) {


            alert("fsfsf");
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/report/getGroup",
                data: {
                    slgp_id: acmp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    alert(data);
                    $("#slgp_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Group</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }
                    alert(html);
                    $("#slgp_id").append(html);

                }
            });
        }

        function getGroup(slgp_id) {
            $("#slgp_id").empty();
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/report/getGroup",
                data: {
                    slgp_id: slgp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    alert(data);
                    $("#sales_group_id").empty();


                    $('#ajax_load').css("display", "none");
                    var html = '<option value="">Select Group</option>';
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i].slgp_name + '</option>';
                    }

                    $("#slgp_id").append(html);

                }
            });
        }

        function exportTableToCSV(filename, tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#' + tableId + '  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length - 1; j++)
                    row.push(cols[j].innerText);
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;
            csvFile = new Blob([csv], {type: "text/csv"});
            downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function ConfirmReset() {
            var x = confirm("Are you sure you want to Reset?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection