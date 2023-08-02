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
                            <strong>All Item</strong>
                        </li>
                        <li>
                            <a href="{{ URL::to('/default-discount')}}">Default Discount List</a>
                        </li>
                    </ol>
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
                            <h2 class="text-center">Mapping Items</h2>
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
                                    <th>Item Name</th>
                                    <th>Item Code</th>
                                    <th>Discount %</th>
                                    <th>Discount Name</th>
                                    <th>Discount Code</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody id="cont">
                                @php
                                    $i=1;
                                @endphp
                                @foreach($data as $d)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$d->amim_name}}</td>
                                    <td>{{$d->amim_code}}</td>
                                    <td>{{$d->dfim_disc}}</td>
                                    <td>{{$d->dfdm_name}}</td>
                                    <td>{{$d->dfdm_code}}</td>
                                    
                                    <td>
                                    @if($permission->wsmu_delt)
                                            <a href="#"
                                                class="btn btn-danger btn-xs" onclick="removeItem(this)" value="{{$d->id}}" dfdm_id="{{$d->dfdm_id}}"><i class="fa fa-destroy"></i> Remove Item
                                            </a>
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

        function removeItem(v){
            var id=$(v).attr('value');
            var dfdm_id=$(v).attr('dfdm_id');
            $.ajax({
                type:"GET",
                url:"{{URL::to('/')}}/removeItem/"+id+"/"+dfdm_id,
                cache:"false",
                success:function(data){
                    console.log(data);
                    var html='';
                    $('#cont').empty();
                        for(var i=0;i<data.length;i++){
                            html+='<tr><td>'+(i+1)+'</td>'+
                                    '<td>'+data[i].amim_name+'</td>'+
                                    '<td>'+data[i].amim_code+'</td>'+
                                    '<td>'+data[i].dfim_disc+'</td>'+
                                    '<td>'+data[i].dfdm_name+'</td>'+
                                    '<td>'+data[i].dfdm_code+'</td>'+
                                    '<td><a href="#" class="btn btn-danger btn-xs" onclick="removeItem(this)" value="'+data[i].id+'" dfdm_id="'+data[i].dfdm_id+'">Remove Item</tr>';
                        }
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Item Removed Successfully!',
                    });
                    
                    $('#cont').append(html);

                },error:function(error){
                    console.log(error)
                }

            });
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
