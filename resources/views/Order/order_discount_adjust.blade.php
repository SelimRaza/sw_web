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
                            <strong>Order</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <form action="{{ URL::to('/order')}}" method="get">
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group">

                            <input type="text" class="form-control" name="search_text" placeholder="Search for..."
                                    value="">
                            <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">Go!</button>
                </span>

                        </div>
                    </div>
                </div>
            </form>
      
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
                            @if($permission->wsmu_crat)

                                <a href="{{ URL::to('order/create')}}" class="btn btn-success btn-sm">Place Order</a>


                            @endif

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <button class="btn btn-danger btn-sm"
                                    onclick="exportTableToCSV('default_discount_list_<?php echo date('Y_m_d'); ?>.csv','datatabless')"
                                    style="float: right"><span
                                        class="fa fa-cloud-download" style="color: white; font-size: 1.3em"></span>&nbsp;&nbsp;<b>Download
                                    File</b></button>
                            <table id="datatabless" class="table search-table font_color" data-page-length='50'>
                                <thead id="item_head">
                                <tr class="tbl_header_light text-center">
                                    <th class="cell_left_border">SL</th>
                                    <th>Item Name</th>
                                    <th>Item Code</th>
                                    <th>CTN</th>
                                    <th>PCS</th>
                                    <th>Unit Price</th>
                                    <th>Special Disc</th>
                                    <th>Sub Total</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                                </thead>
                                <tbody id="cont">
                                    <form style="display:inline"
                                                        action="#"
                                                        class="pull-xs-right5 card-link" method="POST">
                                                    {{csrf_field()}}
                                    @foreach($data as $i=>$d)
                                        @if($i==0)

                                            <input type="hidden" name="tmp_order_no" id="tmp_order_no" value="{{$d->tmp_ordm_ornm}}">
                                        @endif
                                            @php
                                                $sb_total=(($d->amim_ctn*$d->amim_duft)+$d->amim_pics)*$d->amim_tppr;
                                                if($d->sp_disc>0){
                                                    if($d->is_percent==1){
                                                        $sb_total=$sb_total-($sb_total*$d->sp_disc/100);
                                                    }else{
                                                        $sb_total=$sb_total-$d->sp_disc;
                                                    }
                                                }
                                            @endphp
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td id="amim_name"><input type="text" class="form-control" value="{{$d->amim_name}}" disabled><input type="hidden" name="item_list[]" value="{{$d->amim_id}}"></td>
                                        <td id="amim_code"><input type="text" class="form-control" value="{{$d->amim_code}}" disabled></td>
                                        <td><input type="text" class="form-control" value="{{$d->amim_ctn}}" disabled><input type="hidden" name="item_ctn[]" value="{{$d->amim_ctn}}"></td>
                                        <td><input type="text" class="form-control" value="{{$d->amim_pics}}" disabled><input type="hidden" name="item_pics[]" value="{{$d->amim_id}}"></td>
                                        <td><input type="text" class="form-control" value="{{$d->amim_tppr}}" disabled></td>
                                        <td><input type="text" class="form-control" value="{{$d->sp_disc}}{{$d->sp_disc && $d->is_percent==0?'(Amount)':'(%)'}}" disabled></td>
                                        <td><input type="text" class="form-control" value="{{$sb_total}}" disabled><input type="hidden" name="item_sb_total[]" value="{{$d->amim_id}}"></td>
                                        <td>
                                            <a href="#"  class=" btn btn-primary btn-xs" value={{$d->id}} onclick="adjustDiscount(this)">Adjust</a>
                                            <a   class=" btn btn-danger btn-xs" id={{$d->id}} onclick="deleteItem(this)">Delete</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </form>
                                </tbody>
                            </table>
                            <div class="col-md-4 col-md-offset-8">
                                <table id="calculation_table" class= "table table-responsive table-stripe table-border">
                                
                                <tbody id="calc_cont">
                                    <tr class="hm-shadow"><td colspan="2">Order Summary</td></tr>
                                    <tr class="white"><td>NET SALES</td><td id="nt">0.00</td></tr>
                                    <tr class="white"><td  >EXCISE</td><td id="ex">0.00</td></tr>
                                    <tr class="white"><td  >VAT</td><td id="vt">0.00</td></tr>
                                    <tr class="white"><td  >TOTAL</td><td id="tt">0.00</td></tr>
                                    <tr class="white"></tr>
                                    <tr><td id="final_order"><button class="btn btn-success" onclick="getTotalPayablePrice()">Calculate</button></td><td id="order_save"></td></tr>
                                </tbody>

                            </table>
                            <span id="ins"></span>
                            </div>
                            
                           
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{--promotion data extend modal end--}}
    <script type="text/javascript">

        function exportTableToCSV(filename, tableId) {
            var csv = [];
            var rows = document.querySelectorAll('#' + tableId + '  tr');
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length - 1; j++)
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


    <script type="text/javascript">
       function deleteItem(v){
            var id=$(v).attr('id');
            $(v).closest("tr").remove();
            
            $.ajax({
            type: "GET",
            url: "/temp_ord/remove/"+id,
            cache: false,
            dataType: "json",
            success: function (data) {
               swal.fire(
                   'Item Removed',
               )
            },error:function(error){
                console.log(error);
            }
        });
       }
       function adjustDiscount(v){
            var id=$(v).attr('value'); 
            $(v).removeAttr('class');
            $.ajax({
                type: "GET",
                url: "{{ URL::to('/')}}/adjustDiscount/"+id,
                cache: false,
                dataType: "json",
                success: function (data) {
                    console.log(data)
                 var data2=data.data2;
                 var data=data.data1;
                 if(data.dfdm_id !='' && data.prmr_id !=''){
                    Swal.fire({
                        title: 'What Do You Want?',
                        showDenyButton: true,
                        confirmButtonText: 'Default Discount',
                        denyButtonText: `Promotion`,
                        }).then((result) => {
                        if (result.isConfirmed) {
                            choiceSetup(id,'DFLT');
                            var b=$(v).closest("tr")
                            var ind= $('table tr').index(b);
                            var html='<tr><td>'+ind+'</td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_name+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_code+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_ctn+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_pics+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_tppr+'"></td>';
                            var sp_disc=data.is_percent==1?'(%)':'(Amount)';
                            html +='<td><input type="text" disabled class="form-control" value="'+data.sp_disc+''+sp_disc+'"></td>'+
                                    '<td><input type="text" disabled class="form-control" value="'+data.dfdm_pay_prce+'"></td>'+
                                    '<td><a class="btn btn-default btn-xs" value="'+data.id+'" onclick="adjustDiscount(this)">Adjust</a>'+
                                    '<span class="badge">Default</span><a class="btn btn-danger btn-xs" id="'+data.id+'" onclick="deleteItem(this)">Delete</a></td></tr>';
                            
                            $('#datatabless > tbody > tr').eq(ind-1).after(html);
                            $(v).closest("tr").remove();
                            $('.'+data.amim_id).remove();


                        } else if (result.isDenied) {
                            choiceSetup(id,'PROM');
                            $(v).parent().next("span").remove();
                            if(data.is_foc==1){
                                getFreeItemDetails(data.amim_id,v,data);
                                
                            }else{
                                var b=$(v).closest("tr")
                                var ind= $('table tr').index(b);
                                var html='<tr><td>'+ind+'</td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_name+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_code+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_ctn+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_pics+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_tppr+'"></td>';
                            var sp_disc=data.is_percent==1?'(%)':'(Amount)';
                            html +='<td><input type="text" disabled class="form-control" value="'+data.sp_disc+''+sp_disc+'"></td>'+
                                    '<td><input type="text" disabled class="form-control" value="'+data.prmr_pay_prce+'"></td>'+
                                    '<td><a class="btn btn-default btn-xs" value="'+data.id+'" onclick="adjustDiscount(this)">Adjust</a>'+
                                    '<span class="badge">Default</span><a class="btn btn-danger btn-xs" id="'+data.id+'" onclick="deleteItem(this)">Delete</a></td></tr>';
                                
                                $('#datatabless > tbody > tr').eq(ind-1).after(html);
                                $(v).closest("tr").remove();
                                $('#'+data.amim_id).remove();
                            }
                            
                            Swal.fire('Promotion Selected', '', 'info')
                        }
                    })
                 }
                 else if(data.dfdm_id !='' && data.prmr_id==''){
                            choiceSetup(id,'DFLT');
                            var b=$(v).closest("tr")
                            var ind= $('table tr').index(b);
                            var html='<tr><td>'+ind+'</td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_name+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_code+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_ctn+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_pics+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_tppr+'"></td>';
                            var sp_disc=data.is_percent==1?'(%)':'(Amount)';
                            html +='<td><input type="text" disabled class="form-control" value="'+data.sp_disc+''+sp_disc+'"></td>'+
                                    '<td><input type="text" disabled class="form-control" value="'+data.dfdm_pay_prce+'"></td>'+
                                    '<td><a class="" value="'+data.id+'" onclick="adjustDiscount(this)">Adjust</a>'+
                                    '<span class="badge">Default</span><a class="btn btn-danger btn-xs" id="'+data.id+'" onclick="deleteItem(this)">Delete</a></td></tr>';
                            
                            $('#datatabless > tbody > tr').eq(ind-1).after(html);
                            $(v).closest("tr").remove();
                            $('#'+data.amim_id).remove();
                 }
                 else if(data.prmr_id !='' && data.dfdm_id==''){
                        choiceSetup(id,'PROM');
                        if(data.is_foc==1){
                                getFreeItemDetails(data.amim_id,v,data);
                                
                            }else{
                                var b=$(v).closest("tr")
                                var ind= $('table tr').index(b);
                                var html='<tr><td>'+ind+'</td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_name+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data2.amim_code+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_ctn+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_pics+'"></td>'+
                                        '<td><input type="text" disabled class="form-control" value="'+data.amim_tppr+'"></td>';
                            var sp_disc=data.is_percent==1?'(%)':'(Amount)';
                            html +='<td><input type="text" disabled class="form-control" value="'+data.sp_disc+''+sp_disc+'"></td>'+
                                    '<td><input type="text" disabled class="form-control" value="'+data.prmr_pay_prce+'"></td>'+
                                    '<td><a class="" value="'+data.id+'" onclick="adjustDiscount(this)">Adjust</a>'+
                                    '<span class="badge">Default</span><a class="btn btn-danger btn-xs" id="'+data.id+'" onclick="deleteItem(this)">Delete</a></td></tr>';
                                
                                $('#datatabless > tbody > tr').eq(ind-1).after(html);
                                $(v).closest("tr").remove();
                                $('#'+data.amim_id).remove();
                            }
                 }
                 
                },error:function(error){
                    console.log(error);
                }
            }); 
       }
       function choiceSetup(id,text){
        $.ajax({
            type: "GET",
            url: "/order_promotion_or_dflt_disc_choice_setup/"+id+'/'+text,
            cache: false,
            dataType: "json",
        });
       }
       function calculatePayablePrice(id){
        $.ajax({
            type: "GET",
            url: "/calculatePayablePrice/"+id,
            cache: false,
            dataType: "json",
            success:function(data){
                return data;
            }
        });
       }
       function getFreeItemDetails(id,v,p_data){
        $.ajax({
            type: "GET",
            url: "/getFreeItemDetails/"+id,
            cache: false,
            dataType: "json",
            success:function(data){
                var b=$(v).closest("tr")
                var ind= $('table tr').index(b);
                var html='<tr class="'+p_data.amim_id+'"><td><span class="badge">FOC</span></td>'+
                            '<td>'+data.amim_name+'</td>'+
                            '<td>'+data.amim_code+'</td>'+
                            '<td></td>'+
                            '<td>'+p_data.fitm_qty+'</td>'+
                            '<td><span class="badge">FOC</span></td>'+
                            '<td><span class="badge">FOC</span></td>'+
                            '<td><span class="badge">FOC</span></td>'+
                            '<td><span class="badge">FOC</span></td>';
               
                
                $('#datatabless > tbody > tr').eq(ind-1).after(html);
                var b=$(v).next()[0].localName;
                if(b=='span'){
                    $(v).next().remove();
                }
            }
        });
       }

       function getTotalPayablePrice(){
           var order_no=$('#tmp_order_no').val();
            $.ajax({
                type: "GET",
                url: "/getTotalPayablePrice/"+order_no,
                cache: false,
                dataType: "json",
                success:function(data){
                    
                    $('#nt').empty();
                    $('#ex').empty();
                    $('#vt').empty();
                    $('#tt').empty();
                    $('#nt').append(data.total);
                    $('#ex').append(data.exec);
                    $('#vt').append(data.vat);
                    var payable_prce=data.total+(data.exec+data.vat);
                    $('#tt').append(payable_prce);
                    
                    $('#order_save').empty();
                   
                    var btn='';
                    if(data.flag==1){
                        btn='<button class="btn btn-danger" onclick="saveOrder()">Place Order</button>';
                    }
                    else{
                        $('#ins').empty();
                        btn='<button class="btn btn-danger" onclick="saveOrder()" disabled>Place Order</button>';
                        $('#ins').append('Adjust all item to Enable Order Place Option');
                    }
                    $('#order_save').append(btn);
                    
                   

                }
            });
       }
       function saveOrder(){
        var order_no=$('#tmp_order_no').val();
        var vat=$('#vt').html();
        var exec=$('#ex').html();
        var net_amount=$('#nt').html();
        var total=$('#tt').html();
       
        $.ajax({
            type: "GET",
            url: "/order/save/"+order_no,
            cache: false,
            dataType: "json",
            success:function(data){
                $('#order_save').empty();
                btn='<button class="btn btn-danger" onclick="saveOrder()" disabled>Place Order</button>';
                $('#order_save').append(btn);
                swal.fire(
                    'success',
                    'Order Place Successfully'
                )
            },error:function(error){
                swal.fire(
                    'Warning!',
                    'Order Already Placed'
                )
                console.log(error)
            }
        });
       }
    </script>

   
    <style>
    .hm-shadow{
        background-color: rgb(19,79,92);
        box-shadow: 0px 5px 5px black;
        -moz-box-shadow: 0px 2px 2px black;
        -webkit-box-shadow: 0px 3px 3px black;
    }
    .white{
        background-color:#fff;
        box-shadow: 0px 2px 2px gray;
        -moz-box-shadow: 0px 2px 2px gray;
        -webkit-box-shadow: 0px 2px 2px gray;
    }

/* #datatabless {margin:0 auto; border-collapse:separate;}
#item_head {background:#CCCCCC;display:block}
#cont {height:420px;overflow-y:scroll;display:block} */
#datatabless{
    text-align: left;
  position: relative;
  border-collapse: collapse; 
}
#item_head{
    background: white;
  position: sticky;
  top: 0; /* Don't forget this, required for the stickiness */
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}
#datatabless {height:420px;overflow-y:scroll;display:block}
    </style>
     <script>
//      var colNumber=9 
// for (var i=0; i<colNumber; i++)
//   {

//     $("#datatabless").find("th:eq("+i+")").css('text-align','center');
//       var thWidth=$("#datatabless").find("th:eq("+i+")").width();
//       var tdWidth=$("#datatabless").find("td:eq("+i+")").width();   
//       var wd=tdWidth+20;   
//       var wd1=tdWidth+10;   
//       if (thWidth<tdWidth)                    
//           $("#datatabless").find("th:eq("+i+")").width(wd);
//       else
//           $("#datatabless").find("td:eq("+i+")").width(wd);           
//   }  
    </script>
@endsection
