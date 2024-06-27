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
                            <strong>All Order</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">

                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Company<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="acmp_id" id="acmp_id"
                                                onchange="getGroup(this.value, $('#_token').val())">

                                            <option value="">Select Company</option>
                                            @foreach ($acmps as $acmp)
                                                <option value="{{ $acmp->id }}">
                                                    {{ $acmp->acmp_code . '-' . $acmp->acmp_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="slgp_id">Group<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="slgp_id" id="slgp_id">
                                            <option value="">Select Group</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="start_date">Start Date<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg start_date" name="start_date"
                                               id="start_date" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="end_date">End Date<span
                                                class="required">*</span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg start_date" name="end_date"
                                               id="end_date" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="staff_id">Staff Id<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="staff_id"
                                               placeholder="Staff Id" name="staff_id">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">

                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="site_code">Site Code<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="site_code"
                                               placeholder="Site Code" name="site_code">
                                    </div>
                                </div>
                                <div class="item form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="ordm_ornm">Order No<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="text" class="form-control in_tg" id="ordm_ornm"
                                               placeholder="Order no" name="ordm_ornm">
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-4 col-xs-12 gvt_filter">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12" for="acmp_id">Order Status<span
                                                class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" id="lfcl_id" name="lfcl_id">

                                            <option value="">Select Status</option>
                                            @foreach ($lfcl_list as $lfcl)
                                                <option value="{{ $lfcl->id }}">
                                                    {{$lfcl->lfcl_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div align="right">
                            <button onclick="filterData()" class="btn btn-success">Search</button>

                        </div>
                    </div>
                </div>
            </div>
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
                              action="{{ URL::to('order_report/pushToRoutePlan')}}" enctype="multipart/form-data"
                              method="post">
                            {{csrf_field()}}
                            {{method_field('POST')}}

                            <div class="x_title">
                                <h1>Maintain Order</h1>

                                <ul class="nav navbar-left panel_toolbox">
                                    {{--  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                        <button type="submit" class="btn btn-success" name="sr_attendence">Push for
                                            RoutePlan
                                        </button>
                                    </li>  --}}
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
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <table class="table table-striped projects">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select_all"/></th>
                                        <th>SL</th>
                                        <th>Order No</th>
                                        <th>Order Amount</th>
                                        <th>Date</th>
                                        <th> Date Time</th>
                                        <th>Staff ID</th>
                                        <th>Emp Name</th>
                                        <th>Outlet id</th>
                                        <th>Outlet Code</th>
                                        <th>Outlet Name</th>
                                        <th>Order Type</th>
                                        <th>Status</th>
                                        <th>Trip</th>
                                        <th>DM ID</th>
                                        <th colspan="3">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="cont">

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
        $('#start_date').datepicker({dateFormat: 'yy-mm-dd'});
        $('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
        $('.cmn_select2').select2();

        $("#select_all").change(function () {  //"select all" change
            var status = this.checked; // "select all" checked status
            $('.checkbox:enabled').each(function () { //iterate all listed checkbox items
                this.checked = status;
                //change ".checkbox" checked status
            });
        });

        $('.checkbox').change(function () { //".checkbox" change
            //uncheck "select all", if one of the listed checkbox item is unchecked
            if (this.checked == false) { //if this item is unchecked
                $("#select_all")[0].checked = false; //change "select all" checked status to false
            }

            //check "select all" if all checkbox items are checked
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $("#select_all")[0].checked = true; //change "select all" checked status to true
            }
        });

        function filterData() {
            let acmp_id = $("#acmp_id").val();
            let slgp_id = $("#slgp_id").val();
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            let emp_id = $("#staff_id").val();
            let site_code = $("#site_code").val();
            let ordm_ornm = $("#ordm_ornm").val();
            let lfcl_id = $("#lfcl_id").val();
            let _token = $("#_token").val();
            $('#ajax_load').css("display", "block");
            $.ajax({
                type: "POST",
                url: "{{ URL::to('/')}}/order_report/filterOrderSummary",
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    emp_id: emp_id,
                    _token: _token,
                    acmp_id: acmp_id,
                    slgp_id: slgp_id,
                    site_code: site_code,
                    ordm_ornm: ordm_ornm,
                    lfcl_id: lfcl_id
                },
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $("#cont").empty();
                    $('#ajax_load').css("display", "none");
                    var html = '';
                    var count = 1;

                    for (var i = 0; i < data.length; i++) {
                        var readonly1 = '';
                        if (data[i].status_id != 1) {
                            readonly1 = 'disabled readonly'
                        }
                        html += '<tr>' +
                            "<td><input " + readonly1 + "  class='checkbox' type='checkbox' name='so_id[]' value='" + data[i].so_id + "'></td>" +
                            '<td>' + count + '</td>' +
                            '<td>' + data[i].order_id + '</td>' +
                            '<td>' + data[i].order_amount + '</td>' +
                            '<td>' + data[i].order_date + '</td>' +
                            '<td>' + data[i].order_date_time + '</td>' +
                            '<td>' + data[i].user_name + '</td>' +
                            '<td>' + data[i].emp_name + '</td>' +
                            "<td>" + data[i].site_id + "</td>" +
                            "<td>" + data[i].site_code + "</td>" +
                            "<td>" + data[i].site_name + "</td>" +
                            "<td>" + data[i].order_type + "</td>" +
                            "<td>" + data[i].status_name + "</td>"+
                            "<td>" + data[i].TRIP_NO + "</td>"+
                            "<td>" + data[i].DM_ID + "</td>";
                        html += "<td><a target='_blank' href='{{ URL::to('/')}}/printer/order/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Order Print </a></td>";
                        if (data[i].status_id == 11 || data[i].status_id == 39) {
                            html += "<td><a target='_blank' href='{{ URL::to('/')}}/printer/salesInvoice_ou/" + data[i].cont_id + "/" + data[i].order_id + "/1"+"' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Sales Invoice Print </a></td>";
                        }
                        if(data[i].status_id==1){
                            html+='<td><a  href="#" class="btn btn-danger btn-xs" value="'+data[i].order_id+'" onclick="cancelOrder(this)"><i class="fa fa-pencil"></i> Cancel Order </a></td>';
                        }
                        html += '</tr>';
                        count++;
                    }

                    $("#cont").append(html)


                }
            });
        }
        function cancelOrder(order_ornm){

            var id=$(order_ornm).attr('value');
            var start_date=$('#start_date').val();
            var end_date=$('#end_date').val();

            Swal.fire({
                title: 'Are you sure?',
                text: "About cancelling this Order!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes,'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#ajax_load').css("display", "block");
                    $.ajax({
                        type:"GET",
                        url:"{{URL::to('/')}}/cancelOrder/"+id+'/'+start_date+'/'+end_date,
                        cache:"false",
                        success:function(data){
                            console.log(data);
                            $('#ajax_load').css("display", "none");
                            $("#cont").empty();
                            var html = '';
                            var count = 1;

                            for (var i = 0; i < data.length; i++) {
                                var readonly1 = '';
                                if (data[i].status_id != 1) {
                                    readonly1 = 'disabled readonly'
                                }
                                html += '<tr>' +
                                    "<td><input " + readonly1 + "  class='checkbox' type='checkbox' name='so_id[]' value='" + data[i].so_id + "'></td>" +
                                    '<td>' + count + '</td>' +
                                    '<td>' + data[i].order_id + '</td>' +
                                    '<td>' + data[i].order_amount + '</td>' +
                                    '<td>' + data[i].order_date + '</td>' +
                                    '<td>' + data[i].order_date_time + '</td>' +
                                    '<td>' + data[i].user_name + '</td>' +
                                    '<td>' + data[i].emp_name + '</td>' +
                                    "<td>" + data[i].site_id + "</td>" +
                                    "<td>" + data[i].site_code + "</td>" +
                                    "<td>" + data[i].site_name + "</td>" +
                                    "<td>" + data[i].order_type + "</td>" +
                                    "<td>" + data[i].status_name + "</td>";
                                html += "<td><a target='_blank' href='{{ URL::to('/')}}/printer/order/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Order Print </a></td>";
                                if (data[i].status_id == 11) {
                                    html += "<td><a target='_blank' href='{{ URL::to('/')}}/printer/salesInvoice/" + data[i].cont_id + "/" + data[i].order_id + "' class='btn btn-info btn-xs'><i class='fa fa-pencil'></i> Sales Invoice Print </a></td>";
                                }
                                if(data[i].status_id==1){
                                    html+='<td><a  href="#" class="btn btn-danger btn-xs" value="'+data[i].order_id+'" onclick="cancelOrder(this)"><i class="fa fa-pencil"></i> Cancel Order </a></td>';
                                }
                                html += '</tr>';
                                count++;
                            }

                            $("#cont").append(html)
                        },
                        error:function(error){
                            $('#ajax_load').css("display", "none");

                            console.log(error);
                        }
                    });

                    Swal.fire(
                        'Order Cancelled',
                        'success'
                    )
                }
            })



        }
    </script>
    <script>
            const WindowUrl =  window.origin;
function getGroup(company_id,_token) {
    console.log(company_id)
    console.log(_token)
    // $('#ajax_load').css("display", "block");
    $.ajax({
        type: "POST",
        url: WindowUrl+"/load/report/getGroup",
        data: {
            slgp_id: company_id,
            _token: _token
        },
        cache: false,
        dataType: "json",
        success: function(data) {
            $('#ajax_load').css("display", "none");
            var html = '<option value="">Select</option>';
            for (var i = 0; i < data.length; i++) {
                html += '<option value="' + data[i].id + '">' + data[i].slgp_code + " - " + data[i]
                    .slgp_name + '</option>';
            }
            $('#slgp_id').empty();
            $('#slgp_id').append(html);
            // console.log(data)

        }
    });
}
</script>
@endsection

@push('custom-script')
    <script  type="text/javascript" >

    </script>
@endpush