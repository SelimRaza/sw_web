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
                            <a href="{{ URL::to('collection/maintainCollection')}}">Collection Maintain</a>
                        </li>
                        <li class="active">
                            <strong>Collection View</strong>
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
                            <h1>Collection View</h1>
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
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="usr">Outlet:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->outlet_name}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Payment Id:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->payment_id}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Note:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->note}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Created Date:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->created_date}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">TRN Date:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->date}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Amount:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->amount}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">On Account:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->amount-$collection->allocated_amount}}"
                                                   readonly>
                                        </div>


                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="usr">Payment Code:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->payment_code}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Bank name:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->bank_name}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Cheque No:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->cheque_no}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Cheque Date:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->cheque_date}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Status:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->status}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Payment Type:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->payment_type}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Collection Type:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->collection_type}}" readonly>
                                        </div>



                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="usr">Crated By:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->created_by}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Verified By:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->verified_by}}"
                                                   readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Matched By:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->match_by}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Cheque Verify By:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->cheque_paid_by}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Carrying Now:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->carrying_by}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Reject Reason:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->reject_reason}}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="usr">Reject Note:</label>
                                            <input type="text" class="form-control" id="usr"
                                                   value="{{$collection->reject_note}}" readonly>
                                        </div>

                                    </div>


                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label for="usr">Cheque Image:</label>
                                    <table id="datatable" class="table table-striped table-bordered"
                                           data-page-length='25'>

                                        <tbody id="cont">
                                        <tr>


                                            @foreach($chequeImage as $index => $chequeImage1)
                                            <td><a target="_blank"
                                                   href="https://images.sihirbox.com/{{$chequeImage1->image_name}}"
                                                   class="btn btn-info btn-xs"><img
                                                            src="https://images.sihirbox.com/{{$chequeImage1->image_name}}"
                                                            class="img-responsive avatar-view">
                                                </a></td>

                                            @endforeach




                                        </tr>
                                        </tbody>
                                    </table>


                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <table id="datatable" class="table table-striped table-bordered"
                                           data-page-length='25'>
                                        <thead>
                                        <tr style="background-color: #2b4570; color: white;">
                                            <th>S/L</th>
                                            <th>Activity User</th>
                                            <th>Note</th>
                                            <th>Time</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">


                                        @foreach($chequeHistory as $index => $chequeHistory1)
                                        <tr>

                                            <td>{{$index+1}}</td>
                                            <td>{{$chequeHistory1->user}}</td>
                                            <td>{{$chequeHistory1->note}}</td>
                                            <td>{{$chequeHistory1->date_time}}</td>
                                        </tr>

                                        @endforeach


                                        </tbody>
                                    </table>


                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">

                                    <table id="datatable" class="table table-striped table-bordered"
                                           data-page-length='25'>
                                        <thead>
                                        <tr style="background-color: #2b4570; color: white;">
                                            <th>S/L</th>
                                            <th>Site Id</th>
                                            <th>Site Name</th>
                                            <th>Ref Number</th>
                                            <th>Ref Tax Number</th>
                                            <th>TRN Type</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Payment</th>
                                            <th>Matching</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">

                                        @foreach($chequeMatching as $index => $chequeMatching1)
                                        <tr>

                                            <td>{{$index+1}}</td>
                                            <td>{{$chequeMatching1->site_id}}</td>
                                            <td>{{$chequeMatching1->site_name}}</td>
                                            <td>{{$chequeMatching1->invoice_code}}</td>
                                            <td>{{$chequeMatching1->tax_invoice}}</td>
                                            <td>{{$chequeMatching1->invoice_type}}</td>
                                            <td>{{$chequeMatching1->date}}</td>
                                            <td align="right">{{$chequeMatching1->invoice_amount}} </td>
                                            <td align="right">{{$chequeMatching1->collection_amount}} </td>
                                            <td align="right">{{$chequeMatching1->matching_amount}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="9" align="right">Total</td>

                                            <td align="right">{{array_sum(array_column($chequeMatching, 'matching_amount'))}}</td>
                                        </tr>

                                        </tbody>
                                    </table>


                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection