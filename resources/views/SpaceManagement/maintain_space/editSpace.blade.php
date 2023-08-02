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
                            <a href="{{ URL::to('/maintain/space')}}">Spaces List</a>
                        </li>
                        <li class="active">
                            <strong>Edit Space Site Mapping</strong>
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

                @if($errors->any())
                    <div class="alert alert-danger" style="font-family:sans-serif;">
                        <p><strong>Opps Something went wrong</strong></p>
                        <ol>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error}}</li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                <div class="col-md-12 col-sm-12 col-xs-12 spsb-type-select" style="padding: 0">
                    <div class="x_panel" style="display: flex;
                            justify-content: center;
                            flex-direction: column;
                            align-items: center;">
                        <div class="col-md-7">

                            <form class="form-horizontal form-label-left" id="showcase-form"
                                  action="{{URL::to('updateSpaceSiteMapping'.$space->id)}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                                <strong>
                                    <center> ::: Assigned Site :::</center>
                                </strong>
                                <hr/>
                                <div class="item form-group">

                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <table id="myTableSlab"
                                               class="table table-bordered table-striped projects">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Site Code</th>
                                                <th>Site Name</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($sites as $index => $site)
                                                <tr>
                                                    <td>{{$index+1}}</td>
                                                    <td>{{$site->site->site_code}}</td>
                                                    <td>{{$site->site->site_name}}</td>
                                                    <td>
                                                        @if($space->spcm_exdt > date('Y-m-d'))
                                                            <a onclick="setSiteStatus('{{$site->id}}')" id="site-{{$site->id}}"
                                                               class="btn-primary btn-xs">
                                                                {{($site->lfcl_id==1) ? 'Active' : 'Inactive'}}
                                                            </a>
                                                        @else
                                                            <span class="btn-info btn-xs">
                                                                {{($site->lfcl_id==1) ? 'Active' : 'Inactive'}}
                                                            </span>
                                                        @endif

                                                    </td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <style>
        .btn-primary:hover{
            cursor: pointer;
        }
    </style>

    <script>
        function setSiteStatus(site_id){
            let _token=$('#_token').val();

            console.log(_token, site_id)


            if(site_id != ''){
                $('#ajax_load').css('display','block');
                $.ajax({
                    type:"POST",
                    url:"{{URL::to('/')}}/updateSpaceSiteMapping/"+site_id,
                    data:{
                        _token:_token,
                    },
                    cache:"false",
                    success:function(data){
                        $('#ajax_load').css('display','none');

                        if(data === '1'){
                            $(`#site-${site_id}`).text('Active')
                        }else if(data === '2'){
                            $(`#site-${site_id}`).text('Inactive')
                        }

                    },error:function(error){
                        $('#ajax_load').css('display','none');

                        Swal.fire({
                            text: 'Zone not found',
                        })
                    }
                });
            }
            else{
                Swal.fire({
                    text: 'Something Went Wrong',
                })
            }
        }
    </script>
@endsection