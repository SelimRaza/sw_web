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
                            <a href="{{ URL::to('/sku')}}">All SKU</a>
                        </li>
                        <li class="active">
                            <strong>Show SKU</strong>
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
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h1>SKU </h1>
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
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <img src="https://images.sihirbox.com/{{$sku->amim_imgl}}" class="avatar" alt="Avatar" width="500" height="500">
                            </div>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <form class="form-horizontal form-label-left"
                                      action="{{route('sku.show',$sku->id)}}"
                                      method="post">
                                    {{csrf_field()}}
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" name="amim_name" value="{{$sku->amim_name}}"
                                                       placeholder="Name" required="required" type="text" maxlength="30">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bangla Name
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amim_olin" value="{{$sku->amim_olin}}"
                                                       placeholder="Bangla Name" required="required" type="text">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item Code
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amim_code" value="{{$sku->amim_code}}"
                                                       placeholder="Item Code" required="required" type="text">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bar Name
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amim_bcod" value="{{$sku->amim_bcod}}"
                                                       placeholder="Bar Code" required="required" type="text">
                                            </div>
                                        </div>
                                       


                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Short
                                                Name
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amin_snme" value="{{$sku->amin_snme}}"
                                                       placeholder="Short Name" required="required" type="text">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thikness
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amim_tkns" value="{{$sku->amim_tkns}}"
                                                       placeholder="Thikness" required="required" type="text">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Excise Per CTN
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amim_pexc" value="{{$sku->amim_pexc}}"
                                                       placeholder="Excise Per CTN" required="required" step="any" type="number">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat Percentage
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="ln_name" class="form-control col-md-7 col-xs-12"
                                                       maxlength="10" name="amim_pvat" value="{{$sku->amim_pvat}}"
                                                       placeholder="Vat Percentage" step="any" required="required" type="number">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CTN Size
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2"
                                                       name="amim_duft" value="{{$sku->amim_duft}}"
                                                       placeholder="CTN Size" min="0" required="required" type="number"
                                                       step="1">
                                            </div>
                                        </div>

                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Dealer Price
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12" value="{{$sku->amim_dppr}}"
                                                       data-validate-length-range="6" data-validate-words="2" name="amim_dppr"
                                                       placeholder="Dealer Price" min="0" required="required" type="number"
                                                       step="1">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Trade Price
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12" value="{{$sku->amim_tppr}}"
                                                       data-validate-length-range="6" data-validate-words="2" name="amim_tppr"
                                                       placeholder="Trade Price" min="0" required="required" type="number"
                                                       step="1">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">MRP
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12" value="{{$sku->amim_mrpp}}"
                                                       data-validate-length-range="6" data-validate-words="2" name="amim_mrpp"
                                                       placeholder="MRP" min="0" required="required" type="number"
                                                       step="1">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CBM
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12" value="{{$sku->amim_cbm}}"
                                                       data-validate-length-range="6" data-validate-words="2" name="amim_cbm"
                                                       placeholder="CBM" min="0" required="required" type="number"
                                                       step="1">
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Is Sales Able
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                       data-validate-length-range="6" data-validate-words="2" {{ $sku->amim_issl == '1' ? 'checked' : '' }}
                                                       name="amim_issl" type="checkbox"
                                                >
                                            </div>
                                        </div>
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Color
                                                <span
                                                        class="required">*</span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12" value="{{$sku->amim_colr}}"
                                                       data-validate-length-range="6" data-validate-words="2" name="amim_colr"
                                                       placeholder="Color" min="0" required="required" type="text"
                                                       step="1">
                                            </div>
                                        </div>
                                        
                                        @if(Auth::user()->country()->module_type==2 && $icmp !='')
                                        <div class="item form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Source Country
                                                <span
                                                        class=""></span>
                                            </label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <input readonly id="name" class="form-control col-md-7 col-xs-12" value="{{$icmp?$icmp[0]->cont_name:'N/A'}}"
                                                       
                                                      >
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection