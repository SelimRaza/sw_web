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
                            <a href="{{ URL::to('/promotion_sp_2')}}">All Promotion</a>
                        </li>
                        <li class="active">
                            <strong>Show Promotion</strong>
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
                                <center> ::: Extend Promotion Date :::</center>
                            </strong>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="{{URL('promotion_sp_2/extend_date_save')}}"
                                  method="post">
                                {{csrf_field()}}
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                @foreach($pr_det as $index => $pr_det)
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Promotion
                                            Name
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input name="Promotion_name"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$pr_det->prms_name}}" type="text">

                                            <input name="promotion_id" value="{{$pr_det->id}}" type="hidden">

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
                                                   value="{{$pr_det->prms_sdat}}" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End
                                            Date <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="endDate" name="endDate"
                                                   class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   value="{{$pr_det->prms_edat}}" type="text">
                                        </div>
                                    </div>
                                    @if($pr_det->lfcl_id == "1")
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Status <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="lfcl_id" id="lfcl_id" onchange="getPromotionSlab(this.value)"
                                                    required >

                                                <option value="1" @if($pr_det->lfcl_id == "1") SELECTED @endif >Active</option>
                                                <option value="2" @if($pr_det->lfcl_id == "2") SELECTED @endif >Inactive</option>

                                            </select>

                                        </div>
                                    </div>
                                    @endif


                                    <div class="item form-group">
                                        <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-3">
                                            <button id="send" type="submit" style="margin-top: 22px;"
                                                    class="btn btn-primary"><span class="fa fa-check-circle"
                                                                                style="color: white; font-size: 1.3em"></span>
                                                <b>Save</b>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
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
        $('#startDate').datetimepicker({format: 'YYYY-MM-DD'});
        $('#endDate').datetimepicker({format: 'YYYY-MM-DD'});

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