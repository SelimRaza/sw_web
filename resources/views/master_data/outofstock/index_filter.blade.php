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
                            <strong>OutofStock Item</strong>
                        </li>
                        @if($permission->wsmu_crat)
                            <li class="label-success">
                                <a href="{{ URL::to('/outofstock/create')}}">Add New Item</a>
                            </li>
                        @endif
                    </ol>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <div class="x_content">
                                <form class="form-horizontal form-label-left">
                                    <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                    {{csrf_field()}}
                                    <div class="x_title">
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12 text_left"
                                                    for="dpot_id" style="text-align: left">Depo Name<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="dpot_id" id="dpot_id">
                                                        <option value="">Select</option>
                                                        @foreach($dpot as $dpt)
                                                            <option value="{{$dpt->id}}">{{$dpt->dpot_code}}
                                                                - {{$dpt->dpot_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itcg_id"
                                                    style="text-align: left">Category<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="itcg_id" id="itcg_id">
                                                        <option value="">Select</option>
                                                        @foreach($itcg as $cg)
                                                            <option value="{{$cg->id}}">{{$cg->itcg_code}}
                                                                - {{$cg->itcg_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="itsg_id"
                                                    style="text-align: left">Subcategory<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="itsg_id" id="itsg_id">
                                                        <option value="">Select</option>
                                                        @foreach($itcg as $cg)
                                                            <option value="{{$cg->id}}">{{$cg->itcg_code}}
                                                                - {{$cg->itcg_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Channel<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="chnl_id" id="chnl_id" onchange="getSubChannel(this.value)">
                                                        <option value="">Select</option>
                                                        @foreach($chnl as $cnl)
                                                            <option value="{{$cnl->id}}">{{$cnl->chnl_code}}
                                                                - {{$cnl->chnl_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Sub Channel<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="scnl_id" id="scnl_id">
                                                        <option value="">Select</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Life Cycle<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="lfcl_id" id="lfcl_id">
                                                        <option value="">All</option>
                                                        <option value="1">Active</option>
                                                        <option value="2">Inactive</option>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Verified/Unverified<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <select class="form-control select2" name="site_vrfy" id="site_vrfy">
                                                        <option value="">All</option>
                                                        <option value="1">Verified</option>
                                                        <option value="0">Unverified</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Site Code<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="site_code" id="site_code">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-6">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"
                                                    style="text-align: left">Vat-Tran No<span
                                                            class="required"></span>
                                                </label>
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="site_vtrn" id="site_vtrn">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <button id="send" type="button" style="margin-left:10px;"
                                                        class="btn btn-success"
                                                        onclick="getOutletMaster()"><span class="fa fa-search"
                                                                                    style="color: white;"></span>
                                                    <b>Search</b>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
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
                            <table id="datatable"  class="table table-bordered projects" data-page-length='100'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL


                                    </th>
                                    <th>DPO Name</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Class Name</th>
                                    <th>Category Name</th>
                                    <th>SubCategory Name</th>
                                    <th style="width: 30%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($outofstockData as $index => $locationData1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$locationData1->DealerName}}</td>
                                        <td>{{$locationData1->amim_code}}</td>
                                        <td>{{$locationData1->amim_name}}</td>
                                        <td>{{$locationData1->itcl_name}}</td>
                                        <td>{{$locationData1->itcg_name}}</td>
                                        <td>{{$locationData1->itsg_name}}</td>
                                        <td>
                                            @if($permission->wsmu_read)
                                                <form style="display:inline"
                                                      action="{{route('outofstock.destroy',$locationData1->id)}}"
                                                      class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                                    {{method_field('DELETE')}}
                                                    <input class="btn btn-danger btn-xs" type="submit"
                                                           value="Delete">
                                                    </input>
                                                </form>
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
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to Inactive?");
            if (x)
                return true;
            else
                return false;
        };
        function ConfirmReset() {
            var x = confirm("Are you sure you want to Reset?");
            if (x)
                return true;
            else
                return false;
        };
    </script>
@endsection