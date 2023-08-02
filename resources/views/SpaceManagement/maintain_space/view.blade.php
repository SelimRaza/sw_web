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
                            <a href="{{ URL::to('/maintain/space')}}">Space Maintain List</a>
                        </li>
                        <li class="active">
                            <strong>Space Maintain</strong>
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


                 <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <form class="form-horizontal form-label-left"
{{--                                      action="{{route('promotion.create.exist')}}"--}}
                                      method="post">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <strong>
                                        <center> ::: Space Details :::</center>
                                    </strong>
                                    <hr/>


                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="item form-group">
                                            <table id="myTableSlab"
                                                   class="table table-bordered table-striped projects">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Code</th>
{{--                                                    <th>Group Code</th>--}}
{{--                                                    <th>Group Name</th>--}}
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
{{--                                                    <th>Qualifier</th>--}}
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{$space->spcm_name}}</td>
                                                        <td>{{$space->spcm_code}}</td>
{{--                                                        <td>{{optional($space->saleGroup)->slgp_code}}</td>--}}
{{--                                                        <td>{{optional($space->spcm_code)->slgp_name}}</td>--}}
                                                        <td>{{$space->spcm_sdat}}</td>
                                                        <td>{{$space->spcm_exdt}}</td>
{{--                                                        <td>{{$space->spcm_qyfr==1 ? 'Value' : 'FOC' }}</td>--}}
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <hr/>
                                    <strong>
                                        <center> ::: Showcase items :::</center>
                                    </strong>
                                    <hr/>

                                    <div class="item form-group">

                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table id="myTableSlab"
                                                   class="table table-bordered table-striped projects">
                                                <thead>
                                                <tr>
                                                    <th style="width: 8%">SL</th>
                                                    <th style="width: 10%">Item Code</th>
                                                    <th>Item Name</th>
                                                    <th style="width: 15%">Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($showcases as $index => $showcase)
                                                    <tr>
                                                        <td>{{$index+1}}</td>
                                                        <td>{{$showcase->amim_code}}</td>
                                                        <td>{{$showcase->amim_name}}</td>
                                                        <td>{{$showcase->min_qty ?? ''}}</td>
                                                    </tr>
                                                @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    <hr/>


                                    @if(count($free_items) > 0)
                                        <strong>
                                            <center> ::: Free Items :::</center>
                                        </strong>

                                        <div class="item form-group">

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table id="myTableSlab"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 8%">SL</th>
                                                        <th style="width: 10%">Item Code</th>
                                                        <th>Item Name</th>
                                                        <th style="width: 15%">Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($free_items as $key => $item)
                                                        <tr>
                                                            <td>{{$key+1}}</td>
                                                            <td>{{$item->amim_code}}</td>
                                                            <td>{{$item->amim_name }}</td>
                                                            <td>{{$item->min_qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <hr/>
                                    @endif


                                    @if(count($free_amounts) > 0)

                                        <strong>

                                            <center> ::: Gift Amount :::</center>
                                        </strong>
                                        <hr/>

                                        <div class="item form-group">

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table id="myTableSlab"
                                                       class="table table-bordered table-striped projects">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 8%">SL</th>
                                                        <th style="width: 10%">Zone Code</th>
                                                        <th>Zone Name</th>
                                                        <th style="width: 15%">Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($free_amounts as $key => $zone)
                                                        <tr>
                                                            <td>{{$key+1}}</td>
                                                            @if($zone->zone_id == 0)
                                                                <td colspan="2" class="text-center">All Zones</td>
                                                            @else
                                                                <td>{{$zone->zone_code }}</td>
                                                                <td>{{$zone->zone_name }}</td>
                                                            @endif
                                                            <td>{{$zone->max_amnt}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <hr/>
                                    @endif

                                    <strong>
                                        <center> ::: Assigned Zone :::</center>
                                    </strong>
                                    <hr/>
                                    <div class="item form-group">

                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table id="myTableSlab"
                                                   class="table table-bordered table-striped projects">
                                                <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Zone Code</th>
                                                    <th>Zone Name</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($zones as $index => $zone)
                                                    <tr>
                                                        <td>{{$index+1}}</td>
                                                            <td>{{$zone->zone_code}}</td>
                                                            <td>{{$zone->zone_name}}</td>
                                                            <td>{{$zone->max_approve}}</td>
                                                            <td>
                                                                <a onclick="setZoneStatus('{{$zone->id}}')" id="zone-{{$zone->id}}"
                                                                   class="btn-primary btn-xs">
                                                                    {{($zone->lfcl_id==1) ? 'Active' : 'Inactive'}}
                                                                </a>
                                                            </td>

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td>1</td>
                                                        <td colspan="3" class="text-center"> All Zones </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    @isset($space->spcm_imge)
                                        <strong>
                                            <center> ::: Regulations / Notes :::</center>
                                        </strong>
                                        <hr/>
                                        <div class="item form-group" style="display: flex; justify-content: center;">

                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <img src="https://images.sihirbox.com/{{$space->spcm_imge}}" alt="notes" width="512" height="512">
                                            </div>
                                        </div>
                                    @endisset
                                </form>
                            </div>
                        </div>
                    </div>

            </div>

        </div>
        </div>
        <style>
            .fa-times-circle-o:hover{
                cursor: pointer;
            }

            #radio:hover{
                color: #73879C;
                cursor: pointer;
            }

            td{
                padding: 4px !important;
            }
        </style>
        <script>
            function setZoneStatus(zone_id){
                let _token=$('#_token').val();



                if(zone_id != ''){
                    $('#ajax_load').css('display','block');
                    $.ajax({
                        type:"POST",
                        url:"{{URL::to('/')}}/updateSpaceZoneMapping/"+zone_id,
                        data:{
                            _token:_token,
                        },
                        cache:"false",
                        success:function(data){
                            $('#ajax_load').css('display','none');
                            if(data === '1'){
                                $(`#zone-${zone_id}`).text('Active')
                            }else if(data === '2'){
                                $(`#zone-${zone_id}`).text('Inactive')
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