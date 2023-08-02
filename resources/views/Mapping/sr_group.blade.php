@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">
                            <strong> SR-Group</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">
                            <!-- <div class="form-group col-md-4">
                                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="itsg_id">Item Code
                                           
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                </div>
                            </div> -->
                            <div class="form-group col-md-4">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="aemp_id">Employee
                                </label>
                                <div class="col-md-8 col-sm-8 col-xs-12">
                                    <select class="form-control cmn_select2" name="aemp_id" id="aemp_id"
                                            >
                                        <option value="">Select Employee</option>
                                        @foreach($users as $usr)
                                            <option value="{{ $usr->id }}">{{ '['.$usr->aemp_usnm.'] '.ucfirst($usr->aemp_name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            
                        </div>
                        <div  class="col-md-3 col-sm-3 col-xs-12">
                            <button type="submit" class="btn btn-success" onclick="filterData()">Search</button>
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
                        <div class="x_content">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <h3 class="text-center">SR-Group</h3>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                   
                                        <tr>
                                            <th>SL</th>
                                            <th>Employee Id</th>
                                            <th>Employee Name</th>
                                            <th>Group Code</th>
                                            <th>Group Name</th>
                                        </tr>
                                    
                                    </thead>
                                    <tbody id="cont">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        
        $(".cmn_select2").select2({width: 'resolve'});
        var user_name = $("#user_name").val();
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        }
        
        function filterData() {
            let aemp_id = $("#aemp_id").val();      
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/sr-group",
                data: {
                    aemp_id: aemp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    let html = '';
                    let count = 1;
                    for (let i = 0; i < data.length; i++) {
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].aemp_usnm + '</td>' +
                            '<td>' + data[i].aemp_name + '</td>' +
                            '<td>' + data[i].slgp_code + '</td>' +
                            '<td>' + data[i].slgp_name + '</td>' +
                           '</tr>';
                            
                        count++;
                    }
                   $("#cont").append(html)
                },error:function(error){
                    console.log(error)
                    swal.fire({
                        icon:"error",
                        text:"Something Went Wrong !!!",
                    })
                }
            });
        }
        
       
    </script>
@endsection