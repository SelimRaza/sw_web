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
                            <strong>All  Location</strong>
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

                        <div class="row">

                            <div class="col-md-6">

                            </div>

                        </div>


                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>

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
                                <h1>Maintain Location</h1>

                                <ul class="nav navbar-left panel_toolbox">

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
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>

                                        <th>S/L</th>
                                        <th>location</th>
                                        <th>Type</th>
                                        <th>Location name</th>
                                        <th>QR Code</th>
                                        <th>Map address</th>
                                        <th>created_by</th>
                                        <th>updated_by</th>
                                        <th>created_at</th>
                                        <th>updated_at</th>
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
    <script type="text/javascript">
       // $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
      //  $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                //change ".checkbox" checked status
            });
        });

        $('.checkbox').change(function () { //".checkbox" change
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false
            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });

        function filterData() {


            var _token = $("#_token").val();
           // console.log(start_date + end_date);
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/location/filterMaintainLocation",
                data: {
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    //onsole.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;

                    for (var i = 0; i < data.length; i++) {
                        var readonly1 = '';
                        if (data[i].status_id != 1) {
                            readonly1 = 'disabled readonly'
                        }
                        html += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].location_master + '</td>' +
                            '<td>' + data[i].type_name + '</td>' +
                            '<td>' + data[i].locd_name + '</td>' +
                            '<td>' + data[i].locd_code + '</td>' +
                            '<td>' + data[i].location + '</td>' +
                            '<td>' + data[i].created_by + '</td>' +
                            "<td>" + data[i].updated_by + "</td>" +
                            "<td>" + data[i].created_at + "</td>" +
                            "<td>" + data[i].updated_at + "</td>";
                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
    </script>
@endsection