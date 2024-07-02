<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>

    </title>
    <style type="text/css">
        body {
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
        }

        body, td, th {
            font-family: Times New Roman, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #20292c;
        }

        .style4 {
            font-size: 16px;
            font-weight: bold;
        }

        .style5 {
            color: #0170c1;
            font-size: 21px;
        }

        .style8 {
            font-size: 14px;
            font-weight: bold;
        }

        .style12 {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }

        .invoiceArabic {
            display: block !important;
        }

        .wd100 {
            width: 100%;
        }

        .width100fr {
            width: 100% !important;
            float: right !important;
        }

        #tbldisc tr td {
            border: 1px solid #d3d3d3;

        }

        #Disval tr td {
            width: 50%;
            border-right: 1px solid #d3d3d3;
            border: 1px solid #d3d3d3;
            border-collapse: collapse !important;
            visibility: visible;

        }

        .HV {
            visibility: visible;
            margin-bottom: -8px;
            margin-top: 8px;
        }

        .VH {
            visibility: hidden;
            display: none;
        }

    </style>
</head>
<body>

<div>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table border="0" cellspacing="0" cellpadding="0" width="720px" style="padding: 0px 10px;">
                    <tr style="text-align: center">
                        <span class="style4" style="text-transform: uppercase"> Tele Sales </span>
<br>
<br>
                    </tr>
                    <tr>
                        <td width="100%">
                            <table cellspacing="0" cellpadding="0" width="100%">
                                <tr >
                                    <td width="100%">
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                            <tr>
                                                <td>
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr style="text-align: center">
                                                            <td>
                                                                <span class="style4" >{{ $order_items[0]->acmp_name }}</span>
                                                                </td>
                                                        </tr>

                                                        <br>

                                                        <tr>
                                                            <td width="100%">
                                                                <table width="100%" height="24" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3" style="border-top: none;">
                                                                    <tbody style="border-top-color: black !important;">
                                                                    <tr>
                                                                        <td width="200">
                                                                            <span class="style8" style="text-transform: uppercase">Site Code:</span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="style8">{{ $order_items[0]->site_code }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="style8"  style="text-transform: uppercase">Site Name: </span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="style8">{{ $order_items[0]->site_name }}</span>
                                                                        </td>
                                                                    </tr>

                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>


                                                        <tr>
                                                            <td width="100%">

                                                                <table width="100%" height="24" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3" style="border-top: none;">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td width="200">
                                                                            <span class="style8" style="text-transform: uppercase">Order By ID:</span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="style8">{{ $order_items[0]->aemp_usnm }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="style8"  style="text-transform: uppercase">Order By Name: </span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="style8">{{ $order_items[0]->aemp_name }}</span>
                                                                        </td>
                                                                    </tr>

                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>

                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">

                                <tr>
                                    <td height="30" style="width: 33.33%; text-align: left">
                                        <span class="style8">Order Date:</span>
                                        {{$order_items[0]->ordm_date}}
                                    </td>

                                    <td height="30" style="width: 33.33%;" align="center">
                                        <span class="style8">Site Mobile:&nbsp;</span>
                                        +{{$order_items[0]->mobile}}
                                    </td>

                                    <td height="30" style="width: 33.33%;" align="right">
                                        <span class="style8"> Delivery Date:&nbsp;</span>
                                        {{$order_items[0]->ordm_drdt}}
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                <tr bgcolor="#b0c4de">
                                    <td width="150" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">ORDER NUMBER</span><br/>
                                    </td>
                                    <td width="90" style="text-align: center">

                                        <span style="font-size:12px;" class="style12">DEPO CODE</span><br/>
                                    </td>
                                    <td width="90" style="text-align: center">

                                        <span style="font-size:12px;" class="style12">DEPO NAME</span><br/>
                                    </td>
                                    <td width="90" style="text-align: center">

                                        <span style="font-size:12px;" class="style12">SLGP CODE</span><br/>
                                    </td>
                                    <td width="90" style="text-align: center">

                                        <span style="font-size:12px;" class="style12">SLGP NAME</span><br/>
                                    </td>
                                    <td width="90" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">I. COUNT</span><br/>
                                    </td>
                                    <td width="90" align="right">
                                        <span style="font-size:12px;" class="style12">AMOUNT </span><br/>
                                    </td>


                                </tr>
                                <tr>
                                    <td height="24" style="text-align: center">&nbsp;{{$order_items[0]->ordm_ornm}}
                                    </td>
                                    <td height="24" style="text-align: center">{{$order_items[0]->dlrm_code}}
                                    </td>
                                    <td height="24" style="text-align: center">&nbsp;{{$order_items[0]->dlrm_name}}
                                    </td>
                                    <td height="24" style="text-align: center">&nbsp;{{$order_items[0]->slgp_code}}
                                    </td>
                                    <td height="24" style="text-align: center">&nbsp;{{$order_items[0]->slgp_name}}
                                    </td>
                                    <td height="24" style="text-align: center">&nbsp;{{$order_items[0]->ordm_icnt}}
                                    </td>
                                    <td height="24" align="right">&nbsp;{{$order_items[0]->ordm_amnt}}
                                    </td>

                                </tr>
                            </table>

                            <br>

                            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                <tr bgcolor="#b0c4de">
                                    <td width="65" height="23" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">&nbsp;SL</span><br/>
                                    </td>
                                    <td width="165" height="23" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">&nbsp;ITEM NAME</span><br/>
                                    </td>
                                    <td width="85" height="23" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">&nbsp;ITEM CODE</span><br/>
                                    </td>
                                    <td width="60" height="23" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">&nbsp;CTN SIZE</span><br/>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="style12" style="font-size:12px;" style="margin-left:44px;">&nbsp;Quantity</span>
                                    </td>
                                    <td width="60" height="23" style="text-align: center">
                                        <span style="font-size:12px;" class="style12">&nbsp;PRICE</span><br/>
                                    </td>
                                    <td  style="text-align: center">
                                        <span class="style12" style="font-size:12px;" style="margin-left:44px;">&nbsp;Discount</span><br/>
                                    </td>
                                    <td  style="text-align: end">
                                        <span class="style12" style="font-size:12px;" style="margin-left:44px;">&nbsp;TOTAL</span><br/>
                                    </td>
                                </tr>

                                @foreach ($order_items as $index=>$salesOrderLine1)

                                    <tr>
                                        <td height="10" style="text-align: center">&nbsp;{{$index+1}}
                                        </td>
                                        <td height="10" style="text-align: center">
                                            <span style="float: left;font-size: 10px;">&nbsp;{{$salesOrderLine1->amim_name}}</span>
                                        </td>
                                        <td height="10" style="text-align: center">
                                            {{$salesOrderLine1->amim_code}}
                                        </td>
                                        <td height="10" style="text-align: center">
                                            {{$salesOrderLine1->ordd_duft}}
                                        </td>
                                        <td height="10" style="text-align: center">&nbsp;{{$salesOrderLine1->ordd_inty}}
                                        </td>

                                        <td height="10" style="text-align: center">
                                            &nbsp;{{$salesOrderLine1->ordd_uprc}}
                                        </td>

                                        <td height="23" style="text-align: center">
                                            {{$salesOrderLine1->ordd_spdo}}
                                        </td>
                                        <td height="10" align="right">
                                            {{$salesOrderLine1->ordd_oamt}}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>


                </table>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
