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
                            <strong>All MSP</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <form action="{{ URL::to('/msp')}}" method="get">
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

                                <a href="{{ URL::to('/msp/create')}}" class="btn btn-success btn-sm">Add MSP</a>


                            @endif

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <button class="btn btn-danger btn-sm"
                                    onclick="exportTableToCSV('default_discount_list_<?php echo date('Y_m_d'); ?>.csv','datatabless')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>
                            <table id="datatabless" class="table search-table font_color" data-page-length='50'>
                                <thead>
                                <tr class="tbl_header_light">
                                    <th class="cell_left_border">SL</th>
                                    <th>Msp Name</th>
                                    <th>Msp Code</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody id="cont">
                                @php
                                    $i=1;
                                @endphp
                                @foreach($msps as $d)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$d->mspm_name}}</td>
                                    <td>{{$d->mspm_code}}</td>
                                    <td>{{$d->mspm_sdat}}</td>
                                    <td>{{$d->mspm_edat}}</td>
                                    <td>
                                        @if($permission->wsmu_updt)
                                            <a href="{{route('msp.edit',$d->id)}}"
                                                class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                            </a>
                                            
                                            <a href="{{route('mspitm.show',$d->id)}}"
                                                class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> SKU
                                            </a>
                                            @if(Auth::user()->country()->module_type==2)
                                            <a href="{{route('mspsite.show',$d->id)}}"
                                                class="btn btn-success btn-xs"><i class="fa fa-pencil"></i> Site
                                            </a>
                                            @else
                                            <a href="{{route('mspslgpzone.show',$d->id)}}"
                                                class="btn btn-success btn-xs"><i class="fa fa-pencil"></i>Zone
                                            </a>
                                            @endif
                                            
                                            
                                        @endif
                                        
                                        @if($permission->wsmu_delt)
                                            <form style="display:inline"
                                                    action="{{route('msp.destroy',$d->id)}}"
                                                    class="pull-xs-right5 card-link" method="POST">
                                                {{csrf_field()}}
                                                
                                                <input class="btn btn-danger btn-xs" type="submit"
                                                        value="<?php echo $d->lfcl_id == 1 ? 'Active' : 'Inactive'?>"
                                                        onclick="return ConfirmDelete()">
                                                </input>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$msps->links()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{--promotion data extend modal end--}}
    <script type="text/javascript">
        $('#startDate').datetimepicker({format: 'YYYY-MM-DD'});
        $('#endDate').datetimepicker({format: 'YYYY-MM-DD'});
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Change Life Cyle?");
            if (x)
                return true;
            else
                return false;
        };


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
    </script>


    <script type="text/javascript">
        $(document).ready(function () {
            $('table.search-table').tableSearch({
                searchPlaceHolder: 'Search Text'
            });
        });
        $(document).ready(function () {
            $("select").select2({width: 'resolve'});
        });
        function ConfirmDelete() {
            var x = confirm("Are you sure you about changing the Life Cycle?");
            if (x)
                return true;
            else
                return false;
        };
 

    </script>
@endsection
