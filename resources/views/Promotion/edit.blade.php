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
                            <a href="{{ URL::to('/promotion')}}">All Promotion</a>
                        </li>
                        <li class="active">
                            <strong>Extend Promotion Date</strong>
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
                            <span style="color: #7287b8"><center><strong>Promotion Name: {{$editPromotion->prom_name}}<br /> Promotion Code: {{$editPromotion->prom_code}}</strong></center></span>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
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
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form class="form-horizontal form-label-left"
                                  action="{{route('promotion.update',$editPromotion->id)}}"
                                  enctype="multipart/form-data"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('PUT')}}


                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Start Date <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="startDate" name="startDate" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               placeholder="Name" required="required" type="text"
                                               value="{{$editPromotion->prom_sdat}}">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">End Date <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="endDate" name="endDate" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2"
                                               placeholder="Code" required="required" type="text"
                                               value="{{$editPromotion->prom_edat}}">
                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-3">
                                        <button id="send" type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if($editPromotion->prom_nztp=='1')

                            <form class="form-horizontal form-label-left"
                                  action="{{URL::to('promotion/addZone/'.$Promotion->id)}}" enctype="multipart/form-data"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('POST')}}
                                <div class="x_content">
                                    <hr/>
                                    <strong>
                                        <center> ::: Assign Area :::</center>
                                    </strong>
                                    <hr/>
                                    <h4></h4>
                                    <button id="send" type="submit" class="btn btn-primary">Save</button>
                                    <table id="datatable" class="table table-bordered table-striped projects"
                                           data-page-length="100">
                                        <thead>
                                        <tr class="tbl_header">
                                            <th><input type="checkbox" id="select_all"/></th>
                                            <th>Zone Code</th>
                                            <th>Zone Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($eZone as $index => $eZone)
                                            <tr>
                                                <td><input class="checkbox" type="checkbox" name="zoneId[]"
                                                           value="{{$eZone->id}}" {{$eZone->p_status}}></td>
                                                <td>{{$eZone->zone_code}}</td>
                                                <td>{{$eZone->zone_name}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </form>


                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#startDate').datetimepicker({format: 'YYYY-MM-DD'});
        $('#endDate').datetimepicker({format: 'YYYY-MM-DD'});
        $("#sales_gorup_id").select2ToTree();
        $("#than_id").select2ToTree();
        $("#base_id").select2ToTree();

    </script>
    <script>
        //select all checkboxes
        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox').each(function () { //iterate all listed checkbox items
                this.checked = status; //change ".checkbox" checked status
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
    </script>
@endsection
