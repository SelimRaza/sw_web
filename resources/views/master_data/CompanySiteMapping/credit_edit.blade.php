@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/companySiteMapping')}}"><i class="fa fa-home"></i> Back</a>
                        </li>
                        <li class="active">
                            <strong>Credit Edit</strong>
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
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>Site Credit Adjust</h1>
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

                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Group</th>
                                    <th>Price List</th>
                                    <th>Site Code</th>
                                    <th>Credit Limit</th>
                                    <th>Limit Day</th>
                                    <th>Credit Limit Type</th>
                                    <th>Payment Type</th>
                                </tr>
                                </thead>
                                <tbody id="cont">
                                    <form class="form-control" method="post" action="{{URL::to('credit-adjust')}}">
                                        {{csrf_field()}}
                                        @foreach($data as $i=>$dt)
                                        <tr>
                                            <td><input type="hidden" name="stcm_id[]" value="{{$dt->stcm_id}}">{{$dt->acmp_name}}</td>
                                            <td>{{$dt->slgp_name}}</td>
                                            <td>{{$dt->plmt_name}}</td>
                                            <td>{{$dt->site_code}}</td>
                                            <td><input type="number" class="form-control in_tg" name="stcm_limt[]" value="{{$dt->stcm_limt}}"></td>
                                            <td><input type="number" class="form-control in_tg" name="stcm_days[]" value="{{$dt->stcm_days}}"></td>
                                            <td>
                                                <select class="form-control cmn_select2" name="stcm_isfx[]"
                                                        id="stcm_isfx">
                                                    @if($dt->stcm_isfx==1)
                                                    <option value="1" selected>Fixed</option>
                                                    <option value="0">Variable</option>
                                                    @else
                                                    <option value="0" selected>Variable</option>
                                                    <option value="1">Fixed</option>
                                                    @endif
                                                    
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control cmn_select2" name="optp_id[]"
                                                        id="optp_id">
                                                    @if($dt->optp_id==1)
                                                    <option value="1" selected>Cash</option>
                                                    <option value="2">Credit</option>
                                                    @else
                                                    <option value="2" selected>Credit</option>
                                                    <option value="1" >Cash</option>
                                                    @endif
                                                    
                                                </select>
                                            </td>
                                        @endforeach
                                        <tr>
                                            <td colspan="8"><button type="submit" class=" btn btn-success" style="float:right;">Update</button></td>
                                        </tr>
                                    </form>
                                </tbody>
                            </table>

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
    </script>
@endsection