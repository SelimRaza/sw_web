@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <!-- <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                        </li>
                        <li class="active">
                            <strong>All  Order</strong>
                        </li>
                    </ol>
                </div>

                <div class="title_right">

                </div>
            </div> -->
            <div class="clearfix"></div>
            <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                                @csrf
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div  class="col-md-12 col-sm-12 col-xs-12 tracing">
                                <div  class="col-md-6 col-sm-6 col-xs-12 col-md-offset-6 col-sm-offset-6">
                                    <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                        <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                <button class="btn btn-default btn-block" type="submit" onclick="clearItem()">Clear</button>
                                            </div>
                                            <div class="col-md-5 col-sm-5 col-xs-12">
                                                <input type="text" class="form-control in_tg" id="item_code" placeholder="Item Code" name="item_code">
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                                <button class="btn btn-default btn-block" type="submit" onclick="getSingleItem()">Load Item</button>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                                <button class="btn btn-warning btn-block" type="submit" onclick="updatePriceList()">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                            <div  class="col-md-3 col-sm-3 col-xs-12 tracing">
                                <div class="form-group col-md-12 col-sm-12 gvt_filter">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <select class="form-control cmn_select2" name="acmp_id"
                                                id="acmp_id"
                                                onchange="getPldtGroup(this.value)">
                                            
                                            <option value="-1">Select Company</option>
                                            <option value="0">All</option>
                                            @foreach($acmp_list as $acmp)
                                                <option value="{{$acmp->id}}">{{$acmp->acmp_code ."-".$acmp->acmp_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-bordered table-responsive">
                                                <thead  class="select_table_head">

                                                        <th><input type="checkbox" id="all_slgp_check"> All</th>
                                                        <th>Group</th>

                                                </thead>
                                                <tbody id="slgp_cont" class="select_table_body">
                                                        
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                                
                               
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">                                   
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-default btn-block" type="submit" onclick="getPriceList()">Load PriceList</button>
                                    </div>
                                </div>

                            </div>
                            <div  class="col-md-3 col-sm-3 col-xs-12 tracing" >
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                            <table class="table table-bordered table-responsive">
                                                <thead  class="select_table_head">

                                                        <th><input type="checkbox" id="all_plmt_check"> All</th>
                                                        <th>PriceList</th>

                                                </thead>
                                                <tbody id="plmt_cont" class="select_table_body1">
                                                    <tr>
                                                        <td><input type="checkbox" name="plmt_id[]" value="1"></td>
                                                        <td>PLMT001</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">                                   
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-default btn-block" type="submit" onclick="getSelectedPlmtItems()">Load Items</button>
                                    </div>
                                </div>
                            </div>
                            <div  class="col-md-6 col-sm-6 col-xs-12 tracing" >
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">
                                    <div class="col-md-12 col-sm-12 col-xs-12" style="height:450px;overflow: auto;">
                                        {{-- style="height:450px;overflow: auto;" style="overflow-x: auto; overflow-y:auto;" --}}
                                            <table class="table table-bordered table-responsive">
                                                <thead  class="tbl_header" style="position:sticky; inset-block-start:0;">
                                                    <tr>
                                                        <th><input type="checkbox" id="all_item"> All</th>
                                                        <th>PriceList</th>
                                                        <th>Item Code</th>
                                                        <th>Item Name</th>
                                                        <th>Item TPPR(pics)</th>
                                                    </tr>

                                                </thead>
                                                <tbody id="amim_cont" class="select_table_body2">
                                                   
                                                </tbody>
                                            </table>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 col-sm-12 col-xs-12 gvt_filter">                                   
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-warning btn-block" type="submit" onclick="updatePriceList()">Update Price</button>
                                    </div>
                                </div>
                            </div>
                            
                        
                            <!-- end field -->
                        </div>
                    </div>
                </div>
            
        </div>
    </div>
    <style>
        .select_table_body, .select_table_head,.select_table_body1 { display: block; }
        .select_table_body {
            height: 250px;       /* Just for the demo          */
            overflow-y: auto;    /* Trigger vertical scroll    */
            overflow-x: hidden;  /* Hide the horizontal scroll */
        }
        .select_table_body1 {
            height: 295px;       /* Just for the demo          */
            overflow-y: auto;    /* Trigger vertical scroll    */
            overflow-x: hidden;  /* Hide the horizontal scroll */
        }
        .select_table_body2 {
            height: 450px;       /* Just for the demo          */
            overflow-y: auto;    /* Trigger vertical scroll    */
            overflow-x: hidden;  /* Hide the horizontal scroll */
        }
        .tracing1{

            padding:15px;
        }
    </style>
    <script type="text/javascript">
        $('.cmn_select2').select2();
        $('#all_slgp_check').on('click', function(e) {
            if($(this).is(':checked',true)){
                $(".sub_chk").prop('checked', true);
            } else {
                $(".sub_chk").prop('checked',false);
            }

        });
        $('#all_plmt_check').on('click', function(e) {
            if($(this).is(':checked',true)){
                $(".sub_chk1").prop('checked', true);
            } else {
                $(".sub_chk1").prop('checked',false);
            }

        });
        $('#all_item').on('click', function(e) {
            if($(this).is(':checked',true)){
                $(".sub_chk2").prop('checked', true);
            } else {
                $(".sub_chk2").prop('checked',false);
            }

        });
        function getPriceList(){
            var slgp_id = [];
            var _token =$('#_token').val();
            $.each($("input[name='slgp_id']:checked"), function(){
                slgp_id.push($(this).val());
            });
            console.log(slgp_id);
            $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/pricelist/update/plmt",
                    data: {
                        slgp_id: slgp_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#ajax_load').css("display", "none");
                        var html='';
                        for (var i = 0; i < data.length; i++) {
                            html+=`<tr>
                                    <td><input type="checkbox" name="plmt_id" value="${data[i]['id']}" class="sub_chk1"></td>
                                    <td>${data[i]['plmt_code']}-${data[i]['plmt_name']}</td>
                                    `;
                        }                      
                        $("#plmt_cont").empty();
                        $("#plmt_cont").append(html);
                       
                    }
                });
        }
        function getPldtGroup(acmp_id) {
                var _token = $("#_token").val();
               // $('#ajax_load').css("display", "block");
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/pricelist/update/slgp",
                    data: {
                        acmp_id: acmp_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        //console.log(data);
                        $('#ajax_load').css("display", "none");
                        var html='';
                        for (var i = 0; i < data.length; i++) {
                            html+=`<tr>
                                    <td><input type="checkbox" name="slgp_id" value="${data[i]['id']}" class="sub_chk"></td>
                                    <td>${data[i]['slgp_code']}-${data[i]['slgp_name']}</td>
                                    `;
                        }                      
                        $("#slgp_cont").empty();
                        $("#slgp_cont").append(html);
                       
                    }
                });
        }
        function getSelectedPlmtItems(){
            var _token = $("#_token").val();
            let plmt_id=[];
            $.each($("input[name='plmt_id']:checked"), function(){
                plmt_id.push($(this).val());
            });
            $('#ajax_load').css("display", "block");
            $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/get/plmt_all/items",
                    data: {
                        plmt_id: plmt_id,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#ajax_load').css("display", "none");
                        var html='';
                        for (var i = 0; i < data.length; i++) {
                            html+=`<tr>
                                    <td><input type="checkbox" name="amim_id" value="${data[i]['id']}" class="sub_chk2"></td>
                                    <td>${data[i]['plmt_name']}</td>
                                    <td>${data[i]['amim_code']}</td>
                                    <td>${data[i]['amim_name']}</td>
                                    <td><input type="number" class="form-control in_tg amim_tppr" name="amim_tppr" value="${data[i]['pldt_tppr']}"></td>
                                    </tr>`;
                        }                      
                        $("#amim_cont").empty();
                        $("#amim_cont").append(html);
                       
                    },
                    error:function(error){
                        $('#ajax_load').css("display", "none");
                        swal.fire({
                            icon:'warning',
                            text:'Please select pricelist'
                        });
                    }
            });
        }
        function getSingleItem(){
            let amim_code=$('#item_code').val();
            var _token = $("#_token").val();
            $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/get/single/item",
                    data: {
                        amim_code: amim_code,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('#ajax_load').css("display", "none");
                        var html='';
                        for (var i = 0; i < data.length; i++) {
                            html+=`<tr>
                                    <td><input type="checkbox" name="amim_id" value="${data[i]['id']}" class="sub_chk2"></td>
                                    <td>${data[i]['plmt_name']}</td>
                                    <td>${data[i]['amim_code']}</td>
                                    <td>${data[i]['amim_name']}</td>
                                    <td><input type="number" class="form-control in_tg amim_tppr" name="amim_tppr" value="${data[i]['pldt_tppr']}"></td>
                                    </tr>`;
                        }                      
                        //$("#amim_cont").empty();
                        $("#amim_cont").append(html);
                       
                    },
                    error:function(error){
                        console.log(error);
                    }
            });

        }
        // Update Price 
        function updatePriceList(){
            var _token = $("#_token").val();
            let plmt_id=[];
            let amim_id=[];
            let pldt_tppr=[];
            let amim_tppr=0;
            let id=0;
            $.each($("input[name='plmt_id']:checked"), function(){
                plmt_id.push($(this).val());
            });
            $.each($("input[name='amim_id']:checked"), function(){
                amim_id.push($(this).val());
                id=$(this).val();
                amim_tppr=$(this).closest('tr').find('.amim_tppr').val();
                pldt_tppr.push({'amim_id':id,'pldt_tppr':amim_tppr});
            });
            if(amim_id.length<=0){
                swal.fire({
                    icon:'warning',
                    text:'Please Select Item !'
                });
                return false;
            }
            $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/update/item-price",
                    data: {
                        plmt_id: plmt_id,
                        amim_id: amim_id,
                        pldt_tppr: pldt_tppr,
                        _token: _token
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        if(data==1){
                            swal.fire({
                                icon:'success',
                                text:'Price Updated Successfully'
                            });
                        }
                        else{
                            swal.fire({
                                icon:'warning',
                                text:'Please select pricelist'
                            });
                        }
                       //  clearItem();
                        
                       
                    },
                    error:function(error){
                        swal.fire({
                            icon:'warning',
                            text:'Something Went Wrong !'
                        });
                       // clearItem();
                    }
            });

        }
        function clearItem(){
            $("#amim_cont").empty();
        }



        function exportTableToCSV(filename, tableId) {
                // alert(tableId);
                var csv = [];
                var rows = document.querySelectorAll('#' + tableId + '  tr');
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length; j++)
                        row.push(cols[j].innerText);
                    csv.push(row.join(","));
                }
                downloadCSV(csv.join("\n"), filename);
            }

            function downloadCSV(csv, filename) {
                var csvFile;
                var downloadLink;
                csvFile = new Blob([csv], {type: "text/csv"});
                downloadLink = document.createElement("a");
                downloadLink.download = filename;
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
                downloadLink.click();
            }
    </script>
@endsection