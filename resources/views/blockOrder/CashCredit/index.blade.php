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
                            <strong>Cash Party Credit Budget</strong>
                        </li>
                       
                    </ol>
                </div>
                <form action="{{ URL::to('/cash_party_credit_budget')}}" method="get">
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">

                                <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                       value="{{$search_text}}">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">Go!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
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
                        <div class="x_title">
                            <div>
                            @if($permission->wsmu_crat)
                                <a href="{{URL::to('/cash_party_credit_budget/create')}}" class="btn btn-default" type="submit" target="_blank">Add Credit</a>
                                <a href="{{URL::to('cash-credit/bulk/upload')}}" class="btn btn-default" type="submit" target="_blank">Upload Credit</a>
                                <a href="{{URL::to('cash-credit/report')}}" class="btn btn-warning" type="submit" target="_blank">Report</a>
                            @endif
                               
                            </div>
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
                            {{$spdm->appends(Request::only('search_text'))->links()}}
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Emp Name</th>
                                    <th>Balance</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                @foreach($spdm as $index => $spdm1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$spdm1->aemp_name.'('.$spdm1->aemp_usnm.')'}}</td>
                                        <td>{{round($spdm1->spbm_amnt,4)}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('cashCredit.show',$spdm1->id)}}"
                                                   class="btn btn-primary btn-xs" ><i class="fa fa-folder"></i>  View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)

                                                        <a href="{{route('cashCredit.edit',$spdm1->id)}}"
                                                            class="btn btn-info btn-xs"><i class="fa fa-pencil"  ></i> Adjust
                                                        </a>
                                            @endif



                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    </script>
@endsection