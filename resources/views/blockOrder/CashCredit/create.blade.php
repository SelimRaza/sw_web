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
                            <a href="{{ URL::to('/cash_party_credit_budget')}}">All Cash Party Credit Budget</a>
                        </li>
                        <li class="active">
                            <strong>New Special Budget</strong>
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
                        <strong>Danger! </strong>{{ Session::get('success') }}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert-danger">
                        <strong>Danger! </strong>{{ Session::get('danger') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h3>Cash Party Credit Budget</h3>
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
                                <div class="col-md-12">
                                    <div style="padding: 10px;">
                                        <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                        <div id="sales_heirarchy" class="form-row animate__animated animate__zoomIn">                                      
                                            <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="itsg_id">Staff Id *
                                                        
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="aemp_usnm" id="aemp_usnm">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6  col-sm-6 col-xs-12" style="display:none;">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="scbm_date">Month *
                                                        
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="text" class="form-control in_tg" name="scbm_date" id="scbm_date" value="<?php echo date('Y-m')?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="type">Type
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <select class="form-control cmn_select2" name="type" id="type">                                               
                                                            <option value="1">In</option>
                                                            <option value="2">Out</option>                                                      
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="limit">Limit *
                                                        
                                                </label>
                                                <div class="col-md-8 col-sm-8 col-xs-12">
                                                    <input type="number" class="form-control in_tg" name="limit" id="limit">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12  col-sm-12 col-xs-12">
                                                <!-- <label class="control-label col-md-4 col-sm-4 col-xs-12" for="limit">Limit *
                                                        
                                                </label> -->
                                                <div class="col-md-3 col-sm-3 col-xs-12" style="float:right;">
                                                    <button type="submit" class="btn btn-danger" onclick="closeWindow()" style="float:left;">Close</button>
                                                    <button type="submit" class="btn btn-primary" onclick="checkCreditBudget()" style="float:left;">Check</button>
                                                    <button type="submit" class="btn btn-success" onclick="addCashCreditBudget()">Add Budget</button>
                                                    
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <table class="table table-striped projects">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>TRN</th>
                                            <th>Time</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#trip_type_id").select2({width: 'resolve'});
        $("#emp_id").select2({width: 'resolve'});
        $("#depot_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        $('#scbm_date').datepicker({
                dateFormat: 'yy-mm',
                minDate: '-3m',
                maxDate: '2m',
                autoclose: 1,
                showOnFocus: true
        });
        function closeWindow(){
            window.close();
        }
        function addCashCreditBudget(){
            let aemp_usnm=$('#aemp_usnm').val();
            let scbm_date=$('#scbm_date').val();
            let type=$('#type').val();
            let limit=$('#limit').val();
            let _token=$('#_token').val();
            if(aemp_usnm !='' && scbm_date !='' && type !='' && limit !=''){
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('/')}}/add/cash_party/budget",
                    data:{
                        aemp_usnm:aemp_usnm,
                        scbm_date:scbm_date,
                        type:type,
                        limit:limit,
                        _token:_token,
                    },
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        if(data==2){
                            swal.fire({
                                icon:'info',
                                text:'You can not out budget when user doesnot have any budget',
                            });
                        }
                        else if(data==3){
                            swal.fire({
                                icon:'info',
                                text:'Out limit is greater than this user limit',
                            });
                        }
                        else{
                            swal.fire({
                                icon:'success',
                                text:'Budget Added',
                            });
                        }
                        
                       
                    },
                    error:function (error){
                        console.log(error);
                        swal.fire({
                            icon:'error',
                            text:'Something went wrong!! Please check your input',
                        })
                    }
                });
            }
            else{
                swal.fire({
                    icon:'error',
                    text:'Please fill all input fields',
                })
            }
            
        }

        function checkCreditBudget(){
            let aemp_usnm=$('#aemp_usnm').val();
            let scbm_date=$('#scbm_date').val();
            if(aemp_usnm !='' && scbm_date !=''){
                $.ajax({
                    type: "GET",
                    url: "{{ URL::to('/')}}/check/cash_party/budget/"+aemp_usnm+"/"+scbm_date,
                    cache: false,
                    dataType: "json",
                    success: function (data) {
                        $('#cont').empty();
                        let html='';
                        let count=1;
                        for(let i=0;i<data.length;i++){
                            html+='<tr><td>'+count+'</td>'+
                                    '<td>'+data[i].scbd_amnt+'</td>'+
                                    '<td>'+data[i].scbd_type+'</td>'+
                                    '<td>'+data[i].ordm_ornm+'</td>'+
                                    '<td>'+data[i].created_at+'</td></tr>';

                            count++;
                        }
                        $('#cont').append(html);
                    },
                    error:function (error){
                        console.log(error);
                    }
                });
            }
            else{
                swal.fire({
                    icon:'error',
                    text:'Please provide staff id and date',
                })
            }
            
        }
    </script>
@endsection