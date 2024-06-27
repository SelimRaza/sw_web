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
                            <a href="{{ URL::to('/district')}}">All District</a>
                        </li>
                        <li class="active">
                            <strong>Edit District</strong>
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
                            <center><strong>::: District :::</strong></center>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <form class="form-horizontal form-label-left" action="{{route('district.update',$district->id)}}"
                                  method="post">
                                {{csrf_field()}}
                                {{method_field('PUT')}}

                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"  style="text-align: left">Division <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control" name="disn_id" id="disn_id" required>
                                            <option value="">Select Division</option>
                                            @foreach ($govDivisions as $govDivision1)
                                                <option value="{{ $govDivision1->id }}">{{ ucfirst($govDivision1->disn_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name"  style="text-align: left">Name <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="dsct_name"
                                               value="{{$district->dsct_name}}"
                                               placeholder="Name" required="required" type="text">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="name" style="text-align: left">Code <span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input id="name" class="form-control col-md-7 col-xs-12"
                                               data-validate-length-range="6" data-validate-words="2" name="dsct_code"
                                               value="{{$district->dsct_code}}"
                                               placeholder="Code" required="required" type="text">
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <button id="send" type="submit" class="btn btn-primary btn-sm"><span
                                                    class="fa fa-check-circle" style="color: white; font-size: 1.3em"></span> <b>Submit</b></button>
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
        $(document).ready(function() {
            $("#disn_id").select2({width: 'resolve'});
            const disn_id = '{{ $district->disn_id }}';
            if(disn_id !== '') {
                $('#disn_id').val(disn_id);
            }

        });
    </script>
@endsection