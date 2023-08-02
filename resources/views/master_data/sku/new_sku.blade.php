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
                            <strong>All SKU</strong>
                        </li>

                    </ol>
                </div>

                <form action="{{ URL::to('/sku')}}" method="get">
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

                <div class="title_right">

                </div>
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
                            <h4>New Item List</h4>
                            
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            {{$skus->appends(Request::only('search_text'))->links()}}
                            <table id="datatable" class="table table-bordered projects" data-page-length='500'>
                                <thead>
                                <tr class="tbl_header">
                                    <th>SL</th>
                                    <th>code</th>
                                    <th>Name</th>
                                    <th>Factor</th>
                                    <th>Unit</th>
                                    <th>Class</th>
                                
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($skus as $index=>$sku)
                                    <tr> 
                                        <td>{{$index+1}}</td>
                                        <td>{{$sku->item_code}}</td>
                                        <td>{{$sku->name}}</td>
                                        
                                        <td>{{$sku->factor}}</td>
                                        <td>{{$sku->r_unit}}</td>
                                        <td>{{$sku->item_class}}</td>
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