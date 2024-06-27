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
        @media print {
            @page {
                size: auto;
                margin: 0;
            }

            @page :first {
                margin: 0;
            }

            @page :last {
                margin: 0;
            }
        }

    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.print();
            // setTimeout('window.close()', 100);
        });
    </script>
</head>
<body>
<form method="post" action="./PromotionSalesInvoice.aspx?OrderId=104731" id="form1">
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
                            <span class="style4">Memo</span>

                        </tr>
                        <tr>
                            <td width="100%" >
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr id="rpSalesInvoice_dvUAE_0">
                                        <td width="100%">
                                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr style="text-align: center">
                                                                <td>
                                                                    <span class="style4" style="text-align: center"><?php echo $salesOrder->ou_name; ?></span>
                                                                    <span class="style4" style="text-align: center"><?php echo $salesOrder->note; ?></span>
                                                                </td>
                                                            </tr style="text-align: center">

                                                            <tr>
                                                                <td style="text-align: center">Address : {{$salesOrder->address}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="text-align: right">
                                                                    <span class="style8"> <?php
                                                                        if ($salesOrder->vat_status == 1) {
                                                                            echo "Exice No:" . $salesOrder->tax_number;
                                                                        }
                                                                        ?></span>
                                                                </td>

                                                            </tr>
                                                            <tr style="text-align: left">
                                                                <td>
                                                                    <span class="style8" > <?php
                                                                        if ($salesOrder->vat_status == 1) {
                                                                            echo "VAT No:" . $salesOrder->vat_number;
                                                                        }

                                                                        ?></span>
                                                                </td>

                                                            </tr>


                                                        </table>
                                                    </td>
                                                    <td align="center">
                                                        <!-- <img src="{{ asset("theme/image/logo.png")}}" alt="" width="156" height="100"/>-->
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
                                            <span class="style8">Order (تاريخ الطلب):</span>
                                            {{$salesOrder->order_date}}
                                        </td>
                                        <!--</tr>
                                        <tr>-->
                                        <td height="30" style="width: 33.33%;" align="center">
                                            <span class="style8">Invoice  (تاريخ الفاتورة):&nbsp;</span>
                                            {{$salesOrder->delivery_date}}
                                        </td>
                                        <!--</tr>
                                        <tr>-->
                                        <td height="30" style="width: 33.33%;" align="right">
                                            <span class="style8"> Delivery  (تاريخ التوصيل):&nbsp;</span>
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
                                        <td width="30%" align="left">
                                            <span style="font-size:12px;" class="style8">CS.Info</span>
                                        </td>
                                        <td width="40%" class="style4" align="center">


                                        </td>
                                        <td width="">
                                            <span class="style8"></span>
                                        </td>
                                        <td>
                                             <span class="style8"><?php

                                                 if ($salesOrder->vat_status == 1) {
                                                     echo "CS. Reg. No:" . $salesOrder->VAT_TRN;
                                                 } ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr style="border: none;">
                            <td width="100%">
                                <table width="100%" border="1" cellpadding="0" cellspacing="0"
                                       bordercolor="#d3d3d3">
                                    <tr>
                                        <td width="100" valign="top" style="text-align:center;">
                                            <span class="style4">&nbsp;Delivery Address </span><br/>
                                            <span class="style4 invoiceArabic">&nbsp;وجهة الشحن</span>
                                        </td>
                                        <td width="350">
                                            {{$salesOrder->Site_Name}}
                                            <br/>{{$salesOrder->site_address}}

                                        </td>
                                        <td width="100" valign="top" style="text-align:center;">
                                            <span class="style4">&nbsp;Billing Address</span><br/>
                                            <span class="style4 invoiceArabic">&nbsp;وجهة الفوترة</span>
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
                                    <tr style="text-align: center">

                                        <td>
                                            <span class="style8">Order. No:{{$salesOrder->Order_ID}} </span>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="">
                                    <tr bgcolor="">
                                        <td width="100">
                                            <span style="font-size:12px;" class="style12">SR</span><br/>
                                            <span style="font-size:12px;" class="style12">مندوب المبيعات</span>
                                        </td>
                                        <td width="90">
                                            <span style="font-size:12px;" class="style12">MOBILE</span><br/>
                                        </td>
                                        <td width="90">
                                            <span style="font-size:12px;" class="style12">EMIRATES</span><br/>
                                            <span style="font-size:12px;" class="style12">عقوملا</span>
                                        </td>
                                        <td width="90">
                                            <span style="font-size:12px;" class="style12">SITE CODE</span><br/>
                                            <span style="font-size:12px;" class="style12">رقم الفرع</span>
                                        </td>
                                        <td width="90">

                                            <span style="font-size:12px;" class="style12">CUST CODE</span><br/>
                                            <span style="font-size:12px;" class="style12">رقم العميل</span>
                                        </td>
                                        <td width="90">
                                            <span style="font-size:12px;" class="style12">CURRENCY</span><br/>
                                            <span style="font-size:12px;" class="style12">العملة</span>
                                        </td>
                                        <td width="90">

                                            <span style="font-size:12px;" class="style12">PAYMENT</span><br/>
                                            <span style="font-size:12px;" class="style12">شروط السداد</span>
                                        </td>


                                    </tr>
                                    <tr>
                                        <td height="24">&nbsp;{{$salesOrder->preseller_name}}
                                        </td>
                                        <td height="24">
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

                                <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="">
                                    <tr bgcolor="">
                                        <!-- <td width="35" height="23" rowspan="2">
                                             <span style="font-size:12px;" class="style12">&nbsp;SL#</span><br/>

                                             <span style="font-size:12px;" class="style12 invoiceArabic">&nbsp;الرقم التسلسلي</span>
                                         </td>-->
                                        <td width="65" height="23" rowspan="2"  align="center">
                                            <span style="font-size:12px;" class="style12">&nbsp;PRODUCT CODE</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;رمز الصنف</span>
                                        </td>
                                        <td width="95" height="23" rowspan="2"  align="center">
                                            <span style="font-size:12px;" class="style12">&nbsp;PRODUCT Name</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;رمز الصنف</span>
                                        </td>
                                        <td width="50" height="23" rowspan="2" align="center">
                                            <span style="font-size:12px;" class="style12">UOM</span><br/>
                                            <span style="font-size:12px;" class="style12 invoiceArabic">&nbsp;وحدة القياس</span>
                                        </td>
                                        <td colspan="2"align="center">
                                            <span class="style12" style="font-size:12px;" style="margin-left:44px;">&nbsp;Quantity</span><br/>
                                            <span style="font-size:12px;" class="style12 invoiceArabic"
                                                  style="text-align:center">&nbsp;الكمية</span>
                                        </td>

                                        <td width="60" height="23" rowspan="2" align="center">
                                            <span style="font-size:12px;" class="style12">&nbsp;CTN PRICE</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;سعر الوحدة</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2" align="center">
                                            <span style="font-size:12px;" class="style12">&nbsp;GROSS</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;سعر الوحدة</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2" align="center">
                                            <span class="style12">&nbsp;Total Excise Duty</span><br/>
                                            <span class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td colspan="2" align="center">
                                            <span class="style12" style="font-size:12px;" style="margin-left:44px;">&nbsp;Discount</span><br/>
                                            <span style="font-size:12px;" class="style12 invoiceArabic"
                                                  style="text-align:center">&nbsp;نسبة الخصم</span>
                                        </td>

                                        <td width="60" height="23" rowspan="2" align="center">
                                            <span style="font-size:12px;" class="style12">VAT %</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2" align="center">
                                            <span style="font-size:12px;" class="style12">VAT AMOUNT</span>
                                        </td>
                                        <td width="60" height="23" rowspan="2" align="center">
                                            <span style="font-size:12px;" class="style12">Total(Excise +VAT)</span>
                                        </td>
                                    </tr>
                                    <tr bgcolor="">
                                        <td align="center"><span style="font-size:12px;"
                                                  class="style12">CTN</span></td>
                                        <td align="center"><span style="font-size:12px;"
                                                  class="style12">PCS</span></td>

                                        <td align="center"><span style="font-size:12px;"
                                                  class="style12">Dis%</span></td>
                                        <td align="center"><span style="font-size:12px;"
                                                  class="style12">Dis AMOUNT</span></td>
                                    </tr>
                                    <?php
                                    $exciseDuty = 0;
                                    $totalVatAmount = 0;
                                    $totalInclVat = 0;
                                    $totalgrossAnt = 0;
                                    $total_discount_amount=0;
                                    $count = 1;

                                    foreach ($salesOrderLine as $index=>$salesOrderLine1){

                                    $totalgrossAnt += $salesOrderLine1->Total_Item_Price;
                                    $totalAmt = $salesOrderLine1->Total_Item_Price;
                                    $exciseDuty += $salesOrderLine1->gst;
                                    $totalVatAmount += $salesOrderLine1->tvat;
                                    $total_discount_amount+=$salesOrderLine1->Discount;
                                    $totalIncl_Vat =$totalAmt+$salesOrderLine1->gst+$salesOrderLine1->tvat;
									$totalInclVat +=$totalIncl_Vat;
                                    ?>
                                    <tr align="center">
                                        <td height="10">
                                            <?php echo $salesOrderLine1->Product_id; ?>
                                        </td>
                                        <td height="10">
                                            <?php echo $salesOrderLine1->Product_Name; ?>
                                        </td>
                                        <td height="10">
                                            <?php echo $salesOrderLine1->ctn_size; ?>
                                        </td>

                                        <td height="10">&nbsp; <?php echo $salesOrderLine1->ctn; ?>
                                        </td>
                                        <td height="10">&nbsp;<?php echo $salesOrderLine1->pcs; ?>
                                        </td>


                                        <td height="10" >
                                            &nbsp;<?php echo $salesOrderLine1->Rate; ?>
                                        </td>
                                        <td height="10" >

                                            &nbsp;<?php echo $salesOrderLine1->Total_Item_Price; ?>
                                        </td>
                                        <td height="10" >
                                            <?php echo $salesOrderLine1->gst; ?>
                                        </td>
                                        <td height="10" >
                                            <span>&nbsp;&nbsp;<?php echo $salesOrderLine1->ratio; ?></span>

                                        </td>

                                        <td height="23" >
                                            <?php echo $salesOrderLine1->Discount; ?>
                                        </td>
                                        <td height="10" >
                                            <?php echo $salesOrderLine1->vat; ?>
                                        </td>
                                        <td height="10" >
                                            <?php echo $salesOrderLine1->tvat; ?>                                            &nbsp;
                                        </td>
                                        <td height="10" >
                                            <?php echo $totalIncl_Vat; ?>
                                        </td>
                                    </tr>

                                    <?php
                                    $count++;
                                    }
                                    
                                    ?>
                                    <tr>

                                        <td class="style8" colspan="5">Total:</td>
                                        <td class="style8" height="10"
                                            align="right"> <span class="style8"><?php echo number_format($totalgrossAnt, $salesOrder->round); ?></span></td>
                                        <td class="style8" height="10"
                                            align="right"><?php echo $exciseDuty; ?>
                                        </td>
                                        <td class="style8" height="10"
                                            align="right"></td>
                                        <td class="style8" height="10"
                                            align="right">
                                                    <?php echo $total_discount_amount; ?>
                                                   
                                            </td>
                                        <td></td>
                                        <td class="style8" height="10"
                                            align="right"><?php echo number_format($totalVatAmount, $salesOrder->round); ?></td>
                                        <td class="style8" height="10"
                                            align="right"><?php
                                            if ($salesOrder->total_price!= '') {
                                                echo number_format($totalInclVat-$total_discount_amount, $salesOrder->round);
                                            } else {
                                                echo (double)(($totalInclVat * $salesOrder->round_digit) / $salesOrder->round_digit) - $totalInclVat;
                                            }
                                            ?></td>

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
                                                            if ($salesOrder->total_price!= '') {
                                                                echo ucwords(convert_number_to_words($totalInclVat-$total_discount_amount)) . " " .$salesOrder->currency . ' Only';
                                                            } else {
                                                                echo ucwords(convert_number_to_words((double)(($totalInclVat * $salesOrder->round_digit) / $salesOrder->round_digit))) . " " . $salesOrder->currency . ' Only';
                                                            }
                                                            ?>
                                                            &nbsp;</span></span>
                                        </td>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">Payable Amount: <?php
                                                            if ($salesOrder->total_price!= '') {
                                                                echo number_format($totalInclVat-$total_discount_amount, $salesOrder->round);
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
                               <!-- <table width="100%" border="1" cellspacing="0" cellpadding="0"
                                       style="text-align: left;">
                                    <tr>
                                        <td>
                                                    <span style="font-size:12px;" class="style8" style="float: left;">&nbsp;<u>Terms and Conditions</u>&nbsp;(<u>الشروط
                                                            والأحكام</u>)</span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <ul style="margin: 6px 0px;">
                                                <li>Received complete invoiced quantity in good condition</li>
                                                <li>Official receipt is mandatory for payments</li>
                                                <li>Please issue cheque on behalf
                                                    of <?php // echo $salesOrder->ou_name; ?></li>
                                            </ul>

                                        </td>
                                    </tr> 
                                </table>-->
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;
                            </td>
                        </tr>
						
                        <tr>
                            <td>
                                <tr>
                          <td  align="left"valign="top">
                            <span class="style8">Buyer's Approval</span>
                           </td>
                        </tr>
						<tr>
                          <td  align="right"valign="top">
                            <span class="style8">Enterprise: <?php echo $salesOrder->ou_name; ?></span>
                           </td>
                        </tr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                        <span style="font-size:12px;" class="style5"
                                              style="font-size: 20px; font-weight: bold;">Thanks for cooperating</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</form>
</body>
<script type="text/javascript">
    // window.print();
    // setTimeout('window.close()', 100);
</script>
</html>
