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
                            <strong>Notification</strong>
                        </li>

                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            @if($permission->wsmu_crat)
                <a href="{{ url('create-notification') }}" type="button" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add New</a>
            @endif
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
                            <h6><strong><center> ::: Notification List ::: </center></strong></h6>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <table class="table table-bordered table-responsive">
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>Title</th>
                                    <th>Body</th>
                                    <th>Image</th>
                                    <th>Creation Date</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notification as $index=>$notification)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$notification->noti_title}}</td>
                                        <td>{{$notification->noti_body}}</td>
                                        <td><img src="{{$notification->noti_imge}}" width="200px" height="230px" alt="no image found" /> </td>
                                        <td>{{$notification->noti_date}}</td>


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
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection