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
                            <strong>All Process List</strong>
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
                        <form id="demo-form2" data-parsley-validate
                              class="form-horizontal form-label-left"
                              action="{{ URL::to('setting/process')}}" enctype="multipart/form-data"
                              method="post">
                            {{csrf_field()}}
                            {{method_field('POST')}}
                            <div class="x_title">
                                <center><h5><strong>::: Process Run :::</strong></h5></center>
                                <div class="clearfix"></div>
                                <button type="submit" class="btn btn-success" name="sr_attendence">Run Process
                                </button>
                            </div>
                            <div class="x_content">

                                <table class="table table-bordered projects">
                                    <thead>
                                    <tr class="tbl_header_light">
                                        <th>S/L</th>
                                        <th></th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data as $index=> $data1)

                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            <td><input class='checkbox' type='radio' name='process_id[]'
                                                       value='{{$data1->code}}'></td>
                                            <td><input type="hidden" name="process[]" value="{{$data1->code}}"><input
                                                        class="date_pick" name='date[]' value="{{date('Y-m-d')}}"
                                                        type="text"/></td>

                                            <td> @if($data1->code== "sync_attendance")
                                                    <input
                                                            class="date_pick" name='date2[]' value="{{date('Y-m-d')}}"
                                                            type="text"/>
                                                @endif
                                            </td>

                                            <td>
                                                @if($data1->code== "sync_attendance")<input name='employee[]'
                                                                                            placeholder="Enter Staff ID"
                                                                                            type="text"/>@endif
                                                @if($data1->code== "att_process_hris")
                                                        {{--<select name="employee[]" id="cars" multiple>
                                                            @foreach($emp as $empList)
                                                                <option value="{{$empList->id}}">{{$empList->aemp_usnm}}
                                                                    - {{$empList->aemp_name}}</option>
                                                            @endforeach

                                                        </select>--}}
                                                    <input name='employee[]' placeholder="Enter Staff ID" type="text"
                                                           multiple/>
                                                @endif
                                            </td>
                                            <td>{{$data1->name}}</td>
                                            <td>{{$data1->code}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            $("select").select2({width: 'resolve'});

        });


        $("#select_all").change(function () {
            var status = this.checked;
            $('.checkbox:enabled').each(function () {
                this.checked = status;
            });
        });
        $(document).ready(function () {
            $('.date_pick').datetimepicker({format: 'YYYY-MM-DD'});
        });
        $('.checkbox').change(function () {
            if (this.checked == false) {
                $("#select_all")[0].checked = false;
            }
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true;
            }
        });



    </script>
@endsection