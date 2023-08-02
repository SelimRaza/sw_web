@extends('theme.app')

@section('content')
    <div class="right_col" role="main">
        <div class="">
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
                                <h3 style="text-align:center;">Order Details</h3>
                                <div class="col-md-1 col-sm-1 col-xs-12">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" id="block_order_report">
                            @if($data)
                                <div class="col-md-5 col-sm-5 col-xs-12">
                                    <table class="table">
                                        <tr><td><b>Order No: </td></b><td>{{$data[0]->ordm_ornm}}</td></tr>
                                        <tr><td><b>Order Date: </td></b><td>{{$data[0]->ordm_date}}</td></tr>
                                        <tr><td><b>Exp Delivery: </td></b><td>{{$data[0]->ordm_drdt}}</td></tr>
                                        <tr><td><b>Order Amount: </td></b><td>{{$data[0]->ordm_amnt}}</td></tr>
                                        <tr><td><b>Excise Duty: </td></b><td>{{$data[0]->t_excise}}</td></tr>
                                        <tr><td><b>Total Discount: </td></b><td>{{$data[0]->t_disc}}</td></tr>
                                        <tr><td><b>Currency: </td></b><td>{{$data[0]->cont_cncy}}</td></tr>
                                        <tr><td><b>Status: </td></b><td>{{$data[0]->lfcl_id.'-'.$data[0]->lfcl_name}}</td></tr>
                                        <tr><td><b>Group: </td></b><td>{{$data[0]->slgp_name}}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-2 col-sm-1"></div>
                                <div class="col-md-5 col-sm-5 col-xs-12">
                                    <table class="table">
                                        <tr><td><b>Customer Code: </td></b><td>{{$data[0]->oult_code}}</td></tr>
                                        <tr><td><b>Customer Name: </td></b><td>{{$data[0]->oult_name}}</td></tr>
                                        <tr><td><b>Site Code: </td></b><td>{{$data[0]->site_code}}</td></tr>
                                        <tr><td><b>Site Name: </td></b><td>{{$data[0]->site_name}}</td></tr>
                                        <tr><td><b>SR Name: </td></b><td>{{$data[0]->aemp_name}}</td></tr>
                                        <tr><td><b>SR ID: </td></b><td>{{$data[0]->aemp_usnm}}</td></tr>
                                        <tr><td><b>Created By: </td></b><td>{{$data[0]->created_by}}</td></tr>
                                        <tr><td><b>Region Name: </td></b><td>{{$data[0]->dirg_name}}</td></tr>
                                        <tr><td><b>Cancel Reason: </td></b><td>{{$data[0]->ocrs_name}}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h4><b>Order Details</b></h4>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>

                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>CTN</th>
                                            <th>PCS</th>
                                            <th>CTN SIZE</th>
                                            <th>RATE</th>
                                            <th>Default Disc</th>
                                            <th>Prom Disc</th>
                                            <th>SP Disc</th>
                                            <th>Total Disc</th>
                                            <th>Total Vat</th>
                                            <th>Gross Amnt</th>
                                            <th>Exc Excise Duty</th>
                                            <th>Excise Duty</th>
                                            <th>Inc Excise Duty</th>
                                            
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        @foreach($ord_details as $itm)
                                            @php

                                            $qty=$itm->ordd_inty;
                                            $ctn=(int)($qty/$itm->amim_duft);
                                            $pcs=$qty-($ctn*$itm->amim_duft);
                                            $t_disc=$itm->ordd_spdi+$itm->ordd_opds+$itm->ordd_dfdo;

                                            @endphp
                                            <tr>
                                                <td>{{$itm->amim_code}}</td>
                                                <td>{{$itm->amim_name}}</td>
                                                <td>{{$ctn}}</td>
                                                <td>{{$pcs}}</td>
                                                <td>{{$itm->amim_duft}}</td>
                                                <td>{{$itm->ctn_price}}</td>
                                                <td>{{$itm->ordd_dfdo}}</td>
                                                <td>{{$itm->ordd_opds}}</td>
                                                <td>{{$itm->ordd_spdi}}</td>
                                                <td>{{$t_disc}}</td>
                                                <td>{{$itm->ordd_tvat}}</td>
                                                <td>{{$itm->ordd_oamt}}</td>
                                                <td>{{$itm->ordd_oamt-$t_disc+$itm->ordd_tvat}}</td>
                                                <td>{{$itm->ordd_texc}}</td>
                                                <td>{{$itm->ordd_oamt-$t_disc+$itm->ordd_texc+$itm->ordd_tvat}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h4><b>Offer Details</b></h4>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>CTN</th>
                                            <th>PCS</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <h4><b>Free Items</b></h4>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>CTN</th>
                                            <th>PCS</th>
                                        </tr>
                                        </thead>
                                        <tbody id="cont">
                                        @foreach($free_item as $fitm)
                                            @php
                                                $qty=$fitm->ordd_inty;
                                                $ctn=(int)($qty/$fitm->ordd_duft);
                                                $pcs=$qty-($ctn*$fitm->ordd_duft);
                                            @endphp
                                            <tr>
                                                <td>{{$fitm->amim_code}}</td>
                                                <td>{{$fitm->amim_name}}</td>
                                                <td>{{$ctn}}</td>
                                                <td>{{$pcs}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                            @endif
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $SIDEBAR_MENU = $('#sidebar-menu')
        $(document).ready(function () {
            setTimeout(function () {
                $('#menu_toggle').click();
            },1);
        });

    </script>
@endsection