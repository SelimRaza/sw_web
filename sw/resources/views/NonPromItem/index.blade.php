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
                            <strong>Non Promotional Item</strong>
                        </li>
                       
                    </ol>
                </div>
                <form action="{{ URL::to('/non_prom_item')}}" method="get">
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
                                <a href="{{URL::to('non-prom/item/create')}}" class="btn btn-success" type="submit" target="_blank">Add</a>
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
                            {{$data->appends(Request::only('search_text'))->links()}}
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <a href="#" class="btn btn-danger" type="submit" onclick="removeItemFromNonPromlList()">Delete</a>
                            <table id="datatable" class="table table-bordered projects">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="amim_all">  All</th>
                                    <th>SL</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>SubCategory</th>
                                    <th>Group</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                @foreach($data as $index => $d)
                                    <tr>
                                        <td><input type="checkbox" class="single_item" name="single_item" value="{{$d->id}}"></td>
                                        <td>{{$index+1}}</td>
                                        <td>{{$d->amim_code}}</td>
                                        <td>{{$d->amim_name}}</td>
                                        <td>{{$d->itsg_name}}</td>
                                        <td>{{$d->slgp_name}}</td>
                                        
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
        $('#amim_all').on('click', function(e) {
            if($(this).is(':checked',true)){
            $(".single_item").prop('checked', true);
            } else {
            $(".single_item").prop('checked',false);
            }

        });
        function removeItemFromNonPromlList(){
            let npit = [];
            let _token =$('#_token').val();
            $.each($("input[name='single_item']:checked"), function(){
                npit.push($(this).val());
            });
            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/remove/item/npit",
                data: {
                   npit:npit,
                   _token:_token,
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                    location.reload();
                    Swal.fire({
                    icon:'success',
                    text: 'Item removed from non promotional list!',
                    });
                },
                error: function(data) {
                    Swal.fire({
                    icon:'error',
                    text: 'Something Went Wrong!!!',
                    })
                    console.log(data);

                }
            });

        }
    </script>
@endsection