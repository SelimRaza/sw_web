@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <ol class="breadcrumb">
                        <li class="label-success">
                            <a href="{{ URL::to('/')}}"><i class="fa fa-home"></i>Home</a>
                            
                        </li>
                        <li>
                            <a href="{{ URL::to('/site')}}"></i>Site</a>/
                            <strong>Unverified Outlet List</strong>
                        </li>
                    </ol>
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
                            <h2 style="color:red;">This Page is Under Maintenance</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <input type="hidden" name="_token" id="_token" value="<?php echo csrf_token(); ?>">
                            <div  class="col-md-12 col-sm-12">
                                <div class="form-group col-md-4">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                        for="acmp_id">ERP Zone<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <select class="form-control cmn_select2" name="erp_zone" id="erp_zone">
                                            <option value="">Select</option>
                                            @foreach($erpzn as $zone)
                                            <option value="{{$zone->epzn_code}}">{{$zone->epzn_code}}
                                                - {{$zone->epzn_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div  class="col-md-12 col-sm-12">
                                <div class="form-group col-md-4">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                        for="acmp_id">Promotions<span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <label><input type="radio" name="prom" value="0" checked > None</label>
                                        <label><input type="radio" name="prom" value="1" > Aplly all promotion</label>
                                    </div>
                                </div>
                            </div>
                            <div  class="col-md-12 col-sm-12">
                                <div class="form-group col-md-4">
                                    <label class="control-label col-md-4 col-sm-4 col-xs-12"
                                        for="acmp_id"><span class="required"></span>
                                    </label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <button class="btn btn-danger" id="inactiveSite">Inactive</button>
                                        <button class="btn btn-primary" id="verifySite">Verify</button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-12 col-sm-12" style="margin-top:30px;">
                                    <button class="btn btn-danger" id="inactiveSite">Inactive</button>
                                    <button class="btn btn-primary" id="verifySite">Verify</button>
                            </div> -->
                            <table id="datatable" class="table table-bordered projects" data-page-length='25'>
                                <thead>
                                <tr class="tbl_header">
                                    <th><input type="checkbox" id="group_all">All</th>
                                    <th>SL</th>
                                    <th>Outlet Code</th>
                                    <th>Outlet Name1</th>
                                    <th>Outlet Address1</th>
                                    <th>Outlet Mobile1</th>
                                    <th>Outlet Name2</th>
                                    <th>Outlet Address2</th>
                                    <th>Outlet Owner</th>
                                    <th>Image</th>
                                    <th>Sub chanel</th>
                                    <th>Category</th>
                                    <th>Market</th>
                                </tr>
                                </thead>
                                <tbody id="tbl_cont">
                                @foreach($sites as $index => $site)
                                    <tr>
                                        <td><input type="checkbox" class="sub_chk" name="group" value="{{$site->id}}"></td>
                                        <td>{{$index+1}}</td>
                                        <td><input type="text" class="olt_code form-control" value="{{$site->site_code}}"> <p class="hidden">{{$site->site_code}}</p></td>
                                        <td>{{$site->site_name}}</td>
                                        <td>{{$site->site_adrs}}</td>
                                        <td>{{$site->site_mob1}}</td>
                                        <td>{{$site->site_olnm}}</td>
                                        <td>{{$site->site_olad}}</td>
                                        <td>{{$site->site_olon}}</td>
                                        <td><img src="{{'https://images.sihirbox.com/'.$site->site_imge}}" height="90" width="150"  alt="N/A"></td>
                                        <td>{{$site->scnl_name}}</td>
                                        <td>{{$site->otcg_name}}</td>
                                        <td>{{$site->mktm_name}}</td>
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
         $(document).ready(function () {

            $('#group_all').on('click', function(e) {

                if($(this).is(':checked',true)){

                    $(".sub_chk").prop('checked', true);

                } else {

                    $(".sub_chk").prop('checked',false);

                }

            });
            $('#erp_zone').select2();

        $("#inactiveSite").click(function(){
            var sites = [];
            var _token =$('#_token').val();
            var flag=1;
            var erp_zone=$('#erp_zone').val();
            $.each($("input[name='group']:checked"), function(){
                sites.push($(this).val());
            });
            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/site/unverified_inactive",
                data: {
                   sites:sites,
                   erp_zone:erp_zone,
                   _token:_token,
                   flag:flag,
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                $('#tbl_cont').empty();
                var html = '';
                var count = 1;
                for (var i = 0; i < data.length; i++) {

                    html += '<tr>' +
                        '<td><input type="checkbox" class="sub_chk" name="group" value="'+data[i]['id']+'"></td>'+
                        '<td>' + count + '</td>' +
                        '<td>' + data[i]['site_code'] + '</td>' +
                        '<td>' + data[i]['site_name'] + '</td>' +
                        '<td>' + data[i]['site_adrs'] + '</td>' +
                        '<td>' + data[i]['site_mob1'] + '</td>' +
                        '<td>' + data[i]['site_mob2'] + '</td>' +
                        '<td>' + data[i]['site_olnm'] + '</td>' +
                        '<td>' + data[i]['site_olad'] + '</td>' +
                        '<td>' + data[i]['site_olon'] + '</td>' +
                        '<td>' + data[i]['scnl_name'] + '</td>' +
                        '<td>' + data[i]['otcg_name'] + '</td>' +
                        '<td>' + data[i]['mktm_name'] + '</td>' +
                        '</tr>';
                    count++;
                }
                Swal.fire({
                            text: 'Outlet Inactivated Successfully!',
                        })
                $('#tbl_cont').empty();
                $('#tbl_cont').append(html);

                },
                error: function(data) {

                    console.log(data);

                }
            });

        });

        $("#verifySite").click(function(){
            var sites = [];
            let site_code=[];
            let prom=$('input[name="prom"]:checked').val();
            var erp_zone=$('#erp_zone').val();
            var _token =$('#_token').val();
            var flag=2;
            $.each($("input[name='group']:checked"), function(){
                sites.push($(this).val());
                let code=$(this).parent().parent().find('.olt_code').val();
                site_code.push(code);
            });
            
            $.ajax({
                type: "POST",
                url: "{{URL::to('/')}}/site/unverified_inactive",
                data: {
                   sites:sites,
                   site_code:site_code,
                   prom:prom,
                   erp_zone:erp_zone,
                   _token:_token,
                   flag:flag,
                },
                cache: false,
                dataType: "json",
                success: function(data) {
                    console.log(data);
                $('#tbl_cont').empty();
                var html = '';
                var count = 1;
                for (var i = 0; i < data.length; i++) {

                    html += '<tr>' +
                        '<td><input type="checkbox" class="sub_chk" name="group" value="'+data[i]['id']+'"></td>'+
                        '<td>' + count + '</td>' +
                        '<td><input type="text" class="olt_code form-control" value="'+ data[i]['site_code']+'"></td>' +
                        '<td>' + data[i]['site_name'] + '</td>' +
                        '<td>' + data[i]['site_adrs'] + '</td>' +
                        '<td>' + data[i]['site_mob1'] + '</td>' +
                        '<td>' + data[i]['site_mob2'] + '</td>' +
                        '<td>' + data[i]['site_olnm'] + '</td>' +
                        '<td>' + data[i]['site_olad'] + '</td>' +
                        '<td>' + data[i]['site_olon'] + '</td>' +
                        '<td>' + data[i]['scnl_name'] + '</td>' +
                        '<td>' + data[i]['otcg_name'] + '</td>' +
                        '<td>' + data[i]['mktm_name'] + '</td>' +
                        '</tr>';
                    count++;
                }
                if(flag==1){
                    Swal.fire({
                        icon: 'success',
                        text: 'Outlet Inactivated Successfully!',
                    });
                }else{
                    Swal.fire({
                        icon: 'success',
                        text: 'Outlet Verified Successfully!',
                    });
                }
                
                $('#tbl_cont').empty();
                $('#tbl_cont').append(html);

                },
                error: function(error) {
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        text: 'Error!Duplicate Code',
                    });
                    console.log(error);

                }
            });

        });

});
</script>
@endsection