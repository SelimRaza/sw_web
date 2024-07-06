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
                            <strong>New SKU</strong>
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
                            <center><h5><strong> ::: Add New Item ::: </strong></h5></center>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="{{route('sku.store')}}"
                                  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_name" value="{{old('amim_name')}}"
                                                   placeholder="Name" required="required" type="text" maxlength="30">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item Code
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_code" value="{{old('amim_code')}}"
                                                   placeholder="Item Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bangla Name

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_olin" value="{{old('amim_olin')}}"
                                                   placeholder="Bangla Name" type="text">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bar Name

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_bcod" value="{{old('amim_bcod')}}"
                                                   placeholder="Bar Code" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Item Class
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="itcl_id" id="itcl_id"
                                                    required>
                                                <option value="">Select Class</option>
                                                @foreach ($itemClass as $itemClass)
                                                    <option value="{{ $itemClass->id }}">{{ $itemClass->itcl_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sub Category
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="itsg_id" id="itsg_id"
                                                    required>
                                                <option value="">Select SubCategory</option>
                                                @foreach ($subCategorys as $subCategory)
                                                    <option value="{{ $subCategory->id }}">{{ $subCategory->itsg_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Short
                                            Name

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amin_snme" value="{{old('amin_snme')}}"
                                                   placeholder="Short Name" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Thikness

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_tkns" value="{{old('amim_tkns')}}"
                                                   placeholder="Thikness" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Excise Per
                                            CTN

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_pexc" value="{{old('amim_pexc')}}"
                                                   placeholder="Excise Per CTN" step="any" type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat
                                            Percentage

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="ln_name" class="form-control col-md-7 col-xs-12"
                                                   name="amim_pvat" value="{{old('amim_pvat')}}"
                                                   placeholder="Vat Percentage" step="any" type="number">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Image (Max
                                            Size 5 MB)
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="amim_full" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_imgl')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_imgl"
                                                   placeholder="Image" type="file"
                                                   step="1">
                                        </div>

                                    </div>
                                    @if(Auth::user()->country()->module_type==2 && $country !='')
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Source Country
                                            <span
                                                    class=""></span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <select class="form-control" name="cont_id" id="cont_id"
                                                    >
                                                <option value="">Select country</option>
                                                @foreach ($country as $cnt)
                                                    <option value="{{ $cnt->id }}">{{ $cnt->cont_code.'-'.$cnt->cont_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                </div>
                                <div class="col-md-6">
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CTN Size
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_duft" value="{{old('amim_duft')}}"
                                                   placeholder="CTN Size" min="0" required="required" type="number"
                                                   step="1">
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Retails Unit
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">

                                            <select class="form-control" name="amim_runt" id="amim_runt"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($itemUnit as $itemUnit1)
                                                    <option value="{{ $itemUnit1->id }}">{{ $itemUnit1->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Distribution
                                            Unit
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">

                                            <select class="form-control" name="amim_dunt" id="amim_dunt"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($itemUnit as $itemUnit1)
                                                    <option value="{{ $itemUnit1->id }}">{{ $itemUnit1->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Mother
                                            Company
                                            <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">

                                            <select class="form-control" name="amim_acmp" id="amim_acmp"
                                                    required>
                                                <option value="">Select</option>
                                                @foreach ($company as $company1)
                                                    <option value="{{ $company1->id }}">{{ $company1->acmp_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Dealer Price

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_dppr')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_dppr"
                                                   placeholder="Dealer Price" min="0" type="number"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Trade Price

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_tppr')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_tppr"
                                                   placeholder="Trade Price" min="0" type="number"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">MRP

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_mrpp')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_mrpp"
                                                   placeholder="MRP" min="0" type="number"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">CBM

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_cbm')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_cbm"
                                                   placeholder="CBM" min="0" type="number"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Is Sales
                                            Able
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   {{ old('amim_issl') == 'on' ? 'checked' : '' }}
                                                   name="amim_issl" type="checkbox"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Color

                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="name" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_colr')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_colr"
                                                   placeholder="Color" min="0" type="text"
                                                   step="1">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Image (Max
                                            Size 50 KB)
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input id="image_icon" class="form-control col-md-7 col-xs-12"
                                                   value="{{old('amim_imgl')}}"
                                                   data-validate-length-range="6" data-validate-words="2"
                                                   name="amim_icon"
                                                   placeholder="Image" type="file"
                                                   step="1">
                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <center><button id="send" type="submit" class="btn btn-success">Submit</button></center>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {


            const itcl_id = '{{ old('itcl_id') }}';
            const itsg_id = '{{ old('itsg_id') }}';
            const amim_runt = '{{ old('amim_runt') }}';
            const amim_dunt = '{{ old('amim_dunt') }}';
            const amim_acmp = '{{ old('amim_acmp') }}';
            if (itcl_id !== '') {
                $('#itcl_id').val(itcl_id);
            }
            if (itsg_id !== '') {
                $('#itsg_id').val(itsg_id);
            }
            if (amim_runt !== '') {
                $('#amim_runt').val(amim_runt);
            }
            if (amim_dunt !== '') {
                $('#amim_dunt').val(amim_dunt);
            }
            if (amim_acmp !== '') {
                $('#amim_acmp').val(amim_acmp);
            }
            $("#itcl_id").select2({width: 'resolve'});
            $("#itsg_id").select2({width: 'resolve'});
            $("#amim_runt").select2({width: 'resolve'});
            $("#amim_dunt").select2({width: 'resolve'});
            $("#amim_acmp").select2({width: 'resolve'});
            $("#cont_id").select2({width: 'resolve'});
        });

        var amim_fulld = document.getElementById("amim_full");
        //5MB
        const maxAllowedSize = 1 * 400 * 1024;
        amim_fulld.onchange = function () {
            if (this.files[0].size > maxAllowedSize) {
                alert("Image size is big!!! Max allowed size 400 KB");
                this.value = "";
            }
            ;
        };
        var image_icons = document.getElementById("image_icon");
        const maxAllowedSizedd = 1 * 200 * 1024;
        image_icons.onchange = function () {

            if (this.files[0].size > maxAllowedSizedd) {
                alert("Image size is big!!! Max allowed size 200 KB");
                this.value = "";
            }
            ;
        };


    </script>
@endsection