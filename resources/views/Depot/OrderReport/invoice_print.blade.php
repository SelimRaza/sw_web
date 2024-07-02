<html xmlns="http://www.w3.org/1999/xhtml">
<?php
function convert_number_to_words($number)
{

    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'fourty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int)($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string)$fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

?>
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.print();
            //  setTimeout('window.close()', 100);
        });
    </script>
</head>
<body>


<form method="post" action="" id="form1">
    <div class="aspNetHidden">
        <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value=""/>
    </div>

    <div class="aspNetHidden">

        <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="2A3050CA"/>
        <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION"
               value="/wEdAAPUjqqxcJVDaXyeAbyo2RaiiqXHcGXiwb/pmt5xSUqBvgk/AmOdX45P1P8bspfoqAsnlLMwQaI7N5hF1M7mI7Y6ee32yzW2kcarre9jLNF19A=="/>
    </div>
    <div>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="720px" style="padding: 0px 10px;">
                        <tr style="text-align: center">
                            <span class="style4">{{$salesOrder->invoice_title}}</span>

                        </tr>
                        <tr>
                            <td width="100%">
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr id="rpSalesInvoice_dvUAE_0">
                                        <td width="100%">
                                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td>
                                                                    <span class="style4"> SUPPLIERS DETAILS:</span>
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="style4"><?php echo $salesOrder->ou_name; ?></span>
                                                                    <span class="style4"><?php echo $salesOrder->note; ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>P.O. Box : {{$salesOrder->post_box_no}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Warehouse : {{$salesOrder->address}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Tel:address  {{$salesOrder->phone}}
                                                                    ,Fax:  {{$salesOrder->fax}}
                                                                    ,Email:  {{$salesOrder->email}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="style8"> <?php
                                                                        if ($salesOrder->vat_status == 1) {
                                                                            echo "Exice TRN:" . $salesOrder->tax_number;
                                                                        }
                                                                        ?></span>
                                                                </td>

                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="style8"> <?php
                                                                        if ($salesOrder->vat_status == 1) {
                                                                            echo "VAT TRN:" . $salesOrder->vat_number;
                                                                        }

                                                                        ?></span>
                                                                </td>

                                                            </tr>

                                                        </table>
                                                    </td>
                                                    <td align="center">
                                                        <img src="{{ asset("theme/image/logo.png")}}" alt="" width="156" height="100"/>
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
                                        <td height="30" style="width: 33.33%;" align="left">
                                            <span class="style8">Order Date(تاريخ الطلب):</span>
                                            {{$salesOrder->order_date}}
                                        </td>
                                        <!--</tr>
                                        <tr>-->
                                        <td height="30" style="width: 33.33%;" align="center">
                                            <span class="style8">Invoice Date (تاريخ الفاتورة):&nbsp;</span>
                                            {{$salesOrder->delivery_date}}
                                        </td>
                                        <!--</tr>
                                        <tr>-->
                                        <td height="30" style="width: 33.33%;" align="right">
                                            <span class="style8"> Delivery Date (تاريخ التوصيل):&nbsp;</span>
                                            {{$salesOrder->delivery_date}}
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">

                                    <tr>
                                        <td width="30%" height="20" align="left">
                                            <span class="style8">Customer Details</span>
                                        </td>
                                        <td width="40%" height="20" class="style4" align="center">


                                        </td>
                                        <td width="">
                                            <span class="style8"></span>
                                        </td>
                                        <td>
                                            <span class="style8"><?php

                                                if ($salesOrder->vat_status == 1) {
                                                    echo "Customer TRN:" . $salesOrder->VAT_TRN;
                                                } ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr style="border: none;">
                            <td width="100%">
                                <table width="100%" height="60" border="1" cellpadding="0" cellspacing="0"
                                       bordercolor="#d3d3d3">
                                    <tr>
                                        <td width="100" valign="top">
                                            <span class="style4">&nbsp;SHIP TO</span><br/>

                                            <span class="style4 invoiceArabic">&nbsp;توريد لــــ</span>
                                        </td>
                                        <td width="350">
                                            {{$salesOrder->Site_Name}}
                                            <br/>{{$salesOrder->site_address}}

                                        </td>
                                        <td width="100" valign="top">
                                            <span class="style4">&nbsp;BILL TO</span><br/>

                                            <span class="style4 invoiceArabic">&nbsp;فاتورة لــــ</span>
                                        </td>
                                        <td width="350">
                                            {{$salesOrder->Outlet_Name}}
                                            <br/>{{$salesOrder->outlet_address}}
                                        </td>

                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%">
                                <table width="100%" height="24" border="1" cellpadding="0" cellspacing="0"
                                       bordercolor="#d3d3d3"
                                       style="border-top: none;">
                                    <tr>
                                        <td width="200">
                                            <span class="style8">Tax Invoice No (رقم الفاتورة):</span>
                                        </td>
                                        <td>
                                            <span class="style8"> <?php
                                                if ($salesOrder->vat_status == 1) {
                                                    echo "" . $salesOrder->vat_sl_number;
                                                } ?></span>
                                        </td>
                                        <td>
                                            <span class="style8">Supplier Ref. No:{{$salesOrder->Order_ID}} </span>
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
                                <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr bgcolor="#b0c4de">
                                        <td width="110" height="24">
                                            <strong>&nbsp;PRESELLER NAME</strong><br/>

                                            <strong class="invoiceArabic">&nbsp;مندوب المبيعات</strong>
                                        </td>
                                        <td width="74" height="24">
                                            <strong>&nbsp;DELIVERY EMIRATES
                                            </strong><br/>
                                            <strong class="invoiceArabic">عقوملا</strong>
                                        </td>
                                        <td width="91" height="24">
                                            <strong>&nbsp;SITE CODE</strong><br/>
                                            <strong class="invoiceArabic">&nbsp;رقم الفرع</strong>
                                        </td>
                                        <td width="100" height="24">
                                            <strong>&nbsp;CUST CODE</strong><br/>
                                            <strong class="invoiceArabic">&nbsp;رقم العميل</strong>
                                        </td>
                                        <td width="104" height="24">
                                            <strong>&nbsp;CURRENCY</strong><br/>
                                            <strong class="invoiceArabic">&nbsp;العملة</strong>
                                        </td>
                                        <td width="138" height="24">
                                            <strong>&nbsp;PAYMENT TERMS</strong><br/>
                                            <strong class="invoiceArabic">&nbsp;شروط السداد</strong>
                                        </td>


                                    </tr>
                                    <tr>
                                        <td height="24">&nbsp;{{$salesOrder->preseller_name}}
                                        </td>
                                        <td height="24">&nbsp;{{$salesOrder->Region_Name}}
                                        </td>
                                        <td height="24">{{$salesOrder->customer_number}}
                                        </td>
                                        <td height="24">&nbsp;{{$salesOrder->outlet_id}}
                                        </td>
                                        <td height="24">{{$salesOrder->currency}}
                                        </td>
                                        <td height="24">&nbsp;{{$salesOrder->Payment_Type}}
                                        </td>

                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%">
                                <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr bgcolor="#b0c4de">
                                        <td width="35" height="23" rowspan="2">
                                            <span class="style12">&nbsp;SL#</span><br/>

                                            <span class="style12 invoiceArabic">&nbsp;الرقم التسلسلي</span>
                                        </td>
                                        <td width="65" height="23" rowspan="2">
                                            <span class="style12">&nbsp;ITEM CODE</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;رمز الصنف</span>
                                        </td>
                                        <td width="285" height="23" rowspan="2">
                                            <span class="style12">&nbsp;DESCRIPTION</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;التفاصيل / وصف الصنف</span>
                                        </td>
                                        <td width="285" height="23" rowspan="2">
                                            <span class="style12">Type</span><br/>
                                        </td>
                                        <td width="50" height="23" rowspan="2">
                                            <span class="style12">&nbsp;CTN</span><br/>

                                            <span class="style12 invoiceArabic">&nbsp;الكمية</span>
                                        </td>
                                        <td width="50" height="23" rowspan="2">
                                            <span class="style12">&nbsp;UOM</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;وحدة القياس</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">&nbsp;CTN PRICE</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;سعر الوحدة</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">&nbsp;GROSS Amnt</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;سعر الوحدة</span>
                                        </td>
                                        <td colspan="2">
                                            <span class="style12" style="margin-left:44px;">&nbsp;Discount</span><br/>
                                            <span class="style12 invoiceArabic" style="text-align:center">&nbsp;نسبة الخصم</span>
                                        </td>

                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">&nbsp;Total Excl Excise Duty</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">&nbsp;Total Excise Duty</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">&nbsp;Total Incl Excise Duty</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">VAT %</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">VAT Amt</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2">
                                            <span class="style12">Total Incl Excise +VAT</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#b0c4de">
                                        <td width="75" height="23"><span class="style12">Dis%</span></td>
                                        <td width="75" height="23"><span class="style12">DisVal</span></td>
                                    </tr>
                                    <?php
                                    $exciseDuty = 0;
                                    $totalVatAmount = 0;
                                    $totalInclVat = 0;

                                    $count = 1;

                                    foreach ($salesOrderLine as $index=>$salesOrderLine1){
                                    //dd($salesOrderLine1);
                                  /*  $excise = ($salesOrderLine1->Total_Item_Price *$salesOrderLine1->gst) / 100;

                                    $inclExcise = ($salesOrderLine1->Total_Item_Price - $salesOrderLine1->Discount) + $excise;
                                    $vat = ($inclExcise * $salesOrderLine1->vat) / 100;*/

                                    ?>

                                    <tr>
                                        <td height="23">&nbsp;<?php echo $count; ?>
                                        </td>
                                        <td height="23">&nbsp;<?php echo $salesOrderLine1->Product_id; ?>
                                        </td>
                                        <td height="23" align="left">
                                            <span style="float: left;font-size: 10px;">&nbsp;<?php echo $salesOrderLine1->Product_Name; ?></span>
                                            <span style="float: right;"
                                                  class="invoiceArabic"><?php echo $salesOrderLine1->sku_print_name; ?>
                                                &nbsp;</span>
                                        </td>
                                        <td height="23">

                                        </td>
                                        <td height="23">
                                            &nbsp;<?php echo $salesOrderLine1->confirm_qty; ?>
                                        </td>
                                        <td height="23">&nbsp;
                                            <?php echo $salesOrderLine1->ctn_size; ?>
                                        </td>
                                        <td height="23" align="right">


                                            &nbsp;<?php echo $salesOrderLine1->Rate; ?>
                                        </td>
                                        <td height="23" align="right">

                                            &nbsp;<?php echo $salesOrderLine1->Total_Item_Price; ?>
                                        </td>
                                        <td height="23" align="right">
                                            <span>&nbsp;&nbsp;<?php echo $salesOrderLine1->ratio; ?></span>
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo $salesOrderLine1->Discount; ?>
                                            &nbsp;
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo $salesOrderLine1->Total_Item_Price; ?>
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo 0; ?>
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo 0; ?>
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo $salesOrderLine1->vat; ?>
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo 0; ?>
                                        </td>
                                        <td height="23" align="right">
                                            <?php echo $salesOrderLine1->net_amount; ?>
                                        </td>
                                    </tr>

                                    <?php

                               /*     $exciseDuty = $exciseDuty + ($salesOrderLine1->Total_Item_Price) * $salesOrderLine1->gst / 100;
                                    $totalVatAmount = $totalVatAmount + $vat;
                                    $totalInclVat = $totalInclVat + $salesOrderLine1->pnet_amount;*/
                                    $count++;
                                    }

                                    ?>


                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>
                                        <td width="40%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnTotalOrderAmount_0">
                                                        <input type="hidden" name="rpSalesInvoice$ctl00$hdnPageCount"
                                                               id="rpSalesInvoice_hdnPageCount_0" value="1"/>
                                                        <span class="style8">(المبلغ الاجمالي)Gross Amount: &nbsp;<?php echo $salesOrder->total_price; ?></span>
                                                    </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>
                                        <td width="55%" height="26">
                                            <span class="style8">&nbsp;</span>
                                        </td>
                                        <td width="45%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnTotalDiscount_0">
                                                        <span class="style8">(إجمالي الخصم)Discount

                                                            :
                                                            <?php echo $salesOrder->discount; ?>
                                                        </span></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>


                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">(المجموع بدون الضريبة المضافة
)Total Excluding Excise Duty:</span>
                                                        <span class="style8">
                                                            <?php echo $salesOrder->total_price- $salesOrder->discount; ?>
                                                        </span>
                                                    </span>
                                        </td>

                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">(مجموع الضريبة
)Total Excise Duty:

                                                            <?php echo $exciseDuty; ?></span>
                                                    </span>
                                        </td>


                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>


                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">(المجموع الإجمالي)Total Including Excise Duty
                                                            :&nbsp;
                                                            <?php echo $salesOrder->total_price -$salesOrder->discount  + $exciseDuty; ?></span>
                                                    </span>
                                        </td>


                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">(اجمالي الضريبة المضافة)Total VAT : <?php echo number_format($totalVatAmount, $salesOrder->round); ?></span>
                                                    </span>
                                        </td>


                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">(الاجمالي  بالضريبة المضافة)Total Including VAT : <?php echo number_format($totalInclVat, $salesOrder->round); ?></span>
                                                    </span>
                                        </td>


                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">Round Amount : <?php
                                                            if ($salesOrder->invoice_amount != '') {
                                                                echo number_format($salesOrder->invoice_amount - $totalInclVat, $salesOrder->round);
                                                            } else {
                                                                echo (double)(($totalInclVat * $salesOrder->round_digit) / $salesOrder->round_digit) - $totalInclVat;
                                                            }
                                                            ?></span>
                                                    </span>
                                        </td>


                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr>
                                        <td width="58%" height="26">
                                                    <span id="rpSalesInvoice_spnCurrency_0">
                                                        <span class="style8"> <?php
                                                            if ($salesOrder->invoice_amount != '') {
                                                                echo ucwords(convert_number_to_words($salesOrder->invoice_amount)) . " " .$salesOrder->currency . ' Only';
                                                            } else {
                                                                echo ucwords(convert_number_to_words((double)(($totalInclVat * $salesOrder->round_digit) / $salesOrder->round_digit))) . " " . $salesOrder->currency . ' Only';
                                                            }
                                                            ?>
                                                            &nbsp;</span></span>
                                        </td>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">Payable Amount: <?php
                                                            if ($salesOrder->invoice_amount != '') {
                                                                echo number_format($salesOrder->invoice_amount, $salesOrder->round);
                                                            } else {
                                                                echo (double)(($totalInclVat * $salesOrder->round_digit) / $salesOrder->round_digit);
                                                            }
                                                            ?></span>
                                                    </span>
                                        </td>


                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td width="100%">
                                <table width="100%" border="1" cellspacing="0" cellpadding="0"
                                       style="text-align: left;">
                                    <tr>
                                        <td height="30">
                                                    <span class="style8" style="float: left;">&nbsp;<u>Terms and Conditions</u>&nbsp;(<u>الشروط
                                                            والأحكام</u>)</span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <ul style="margin: 6px 0px;">
                                                <li>Received complete invoiced quantity in good condition</li>
                                                <li>Official receipt is mandatory for payments</li>
                                                <li>Please issue cheque on behalf
                                                    of <?php echo $salesOrder->ou_name; ?> because our company name
                                                    has been changed.
                                                </li>
                                            </ul>

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
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="360" align="left">
                                            <table width="360" height="60" border="2" cellpadding="0" cellspacing="0"
                                                   style="margin: 0px 0px;">
                                                <tr>
                                                    <td valign="top">
                                                        <span class="style8">Customers Signature</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="360" align="right">
                                            <table width="355" height="60" border="2" cellpadding="0" cellspacing="0"
                                                   style="margin: 0px 0px;">
                                                <tr>
                                                    <td valign="top">
                                                        <span class="style8">For <?php echo $salesOrder->ou_name; ?></span>
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

        <input type="hidden" name="hdnPageCount" id="hdnPageCount" value="1"/>
    </div>
</form>
</body>
<script type="text/javascript">
    // window.print();
    // setTimeout('window.close()', 100);
</script>
</html>
