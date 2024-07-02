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
                            <a href="{{ URL::to('/company')}}">All Company</a>
                        </li>
                        <li class="active">
                            <strong>Show Company</strong>
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
                            <h1>Company</h1>
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

                            <form class="form-horizontal form-label-left"
                                  action="{{route('company.update',$company->id)}}"
                                  method="post">
                                {{csrf_field()}}
                                <div class="col-md-6">

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="name" value="{{$company->acmp_name}}"
                                                   placeholder="Name" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Code <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="code" value="{{$company->acmp_code}}"
                                                   placeholder="Code" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Note
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_note" value="{{$company->acmp_note}}"
                                                   placeholder="Note"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Short Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_snam" value="{{$company->acmp_snam}}"
                                                   placeholder="Short Name"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat Number
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_nvat" value="{{$company->acmp_nvat}}"
                                                   placeholder="Vat Number"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Excise Number
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_nexc" value="{{$company->acmp_nexc}}"
                                                   placeholder="Excise Number"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Reg Number
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_creg" value="{{$company->acmp_creg}}"
                                                   placeholder="Reg Number" r type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Address <span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_addr" value="{{$company->acmp_addr}}"
                                                   placeholder="Address" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Vat Status
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" <?php echo $company->acmp_vats == "1" ? "checked" : "" ?>
                                                   name="acmp_vats" type="checkbox"
                                            >
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Sales Invoice Title<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_titl" value="{{$company->acmp_titl}}"
                                                   placeholder="Sales Invoice Title" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="ln_solid"></div>
                                </div>
                                <div class="col-md-6">

                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Return Invoice Title<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_rttl" value="{{$company->acmp_rttl}}"
                                                   placeholder="Return Invoice Title" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Customer code
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_scod" value="{{$company->acmp_scod}}"
                                                   placeholder="Customer code"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Account code
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_acod" value="{{$company->acmp_acod}}"
                                                   placeholder="Account code"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">IBAN code
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_iban" value="{{$company->acmp_iban}}"
                                                   placeholder="IBAN code"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Bank Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_bank" value="{{$company->acmp_bank}}"
                                                   placeholder="Bank Name"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Branch Name
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_brnc" value="{{$company->acmp_brnc}}"
                                                   placeholder="Branch Name"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">SWFT Code
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_swft" value="{{$company->acmp_swft}}"
                                                   placeholder="SWFT Code"  type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Currency<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_crnc" value="{{$company->acmp_crnc}}"
                                                   placeholder="Currency" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Round Digit<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_dgit" value="{{$company->acmp_dgit}}"
                                                   placeholder="Round Digit" required="required" type="text">
                                        </div>
                                    </div>
                                    <div class="item form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Invoice Round Digit<span
                                                    class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <input readonly id="name" class="form-control col-md-7 col-xs-12"
                                                   data-validate-length-range="6" data-validate-words="2" name="acmp_rond" value="{{$company->acmp_rond}}"
                                                   placeholder="Invoice Round Digit" required="required" type="text">
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
@endsection