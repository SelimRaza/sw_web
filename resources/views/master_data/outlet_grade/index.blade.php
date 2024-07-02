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
                            <strong>All Outlet Category</strong>
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
                            @if($permission->wsmu_crat)

                                <a class="btn btn-success btn-sm" href="{{ URL::to('/outlet_grade/create')}}"><span
                                            class="fa fa-plus-circle" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Add
                                        New</b></a>
                            @endif
                            <button class="btn btn-danger btn-sm" onclick="exportTableToCSV('outlet_category_<?php echo date('Y_m_d'); ?>.csv','datatables')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>


                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            <table id="datatables" class="table search-table font_color">
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Category Name</th>
                                    <th>Category Code</th>
                                    <th>Status</th>
                                    <th style="width: 30%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($outletGrades as $index=>$outletGrade)
                                    <tr  class="tbl_body_gray">
                                        <td class="cell_left_border">{{$index+1}}</td>
                                        <td>{{$outletGrade->otcg_name}}</td>
                                        <td>{{$outletGrade->otcg_code}}</td>
                                        <td><?php echo $outletGrade->lfcl_id == 1 ? 'Active' : 'Inactive'?></td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('outlet_grade.show',$outletGrade->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-search"></i> View
                                                </a>&nbsp;|&nbsp;
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('outlet_grade.edit',$outletGrade->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-edit"></i> Edit
                                                </a>&nbsp;|&nbsp;
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('outlet_grade.destroy',$outletGrade->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $outletGrade->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                           onclick="return ConfirmDelete()">
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

        $(document).ready(function(){
            $('table.search-table').tableSearch({
                searchPlaceHolder:'Search Text'
            });
        });
        function exportTableToCSV(filename,tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#'+tableId+'  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length-1; j++)
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
    </script>
@endsection