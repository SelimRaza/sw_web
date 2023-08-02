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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h5 style="margin-top: 0px;font-size: 18px;text-align: left;">Menu Assign</h5>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="saturday" name="saturday"
                                               value="Employee" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input id="user_name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="user_name"
                                               placeholder="user_name" required="required" type="text">
                                        {{--    <select class="form-control" name="emp_id" id="emp_id" required>
                                                <option value="">Select</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->aemp_name." (".$user->aemp_usnm.")" }}</option>
                                                @endforeach
                                            </select>--}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-3">
                                            <button id="send" onclick="filterData()" class="btn btn-primary"><span class="fa fa-search"></span> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <form class="form-horizontal form-label-left" action="{{route('menu.store')}}"
                                  method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                {{csrf_field()}}


                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table id="data_table" class="table table-bordered font_color"
                                               data-page-length='25'>
                                            <thead>
                                            <tr class="tbl_header_light">
                                                <th><input type="checkbox" id="select_all"/> Menu Assess</th>
                                                <th><input type="checkbox" id="select_c"/> Create</th>
                                                <th><input type="checkbox" id="select_r"/> View</th>
                                                <th><input type="checkbox" id="select_u"/> Update</th>
                                                <th><input type="checkbox" id="select_d"/> Inactive</th>
                                                <th> Menu Name</th>
                                                <th> URL</th>
                                                <th> Menu</th>
                                            </tr>
                                            </thead>
                                            <tbody id="cont">


                                            </tbody>
                                        </table>


                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <button id="send" type="submit" class="btn btn-primary"> <span class="fa fa-check-circle"></span> Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        //$("#user_name").select2();
        function filterData() {
            var user_name = $("#user_name").val();
            var _token = $("#_token").val();
            /*if(dbhouse_id == ''){
             dbhouse_id = -1;
             }*/
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/menu/filterMenu",
                data: {
                    user_name: user_name,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    //onsole.log(data);
                    $("#cont").empty();
                    //$("#cont").append(data);
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;
                    for (var i = 0; i < data.length; i++) {
                        console.log(data[i]);
                        var status = '';
                        var c = '';
                        var r = '';
                        var u = '';
                        var d = '';
                        if (data[i].visibility == 1) {
                            status = 'checked';
                        }
                        if (data[i].c != 0) {
                            c = 'checked';
                        }
                        if (data[i].r != 0) {
                            r = 'checked';
                        }
                        if (data[i].u != 0) {
                            u = 'checked';
                        }
                        if (data[i].d != 0) {
                            d = 'checked';
                        }

                        html += '<tr class="tbl_body_gray">' +
                            '<td><input  ' + status + ' class="checkbox" type="checkbox" name="sub_menu_id[]" value=' + data[i].id + '></td>' +
                            '<td><input  ' + c + ' class="checkbox_c" type="checkbox" name="create[' + data[i].id + ']" value=""></td>' +
                            '<td><input   ' + r + ' class="checkbox_r" type="checkbox" name="read[' + data[i].id + ']" value=""></td>' +
                            '<td><input  ' + u + ' class="checkbox_u" type="checkbox" name="update[' + data[i].id + ']" value=""></td>' +
                            '<td><input  ' + d + ' class="checkbox_d" type="checkbox" name="delete[' + data[i].id + ']" value=""></td>' +
                            '<td><input type="hidden"   name="user_id" value=' + data[0].user_id + '>' + data[i].name + '</td>' +
                            '<td>' + data[i].url + '</td>' +
                            '<td>' + data[i].menu_name + '</td>' +
                            '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }


    </script>


    <script type="text/javascript">

        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                console.log(status);
                //change ".checkbox" checked status
            });
        });

        $("#select_c").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox_c:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                console.log(status);
                //change ".checkbox" checked status
            });
        });
        $("#select_r").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox_r:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                console.log(status);
                //change ".checkbox" checked status
            });
        });
        $("#select_u").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox_u:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                console.log(status);
                //change ".checkbox" checked status
            });
        });
        $("#select_d").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox_d:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                console.log(status);
                //change ".checkbox" checked status
            });
        });

        $('.checkbox').change(function () {
            if (this.checked == false) {
                $("#select_all")[0].checked = false;
            }
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true;
            }
        });

        $('.checkbox_c').change(function () {
            if (this.checked == false) {
                $("#select_c")[0].checked = false;
            }
            if ($('.checkbox_c:checked').length == $('.checkbox_c').length) {
                $("#select_c")[0].checked = true;
            }
        });
        $('.checkbox_r').change(function () {
            if (this.checked == false) {
                $("#select_r")[0].checked = false;
            }
            if ($('.checkbox_r:checked').length == $('.checkbox_r').length) {
                $("#select_r")[0].checked = true;
            }
        });
        $('.checkbox_u').change(function () {
            if (this.checked == false) {
                $("#select_u")[0].checked = false;
            }
            if ($('.checkbox_u:checked').length == $('.checkbox_u').length) {
                $("#select_u")[0].checked = true;
            }
        });
        $('.checkbox_d').change(function () {
            if (this.checked == false) {
                $("#select_d")[0].checked = false;
            }
            if ($('.checkbox_d:checked').length == $('.checkbox_d').length) {
                $("#select_d")[0].checked = true;
            }
        });
    </script>
@endsection
