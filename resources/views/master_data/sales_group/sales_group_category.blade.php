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
                        <li>
                            <a href="{{ URL::to('/sales-group')}}">All Sales Group</a>
                        </li>
                        <li class="active">
                            <strong>Sales Category</strong>
                        </li>

                    </ol>
                </div>

                <div class="title_right">
                    @if ($permission->wsmu_crat)
                        <a href="{{ URL::to('sales-group-category-mapping') }}" style="color:darkred;font-weight:bold;" target="_blank"><i class="fa fa-upload"></i>Upload</a>
                    @endif
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
                    <div class="col-md-12">
                        @if($permission->wsmu_updt)
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h2>{{$salesGroup->slgp_name}}
                                                <small>{{$salesGroup->slgp_code}}</small>
                                            </h2>
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
                                            <form id="demo-form2" data-parsley-validate
                                                  class="form-horizontal form-label-left"
                                                  action="{{ URL::to('sales-group/category_add/'.$salesGroup->id)}}"
                                                  method="GET">
                                                {{csrf_field()}}
                                                {{method_field('PUT')}}
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">Category name
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2" name="category_name" value="{{old('category_name')}}"
                                                               placeholder="category_name" required="required" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">Category Code
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2" name="category_code" value="{{old('category_code')}}"
                                                               placeholder="category_code" required="required" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"
                                                           for="first-name">Sequence No
                                                        <span class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 col-xs-12 ">
                                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                                               data-validate-length-range="6" data-validate-words="2" name="category_seqc" value="{{old('category_seqc')}}"
                                                               placeholder="category_seqc" required="required" type="number">
                                                    </div>
                                                </div>

                                                <div class="ln_solid"></div>
                                                <div class="form-group">
                                                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                        <button type="submit" class="btn btn-success">Save</button>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>{{$salesGroup->name}}
                                            <small>{{$salesGroup->code}}</small>
                                        </h2>
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
                                        <button onclick="exportTableToCSV('sales_group_category<?php echo date('Y_m_d'); ?>.csv')"
                                                class="btn btn-warning">Export
                                        </button>
                                        <table class="table table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Category Id</th>
                                                <th>Category Code</th>
                                                <th>Category Name</th>
                                                <th style="width: 20%">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($salesGroupCategorys as $index=>$salesGroupCategory)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$salesGroupCategory->id}}</td>
                                                    <td>{{$salesGroupCategory->category_code}}</td>
                                                    <td>{{$salesGroupCategory->category_name}}</td>
                                                    <td>
                                                        <form style="display:inline"
                                                              action="{{ URL::to('sales-group/category_delete/'.$salesGroupCategory->id)}}"
                                                              class="pull-xs-right5 card-link" method="GET">
                                                            {{csrf_field()}}
                                                            {{method_field('DELETE')}}
                                                            <input class="btn btn-danger btn-xs" type="submit"
                                                                   value="<?php echo $salesGroupCategory->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                                   >
                                                            </input>
                                                        </form>

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
        </div>
    </div>
    <script type="text/javascript">
        $("#sku_id").select2({width: 'resolve'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Delete?");
            if (x)
                return true;
            else
                return false;
        };
        function exportTableToCSV(filename) {
                // alert(tableId);
                var csv = []; 
                var rows = document.querySelectorAll('tr');
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push(cols[j].innerText);
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }

            function downloadCSV(csv, filename) {
                var csvFile;
                var downloadLink;
                
                csvFile = new Blob([csv], {type: "text/csv;charset=utf-8"});
                downloadLink = document.createElement("a");
                downloadLink.download = filename;
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
                downloadLink.click();
            }
    </script>
@endsection