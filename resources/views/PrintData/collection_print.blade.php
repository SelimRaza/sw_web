<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Collection</title>
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

                    <tr>
                        <td width="100%">
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td width="100%">
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">

                                            <tr>
                                                <td width="200" style="text-align: center;">
                                                    <p style="font-size: 30px;padding: 0px;margin: 0px;"> {{$collectionData->ou_name}}</p>
                                                    <p style="font-size: 25px; padding: 0px;margin: 0px;">Collection
                                                        Voucher</p>
                                                </td>


                                            </tr>

                                            <tr>
                                                <th align="left">
                                                    Received from: {{$collectionData->outlet_id}}-
                                                    {{$collectionData->outlet_name}}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="left">
                                                    Payment Id: {{$collectionData->payment_id}}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="left">
                                                    Payment Code: {{$collectionData->collection_code}}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="left">
                                                    Payment Date: {{$collectionData->date}}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="left">
                                                    Payment Amount: {{$collectionData->payment_amount}}
                                                </th>
                                            </tr>

                                            <tr>
                                                <td width="100%">
                                                    <table width="720" border="1" cellpadding="0" cellspacing="0"
                                                           bordercolor="#d3d3d3">
                                                        <tr>

                                                            <th align="left">Collection type
                                                            </th>
                                                            <th>Amount
                                                            </th>
                                                            <th>Cheque Date
                                                            </th>
                                                            <th>Cheque No.
                                                            </th>
                                                            <th>Bank Name
                                                            </th>
                                                        </tr>


                                                        <tr>
                                                            <th align="left"> {{$collectionData->payment_type}}
                                                            </th>
                                                            <th> {{$collectionData->amount}}
                                                            </th>
                                                            <th> {{$collectionData->cheque_date}}
                                                            </th>
                                                            <th> {{$collectionData->cheque_no}}
                                                            </th>
                                                            <th> {{$collectionData->bank_name}}
                                                            </th>

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
                        <td width="100%">
                            <table width="720" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                <thead>
                                <tr bgcolor="#b0c4de">
                                    <th height="23" rowspan="2">
                                        <span class="style12">S/L</span>
                                    </th>
                                    <th width="65" height="23" rowspan="2">
                                        <span class="style12">Site</span>
                                    </th>
                                    <th width="65" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Date</span>
                                    </th>
                                    <th width="50" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Number</span>
                                    </th>
                                    <th width="50" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Tax Number</span>
                                    </th>
                                    <th width="50" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Trans. Type</span>
                                    </th>
                                    <th width="50" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Pending Amount</span>
                                    </th>
                                    <th width="60" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Paid Amount</span>
                                    </th>
                                    <th width="60" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Balance Amount</span>
                                    </th>
                                    <th width="60" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Deduct Amount</span>
                                    </th>
                                    <th width="60" height="23" rowspan="2">
                                        <span class="style12">&nbsp;Net Amount</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $site_id = ""?>
                                @foreach($collectionMatchingData as $index => $collection1)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <?php if($site_id != $collection1->site_id){?>
                                        <td>{{$collection1->site_code}}{{$collection1->site_name}}
                                        </td>
                                        <?php
                                        $site_id = $collection1->site_id;}else{?>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                        <?php
                                        }?>


                                        <td height="23">{{$collection1->date}}
                                        </td>
                                        <td height="23">{{$collection1->invoice_code}}
                                        </td>
                                        <td height="23">{{$collection1->tax_invoice}}
                                        </td>
                                        <td height="23" align="left">
                                            <span style="float: left;">{{$collection1->invoice_type}}</span>
                                            <span style="float: right;" class="invoiceArabic">&nbsp;
                                                        &nbsp;</span>
                                        </td>
                                        <td align="right" height="23">{{$collection1->invoice_amount}}
                                        </td>
                                        <td  align="right" height="23">&nbsp;{{$collection1->collection_amount}}
                                        </td>
                                        <td height="23" align="right">
                                            {{$collection1->balance}}
                                        </td>
                                        <td height="23" align="right">
                                            {{$collection1->deduct_amount}}
                                        </td>
                                        <td height="23" align="right">
                                            {{$collection1->net_amount}}
                                            &nbsp;
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6" align="right">Total</td>
                                    <td align="right">{{array_sum(array_column($collectionMatchingData, 'invoice_amount'))}}</td>
                                    <td align="right">{{array_sum(array_column($collectionMatchingData, 'collection_amount'))}}</td>
                                    <td align="right">{{array_sum(array_column($collectionMatchingData, 'balance'))}}</td>
                                    <td align="right">{{array_sum(array_column($collectionMatchingData, 'deduct_amount'))}}</td>
                                    <td align="right">{{array_sum(array_column($collectionMatchingData, 'net_amount'))}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td>
                            <table width="720" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">

                                @foreach($collectionTypeData as $index => $collectionTypeData1)
                                    <tr height="26">
                                        <td align="left">
                                                <span class="style8">
                                                        <span>{{$collectionTypeData1->invoice_type}}  </span>

                                                    </span>
                                        </td>
                                        <td align="left">
                                            <span class="style8">
                                                        <span>{{$collectionTypeData1->amount}}  </span>
                                                    </span>
                                        </td>
                                    </tr>

                                @endforeach
                                <tr height="26">
                                    <td align="left">
                                                <span class="style8">
                                                        <span>Total  </span>

                                                    </span>
                                    </td>
                                    <td align="left">
                                            <span class="style8">
                                                        <span>{{array_sum(array_column($collectionTypeData, 'amount'))}}  </span>
                                                    </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="720" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="360" align="left">
                                        <table width="360" height="60" border="2" cellpadding="0" cellspacing="0"
                                               style="margin: 0px 0px;">
                                            <tr>
                                                <td valign="top">
                                                    <span class="style8">Prepared By</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="360" align="right">
                                        <table width="355" height="60" border="2" cellpadding="0" cellspacing="0"
                                               style="margin: 0px 0px;">
                                            <tr>
                                                <td valign="top">
                                                    <span class="style8">Approved By</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                                        <span class="style5" style="font-size: 20px; font-weight: bold;">Thank you for your
                                            business</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
</body>
<script type="text/javascript">
    window.print();
    setTimeout('window.close()', 100);
</script>
</html>
