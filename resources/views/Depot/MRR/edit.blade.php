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
                            <a href="{{ URL::to('/mrr')}}">All MRR </a>
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
                        <strong>Success! </strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Alert! </strong>{{ Session::get('danger') }}
                    </div>
                @endif
                <div class="col-md-12">


                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">

                                    <ul class="nav navbar-right panel_toolbox">
                                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                               role="button"
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
                                          action="{{route('mrr.update',$mrrMaster->id)}}"
                                          method="post">
                                        {{csrf_field()}}
                                        {{method_field('PUT')}}
                                        <br/>
                                        <table class="table table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>S/L</th>
                                                <th>MRR Id</th>
                                                <th>SKU ID</th>
                                                <th>SKU Code</th>
                                                <th>QTY</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($dataMrrLine as $index=>$dataMrrLine1)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$dataMrrLine1->mrr_id}}</td>
                                                    <td>{{$dataMrrLine1->sku_id}}</td>
                                                    <td>{{$dataMrrLine1->sku_name}}</td>
                                                    <td>{{$dataMrrLine1->mrrl_qnty}}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-3">
                                                @if($mrrMaster->lfcl_id==1)
                                                    <button id="send" type="submit" class="btn btn-success">Verify
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#emp_id").select2({width: 'resolve'});

    </script>
@endsection