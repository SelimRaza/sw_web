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
                            <strong>All Account</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/self_account/create')}}">New Account</a>
                            </li>
                        @endif
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
                            <h1>Account</h1>
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
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th style="width: 80%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>{{$account->id}}</td>
                                        <td>{{$account->name}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <a href="{{route('self_account.show',$account->id)}}"
                                                   class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> View
                                                </a>
                                            @endif
                                            @if($permission->wsmu_updt)
                                                <a href="{{route('self_account.edit',$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/employee/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Employee
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/cash_move_type/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Cash Move Type
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/cash_source/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Cash Flow Source
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/cash_receive/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Cash Receive
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/cash_out/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Cash Out
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/cash_move/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Cash Move
                                                </a>
                                            @endif
                                            @if($permission->wsmu_read)
                                                <a href="{{ URL::to('self_account/cash_history/'.$account->id)}}"
                                                   class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Cash History
                                                </a>
                                            @endif
                                            @if($permission->wsmu_delt)
                                                <form style="display:inline"
                                                      action="{{route('self_account.destroy',$account->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="<?php echo $account->status_id == 1 ? 'Inactive' : 'Active'?>"
                                                    >
                                                    </input>
                                                </form>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- end project list -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection