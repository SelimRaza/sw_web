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
                            <strong>Create Notification</strong>
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
                        {{--<div class="x_title">
                            <h6><strong><center> ::: Notification List ::: </center></strong></h6>
                            <div class="clearfix"></div>
                        </div>--}}
                        <div class="x_content">
                            <form class="form-horizontal form-label-left" action=""
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   style="text-align: left; margin-left: -10px;" for="name">Title :
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <input id="title" class="form-control col-md-12 col-xs-12"
                                                   name="title"
                                                   placeholder="Enter Notification title" required="required"
                                                   type="text"/>

                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   style="text-align: left; margin-left: -10px;" for="name">Body Text :
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <textarea rows="4" cols="50" class="form-control col-md-12 col-xs-12"
                                                      name="bodyMessage" form="usrform">
                                            </textarea>
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                                   style="text-align: left; margin-left: -10px;" for="name">Image :
                                            </label>
                                            <textarea rows="4" cols="50" class="form-control col-md-12 col-xs-12"
                                                      name="image" form="usrform">
                                            </textarea>
                                        </div>

                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">Company<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="acmp_id" id="acmp_id"
                                                    onchange="getGroupsss(this.value)" multiple>
                                                <option value="">Select Company</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">Group<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="slgp_id" id="slgp_id"
                                                    onchange="jsonGetEmployeeList()" multiple>
                                                <option value="">Select Group</option>
                                            </select>
                                        </div>


                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">Region<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="dirg_id" id="dirg_id"
                                                    onchange="jsonGetEmployeeList()">
                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">Zone<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="zone_id" id="zone_id"
                                                    onchange="jsonGetEmployeeList()">
                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">District<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="dist_id" id="dist_id"
                                                    onchange="jsonGetEmployeeList()">
                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">Thana<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="than_id" id="than_id"
                                                    onchange="jsonGetEmployeeList()">
                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label class="control-label col-md-12 col-sm-12 col-xs-12"
                                               style="text-align: left;" for="name">User Role<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <select class="form-control" name="user_role" id="user_role"
                                                    onchange="jsonGetEmployeeList()">
                                                <option value="">Select Group</option>
                                                @foreach($acmp as $acmpList)
                                                    <option value="{{$acmpList->id}}">{{$acmpList->acmp_code}}
                                                        - {{$acmpList->acmp_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <center>
                                        <button id="send" type="submit" class="btn btn-success">Create Notification</button>
                                    </center>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#user_role').select2();
        $('#acmp_id').select2();
        $('#slgp_id').select2();
        $('#dist_id').select2();
        $('#than_id').select2();
        $('#dirg_id').select2();
        $('#zone_id').select2();

        function getGroupsss(acmp_id) {
            alert(acmp_id);
            var _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/load/filter/getNGroup",
                data: {
                    slgp_id: acmp_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    alert(data);

                }
            });
        }

    </script>
@endsection