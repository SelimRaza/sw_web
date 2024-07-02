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
                            <strong>Survey Items</strong>
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
                            @if($permission->wsmu_crat)
                                <a href="{{URL::to('survey_items/create')}}" class="btn btn-primary" type="submit">Add Item</a>
                            @endif
                               
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
                            <a href="#" class="btn btn-danger" type="submit" onclick="inactiveSurveyItem()">Inactive</a>
                            <table id="datatable" class="table table-bordered projects">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="amim_all">  All</th>
                                    <th>SL</th>
                                    <th>CLASS NAME</th>
                                    <th>ITEM NAME</th>
                                    <th>START DATE</th>
                                    <th>END DATE</th>
                                    <th>CREATED BY</th>
                                    <th>UPDATED BY</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                @foreach($data as $index => $d)
                                    <tr>
                                        <td><input type="checkbox" class="single_item" name="single_item" value="{{$d->id}}"></td>
                                        <td>{{$index+1}}</td>
                                        <td>{{$d->class_name}}</td>
                                        <td>{{$d->amim_name}}</td>
                                        <td>{{$d->sv_sdat}}</td>
                                        <td>{{$d->sv_edat}}</td>
                                        <td>{{$d->iusr_id.'-'.$d->iusr_name}}</td>
                                        <td>{{$d->eusr_id.'-'.$d->eusr_name}}</td>
                                        <td>{{$d->lfcl_name}}</td>
                                        <td><a href="{{URL::to('survey_items/edit',$d->id)}}" class="btn btn-primary btn-sm" type="submit">Edit</a></td>
                                        
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
        function inactiveSurveyItem(){
            let npit = [];
            let _token =$('#_token').val();
            $.each($("input[name='single_item']:checked"), function(){
                npit.push($(this).val());
            });
            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/inactive/survey_items",
                data: {
                    _token:_token,
                    survey_items:npit,
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                    location.reload();
                    Swal.fire({
                    icon:'success',
                    text: 'Survey Item Inactivated Successfully!',
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