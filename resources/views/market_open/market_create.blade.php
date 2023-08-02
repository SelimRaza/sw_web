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
    </style>
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="label-success">
                            <a href="{{ URL::to('/market_open')}}">All Market</a>
                        </li>
                        <li>
                            Add New
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
                            <center><strong> ::: Market Open :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('/employee/routeSearch')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">District
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                         <select class="form-control" name="district_id" id="district_id" required onchange="getThanaBelogToDistrict()">
                                             <option value="">Select</option>
                                             @foreach($districts as $district)
                                                 <option value="{{$district->id}}}">{{$district->dsct_name}}</option>
                                             @endforeach
                                         </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thana
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="thana_id" id="thana_id"
                                                required onchange="getWardNameBelogToThana()">

                                        </select>
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Ward
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="ward_id" id="ward_id" required>

                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-5 col-sm-5 col-xs-12"></div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button type="button" class="btn btn-success btn-sm" onclick="loadWardMarket()">Show</button>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Add New</button>
                            </div>
                        </div>
                        <div class="x_content">
                            <input id="myInput" type="text" placeholder="Search.." style="margin-bottom: 2px;">
                            <table id="myTable" class="table table-bordered projects" data-page-length='50'>
                                <thead>
                                    <tr class="tbl_header">
                                        <th>SL</th>
                                        <th>ID</th>
                                        <th>Code</th>
                                        <th>District</th>
                                        <th>Thana</th>
                                        <th>Ward</th>
                                        <th>Market</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="myTable">

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--==========================Add Model====================--}}

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Market Open</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Market Name:</label>
                            <input type="text" class="form-control" id="market_name" placeholder="Enter Your Market Name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="AddNewMarket()">Add</button>
                </div>
            </div>
        </div>
    </div>
    {{--==========================Add END====================--}}

    {{--==========================Edit Model====================--}}

    <div class="modal fade" id="editexampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Market Open</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Market Name:</label>
                            <input type="text" class="form-control" id="edit_market_name" placeholder="Enter Your Market Name">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Ward Name:</label>
                            <select class="form-control" name="edit_ward_id" id="edit_ward_id">

                            </select>
                            <input type="hidden" class="form-control" id="upgrade_id" placeholder="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveEditDetails()">Update</button>
                </div>
            </div>
        </div>
    </div>
    {{--==========================Add END====================--}}
 <script type="text/javascript">
     $(document).ready(function () {

         $("select").select2({width: 'resolve'});

     });
    function ConfirmDelete()
    {
        var x = confirm("Are you sure you want to delete?");
        if (x)
            return true;
        else
            return false;
    }
    function getThanaBelogToDistrict(){

        var district_id=$('#district_id').val();
        $.ajax({
            type: "GET",
            url: "{{URL::to('/')}}/json/get/market_open/thana_list",
            data: {
                district_id: district_id
            },
            cache: false,
            dataType: "json",
            success: function (data) {
                var $el = $('#thana_id');
                if(!data){
                    $el.html('');
                    $el.append($("<option></option>").attr("value", "").text("---"));
                    $el.selectpicker('destroy');
                }else{

                    $el.html('');
                    $el.append($("<option></option>").attr("value", "").text("Select"));
                    $.each(data, function(key,value) {

                        $el.append($("<option></option>").attr("value", value.id).text(value.than_name));
                    });
                    $el.selectpicker('refresh');
                }

            }
        });
    }
    
    function getWardNameBelogToThana() {

        var thana_id=$('#thana_id').val();
        $.ajax({
            type: "GET",
            url: "{{URL::to('/')}}/json/get/market_open/word_list",
            data: {
                thana_id: thana_id
            },
            cache: false,
            dataType: "json",
            success: function (data) {
                var $el = $('#ward_id');
                if(!data){
                    $el.html('');
                    $el.append($("<option></option>").attr("value", "").text("---"));
                    $el.selectpicker('destroy');
                }else{

                    $el.html('');
                    $el.append($("<option></option>").attr("value", "").text("Select"));
                    $.each(data, function(key,value) {

                        $el.append($("<option></option>").attr("value", value.id).text(value.ward_name));
                    });
                    $el.selectpicker('refresh');
                }

            }
        });
    }

    function loadWardMarket() {

        var ward_id=$('#ward_id').val();
        $.ajax({
            type: "GET",
            url: "{{URL::to('/')}}/json/get/ward_wise/market_list",
            data: {
                ward_id: ward_id
            },
            cache: false,
            dataType: "json",
            success: function (data) {

                console.log(data);

                var rows = '';
                var i=1;
                $.each(data, function (key, value) {
                    rows = rows + '<tr>';
                    rows = rows + '<td>' + i++ + '</td>';
                    rows = rows + '<td>' + value.id + '</td>';
                    rows = rows + '<td>' + value.market_code + '</td>';
                    rows = rows + '<td>' + value.district_code+'-'+value.district + '</td>';
                    rows = rows + '<td>' + value.thana_code+'-'+value.thana + '</td>';
                    rows = rows + '<td>' + value.ward_code+'-'+value.ward + '</td>';
                    rows = rows + '<td>' + value.market_name + '</td>';
                    rows = rows + '<td>' + '<button id="'+value.id+'" class="btn btn-info btn-xs" class="btn btn-primary" data-toggle="modal" data-target="#editexampleModal" data-whatever="@fat" onclick="editMarket(this.id)">Edit</button>'+ '</td>';
                });
                $("tbody").html(rows);

            }
        });

    }

    function AddNewMarket(){

        var ward_id=$('#ward_id').val();
        var market_name=$('#market_name').val();
        if(ward_id==null && market_name==""){

             alert("Ward and Market Name Cannot Empty..!!");

        }else{

            $.ajax({

                type: "GET",
                url: "{{URL::to('/')}}/json/save/market_details",
                data: {

                    ward_id: ward_id,
                    market_name: market_name

                },
                cache: false,
                dataType: "json",
                success: function (data) {

                      $("#exampleModal").modal('hide');
                      if(data=='1'){
                          loadWardMarket();
                          alert('Market Open Success..!!');
                          $('#market_name').val('');

                      }else{

                          alert('Market Open Failed..!!');

                      }

                }
            });


        }


    }

    function editMarket(edit_id) {

        var district_id=$('#district_id').val();
        var thana_id=$('#thana_id').val();
        $.ajax({

            type: "GET",
            url: "{{URL::to('/')}}/json/edit_market",
            data: {

                edit_id: edit_id,
                district_id: district_id,
                thana_id: thana_id

            },
            cache: false,
            dataType: "json",
            success: function (data) {

                var $el = $('#edit_ward_id');
                if(!data['1']){
                    $el.html('');
                    $el.append($("<option></option>").attr("value", "").text("---"));
                    $el.selectpicker('destroy');
                }else{

                    $.each(data['1'], function(key,value) {

                        $el.append($("<option></option>").attr("value", value.id).text(value.name));
                    });

                }

                $('#edit_market_name').val(data['0']);
                $('#upgrade_id').val(data['2']);


            }
        });

    }

    function saveEditDetails() {

        var edit_market_name=$('#edit_market_name').val();
        var edit_ward_id=$('#edit_ward_id').val();
        var upgrade_id=$('#upgrade_id').val();
        $.ajax({

            type: "GET",
            url: "{{URL::to('/')}}/json/edit/market_details",
            data: {

                edit_market_name: edit_market_name,
                edit_ward_id: edit_ward_id,
                upgrade_id: upgrade_id

            },
            cache: false,
            dataType: "json",
            success: function (data) {

                var rows = '';
                var i=1;
                $.each(data, function (key, value) {
                    rows = rows + '<tr>';
                    rows = rows + '<td>' + i++ + '</td>';
                    rows = rows + '<td>' + value.id + '</td>';
                    rows = rows + '<td>' + value.market_code + '</td>';
                    rows = rows + '<td>' + value.district_code+'-'+value.district + '</td>';
                    rows = rows + '<td>' + value.thana_code+'-'+value.thana + '</td>';
                    rows = rows + '<td>' + value.ward_code+'-'+value.ward + '</td>';
                    rows = rows + '<td>' + value.market_name + '</td>';
                    rows = rows + '<td>' + '<button id="'+value.id+'" class="btn btn-info btn-xs" class="btn btn-primary" data-toggle="modal" data-target="#editexampleModal" data-whatever="@fat" onclick="editMarket(this.id)">Edit</button>'+ '</td>';
                });
                $("tbody").html(rows);
                $('#editexampleModal').modal('hide');

            }
        });
    }
 </script>
    <script>
        $(document).ready(function(){
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection