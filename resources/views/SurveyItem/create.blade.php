@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">
                            <strong>  </strong>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                
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
                        <div class="x_content">
                        <div class="col-md-12">
                            <div class="col-md-6 col-sm-6">
                            <form action="{{ URL::to('survey_items/add')}}" method="POST">
                                {{csrf_field()}}
                                <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                                <div id="sales_heirarchy" class="form-group">
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="slgp_id">Class Name
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <select class="form-control cmn_select2" name="class_id" id="class_id">
                                                <option value="">Select class</option>
                                                @foreach($class_list as $class)
                                                    <option value="{{ $class->class_id }}">{{ ucfirst($class->class_name)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="itsg_id">Item Name
                                                
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control in_tg" name="item_code" id="item_code">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sv_sdat">Start Date
                                                
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control in_tg date_class" name="sv_sdat" id="sv_sdat" value="<?php echo date('Y-m-d'); ?>" >
                                        </div>
                                    </div>
                                    <div class="form-row col-md-6 col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sv_edat">End Date
                                                
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" class="form-control in_tg date_class" name="sv_edat" id="sv_edat" value="<?php echo date('Y-m-d',strtotime('+30 days')); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-6  col-sm-6 col-xs-12">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="slgp_id">
                                        </label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <button type="submit" class="btn btn-primary" onclick="storeSurveyItem()" style="float:right;">Add Item</button>
                                        </div>
                                    </div>
                                    <div  class="col-md-1 col-sm-1 col-xs-12" style="float:right;">
                                       
                                </div>
                            </form>
                            </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
<!--             
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <form action="{{ URL::to('survey_items/add')}}" method="POST">
                                {{csrf_field()}}
                                <span style="float: right;"> <button class="btn btn-success" type="submit">Save <i class="fa fa-check-circle"></i> </button> </span>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Item Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Action</th>
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
            </div> -->
    </div>
</div>
</div>
</div>
</div>
    <script type="text/javascript">
        $(".date_class").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true
        });
        $("#acmp_id").select2({width: 'resolve'});
        $(".cmn_select2").select2({width: 'resolve'});
        function closeWindow(){
            window.close();
        }
        function addItem(){
            let item_name=$('#item_code').val();
            let class_id=$('#class_id').val();
            let class_name=$('#class_id option:selected').text();
            let slgp_id=$('#slgp_id').val();
            let html='';
            if(amim_code !='' && slgp_id !=''){
                     html+='<tr><td><input type="hidden" name="amim_code[]" id="amim_code" value="'+amim_code+'">'+amim_code+'</td>'+
                       '<td><input type="hidden" name="sgp[]" id="sgp" value="'+slgp_id[i]+'">'+slgp_id[i]+'</td>'+
                       '<td><input type="submit" class="btn btn-danger btn-xs" value="Remove" onclick="removeItem(this)"></td></tr>';
                
                $('#cont').append(html);
            }else{
                Swal.fire({
                    icon:'error',
                    text:'Please fill all fields',
                });
            }
        }
        function removeItem(v){
            $(v).parent().parent().remove();
        }
        
    </script>
@endsection