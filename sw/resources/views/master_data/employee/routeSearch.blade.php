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
            border-bottom: 1px solid #d8e1d3;
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
                            <a href="{{ URL::to('/employee')}}">All Employee</a>
                        </li>
                        <li>
                            Route Search
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
                            <center><strong> ::: Route Search :::
                                </strong></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action=""
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Search<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input class="form-control col-md-7 col-xs-12" name="route_id" id="route_id" placeholder="Enter Route ID" type="text">
                                    </div>
                                     <div class="col-md-2">
                                        <button id="send" type="submit" class="btn btn-success" onclick="loadSearchRecord()">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Edit Route</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-group">
                                                 <label for="message-text" class="col-form-label">Route</label>
                                                 <input type="text" name="" class="form-control" id="create_market_code">
                                            </div>
                                            <div class="form-group">
                                                <label for="message-text" class="col-form-label">Name</label>
                                                <input type="text" name="" class="form-control" id="create_market_code">
                                            </div>
                                            <div class="form-group">
                                                <label for="message-text" class="col-form-label">Employee</label>
                                                <select class="form-control" name="edit_ward_id" id="edit_ward_id" required>
                                                    <option value="">Select</option>

                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <table class="table table-bordered projects" data-page-length='50' id="routeTable">
                                <thead>
                                    <tr class="tbl_header">
                                        <th>SL</th>
                                        {{--<th>User_ID</th>--}}
                                        <th>User_ID</th>
                                        <th>User_Name</th>
                                        <th>Route_Id</th>
                                        <th>Route_Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

 <script type="text/javascript">
     $(document).ready(function () {

         $("select").select2({width: 'resolve'});

     });

    //====================Load JSON Search Record===============

     function loadSearchRecord() {

       event.preventDefault();
       var route_id=$('#route_id').val();
       if(route_id==""){

           alert("Please Enter Route ID..!!");

       }else{

           $.ajax({
               type: "GET",
               url: "{{URL::to('/')}}/json/get/employee/route",
               data: {
                   route_id: route_id
               },
               cache: false,
               dataType: "json",
               success: function (response) {
                   var i=1;
                   var rows = '';
                   $.each(response, function (key, value) {
                       rows = rows + '<tr>';
                       rows = rows + '<td>' + i++ + '</td>';
                       rows = rows + '<td style="display: none">' + value.id + '</td>';
                       rows = rows + '<td>' + value.aemp_name + '</td>';
                       rows = rows + '<td>' + value.aemp_usnm + '</td>';
                       rows = rows + '<td>' + value.rout_code + '</td>';
                       rows = rows + '<td>' + value.rout_name + '</td>';
                       // rows = rows + '<td>' + '<input type="button" class="btn btn-info btn-xs" data-id="'+value.id+'" data-toggle="modal" data-target="#exampleModalCenter" value="Edit">'
                       //     +" "+'<input type="button" class="btn btn-danger btn-xs" value="Del" id="delete_id">' +'</td>';
                       rows = rows + '<td>'+'<input type="button" class="btn btn-danger btn-xs" value="Del" id="delete_id">' +'</td>';
                       rows=  rows + '</tr>';
                   });
                   $("tbody").html(rows);

               }
           });

         }


     }

     $('#routeTable').on('click', '#delete_id', function () {
         var currow=$(this).closest('tr');
         var delete_id=currow.find('td:eq(1)').text();
         var rowIndex = $(this).closest('tr').prop('rowIndex');
         var x=ConfirmDelete();
         if(x=='1'){
             $.ajax({
                 type: "GET",
                 url: "{{URL::to('/')}}/json/delete/employee/route",
                 data: {

                     delete_id:delete_id
                 },
                 cache: false,
                 dataType: "json",
                 success: function (data) {

                     if(data=='1'){

                         $('#routeTable tr').filter(function () {

                             return this.rowIndex === rowIndex;

                         }).remove();
                         alert("Delete Successful...!!");

                     }else{

                         alert("Fail");
                     }

                 }
             });

         }

     });

     function ConfirmDelete()
     {
         var x = confirm("Are you sure you want to delete?");
         if (x)
             return 1;
         else
             return 0;
     }

     //===================End Function=======

     // function ConfirmDelete(delete_id)
     // {
     //     var rowIndex = $('#routeTable').closest('tr').prop('rowIndex');
     //     alert(rowIndex);
         // $('.coupon_table tr').filter(function () {
         //
         //     return this.rowIndex === rowIndex;
         //
         // }).remove();

        // var x = confirm("Are you sure you want to delete?");







     // }

    {{--function editService(e){--}}

        {{--$.ajax({--}}
            {{--type: "GET",--}}
            {{--url: "{{URL::to('/')}}/json/get/edit/record",--}}
            {{--data: {--}}
                {{--edit_id: e--}}
            {{--},--}}
            {{--cache: false,--}}
            {{--dataType: "json",--}}
            {{--success: function (data) {--}}

                {{--alert(data);--}}

                {{--// $("#thana_id").empty();--}}
                {{--// $("#ward_id").empty();--}}
                {{--// $("#market_id").empty();--}}
                {{--// $('#ajax_load').css("display", "none");--}}

            {{--}--}}
        {{--});--}}

    {{--}--}}
 </script>     
@endsection