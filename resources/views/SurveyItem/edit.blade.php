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
                            <a href="{{ URL::to('/survey_items')}}">Survey Item</a>
                        </li>
                        <li class="active">
                            <strong>Survey Item</strong>
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
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <strong>
                                <center> ::: Extend Survey Duration :::</center>
                            </strong>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="{{URL('survey_items/updated',$data->id)}}"
                                  method="post">
                                {{csrf_field()}}
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item
                                            Name
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input name="Promotion_name"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$data->amim_name}}" type="text" disabled>

                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Start
                                            Date <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="startDate" name="startDate"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$data->sv_sdat}}" type="text" disabled>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End
                                            Date <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="endDate" name="endDate"
                                                   class="form-control col-md-7 col-xs-12 "
                                                   value="{{$data->sv_edat}}" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Status <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="lfcl_id" id="lfcl_id" onchange="getPromotionSlab(this.value)"
                                                    required >

                                                <option value="1" @if($data->lfcl_id == "1") SELECTED @endif >Active</option>
                                                <option value="2" @if($data->lfcl_id == "2") SELECTED @endif >Inactive</option>

                                            </select>

                                        </div>
                                    </div>
                                    @if($permission->wsmu_updt)
                                   <div class="item form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-3">
                                            <button id="send" type="submit" style="margin-top: 22px;"
                                                    class="btn btn-primary"><span class="fa fa-check-circle"
                                                                                style="color: white; font-size: 1.3em"></span>
                                                <b>Update</b>
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                <hr/>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#myDiv').hide();
        $("#endDate").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true
        });

        function getReport() {

            var _token = $("#_token").val();
            var promotion_id = $('#promotion_id').val();
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();
            var lfcl_id = $("#lfcl_id").val();
            alert(promotion_id);
            alert(promotion_id);
            alert(promotion_id);
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/promotion_sp/extend_date/save",
                data: {
                    promotion_id: promotion_id,
                    startDate: startDate,
                    endDate: endDate,
                    lfcl_id: lfcl_id,
                    _token: _token
                },
                cache: false,
                dataType: "json",
                success: function (data) {

                    alert(data);
                    $('#ajax_load').css("display", "none");
                }
            });
        }
    </script>
@endsection