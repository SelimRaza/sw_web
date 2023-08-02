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
	<meta charset="utf-8">
    <!-- <link rel="stylesheet" href="card.css"> -->
    <style type="text/css">
	 @media print
        {
          @page
            {         
            size: A5 landscape;
             
            }
        }
        body {
            margin-left: 3px;
            margin-top: 3px;
            margin-right: 3px;
            margin-bottom: 3px;
        }

        body, th {
            font-family: Times New Roman, Arial, Helvetica, sans-serif;
            font-size: 12;
            color: #20292c;
        }
		body, tr,td {
            font-family: Times New Roman, Arial, Helvetica, sans-serif;
            font-size: 11;
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
	
html {
    /* off-white, so body edge is visible in browser */
    background: #eee;
}

    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.print();
            // setTimeout('window.close()', 100);
        });
    </script>
</head>
<body class="face face-back">

    <div>

        <table >
            <tr>
                <td align="center">
                    <table >
                      <!--  <tr style="text-align: center">
                            <span class="style4">{{$salesOrder->invoice_title}}</span>

                        </tr> -->
                        <tr>
                            <td width="100%">
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr id="rpSalesInvoice_dvUAE_0">
                                        <td width="100%">
                                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <tr style="text-align: center">
                                                                <td>
                                                                    <!--<span class="style4"> SUPPLIERS DETAILS:</span>-->
                                                                    <span class="style4"> {{$salesOrder->invoice_title}}</span>
                                                                </td>

                                                            </tr>
                                                            <tr style="text-align: center">
                                                                <td>
                                                                    <span class="style4"><?php echo $salesOrder->ou_name; ?></span>
                                                                    <span class="style4"><?php echo $salesOrder->note; ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr style="text-align: center">
                                                                <td>Warehouse : {{$salesOrder->address}}
																<br/>							                                    
                                                                </td>
                                                            </tr>																														
                                                           <td width="100%"> <?php
                                                                        if ($salesOrder->vat_status == 1) {  ?></br>                                                                             
															  <table width="100%" height="24" border=".5" cellpadding="0" cellspacing="0"
                                                               bordercolor="#d3d3d3"
                                                                style="border-top: none;">
															<tr >
                                                                <td style="width: 50%;" align="left">                                                                    
																<span class="style8">Exice TRN:{{$salesOrder->tax_number}}</span>		
                                                                </td>                                                           
                                                                <td style="width: 50%;" align="left">                                                                    
																<span class="style8">VAT TRN:{{$salesOrder->vat_number}}</span>
                                                                </td>
                                                            </tr>			
                                                                       <?php }
                                                                        ?> 
															   </table>			
														   </td>
                                                        </table>
                                                    </td>
                                                   <!-- <td align="center">
                                                         <img src="{{ asset("theme/image/logo.png")}}" alt="" width="100" height="60" style="margin-top:2px!important;"/>                                                                                                                                                                              
                                                        <p id="qrcode_n"></p>	        	   																									
													    </td> -->
                                                </tr>                                   
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>							
                        </tr>
						<!--<tr>						 
                            <td width="100%" text-align: center>
							<h4 style="text-align:center;"> <span class="style4">{{$salesOrder->invoice_title}}</span></h4>                                
                            </td>
                        </tr>-->
						
						<tr>
                            <td width="100%">
                                <table width="100%" height="24" border="1" cellpadding="0" cellspacing="0"
                                       bordercolor="#d3d3d3"
                                       style="border-top: none;">
                                    <tr>
                                        <td style="width: 50%;" align="left">                                            
											<span class="style8">Tax Invoice No:{{$salesOrder->vat_sl_number}} </span>
                                        </td>                                      
                                        <td style="width: 50%;" align="left">
                                            <span class="style8">Supplier Ref. No:{{$salesOrder->Order_ID}} </span>
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
                                        <td >                                          
												<span class="style8">PO No: {{$salesOrder->po_no}} </span>
                                        </td>
                                        <td >
                                            <span class="style8">PO Date: {{$salesOrder->po_date}} </span>
                                        </td>                                    
                                        <td >                                            
											<span class="style8">DM ID: {{$salesOrder->DM_CODE}} </span>
                                        </td>                                        
                                        <td>
                                            <span class="style8">Vehicle No: {{$salesOrder->V_NAME}} </span>
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
                                            <span class="style8">Order Date:</span>
                                            {{$salesOrder->order_date}}
                                        </td>
                                       
                                        <td height="30" style="width: 33.33%;" align="center">
                                            <span class="style8">Invoice Date:&nbsp;</span>
                                            {{$salesOrder->delivery_date}}
                                        </td>
                                        
                                        <td height="30" style="width: 33.33%;" align="right">
                                            <span class="style8"> Delivery Date:&nbsp;</span>
                                            {{$salesOrder->delivery_date}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>                           
                                        <td width="30%" align="left">
                                            <span style="font-size:12px;" class="style8"><u>SHIP TO</u>:{{$salesOrder->site_address}}</span>
                                        </td>                                                                                                                   
                        </tr>
						                                             
                        <tr>
                            <td>
                                <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr bgcolor="#b0c4de">
                                        <td width="100"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">SALES MAN</span><br/>
                                            
                                        </td>
                                        <td width="90"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">MOBILE</span><br/>
                                        </td>
                                      
                                        <td width="90"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">SITE CODE</span><br/>
                                          
                                        </td>
                                        <td width="90"style="text-align:center;">

                                            <span style="font-size:12px;" class="style12">CUST CODE</span><br/>
                                           
                                        </td>
                                        <td width="90"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">CURRENCY</span><br/>
                                          
                                        </td>
                                        <td width="90"style="text-align:center;">

                                            <span style="font-size:12px;" class="style12">PAYMENT</span><br/>
                                           
                                        </td>


                                    </tr>
                                   <tr>
                                        <td height="24"style="text-align:center;">&nbsp;{{$salesOrder->preseller_name}}
                                        </td>
                                        <td height="24"style="text-align:center;">&nbsp;{{$salesOrder->preseller_mob}}
                                        </td>
                                       
                                        <td height="24"style="text-align:center;">{{$salesOrder->customer_number}}
                                        </td>
                                        <td height="24"style="text-align:center;">&nbsp;{{$salesOrder->outlet_id}}
                                        </td>
                                        <td height="24"style="text-align:center;">{{$salesOrder->currency}}
                                        </td>
                                        <td height="24"style="text-align:center;">&nbsp;{{$salesOrder->Payment_Type}}
                                        </td>

                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%">

                                <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#d3d3d3">
                                    <tr bgcolor="#b0c4de">
                                       <!--<td width="10" height="23" rowspan="2"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">Sl</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;</span>
                                        </td> -->
                                        <td width="55" height="20" rowspan="2"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">ITEM CODE</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
										<td width="350" height="20" rowspan="2" style="text-align:center">
                                            <span style="font-size:12px;text-align:center" class="style12">&nbsp;ITEM Name</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td width="50" height="20" rowspan="2"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">UOM</span><br/>                                        
                                        </td>
                                        <td colspan="2"style="text-align:center;">
                                            <span class="style12" style="font-size:12px;" style="text-align:center;">&nbsp;Quantity</span><br/>
                                            <span style="font-size:12px;" class="style12 invoiceArabic"
                                                  style="text-align:center">&nbsp;</span>
                                        </td>

                                        <td width="60" height="20" rowspan="2"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">&nbsp;CTN PRICE</span><br/>
                                           <!-- <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;</span> -->
                                        </td>
                                        <td width="50" height="20" rowspan="2"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">&nbsp;GROSS Amnt</span><br/>
                                            <span style="font-size:12px;"
                                                  class="style12 invoiceArabic">&nbsp;</span>
                                        </td>
                                        <td colspan="2"style="text-align:center;">
                                            <span class="style12" style="font-size:12px;" >&nbsp;Discount</span><br/>                                            
                                        </td>
                                        <td width="60" height="20" rowspan="2"style="text-align:center;">
                                            <span style="font-size:12px;" class="style12">Net Amt</span>
                                        </td>
                                    </tr>
                                    <tr bgcolor="#b0c4de">
                                        <td style="text-align:center;"><span style="font-size:12px;"
                                                  class="style12">CTN</span></td>
                                        <td style="text-align:center;"><span style="font-size:12px;"
                                                  class="style12">PCS</span></td>

                                        <td style="text-align:center;"><span style="font-size:12px;"
                                                  class="style12">Dis%</span></td>
                                        <td style="text-align:center;"><span style="font-size:12px;"
                                                  class="style12">DisVal</span></td>
                                    </tr>
                                    <?php
                                    $exciseDuty = 0;
                                    $totalVatAmount = 0;
                                    $totalInclVat = 0;
                                    $totalGrossAmt = 0;
                                    $newNet = 0;
                                    $TotalDiscount = 0;
                                    $count = 1;

                                    ?>

                                    @foreach ($salesOrderLine as $index=>$salesOrderLine1)

                                        @php
                                          $totalVatAmount += $salesOrderLine1->total_vat;
										  $totalGrossAmt += $salesOrderLine1->Total_Item_Price;
										  $exciseDuty += $salesOrderLine1->total_gst;
										  $TotalDiscount +=  $salesOrderLine1->total_discount;
										  $newNet += $salesOrderLine1->net_amount;
                                        @endphp


                                    <tr id="invoice-line-{{$count}}">
                                       <!-- <td height="10"style="text-align:center;">
                                            {{ $count}}
                                        </td> -->
										<td height="10"style="text-align:center;">
                                            {{$salesOrderLine1->Product_id }}
                                        </td>
										<td align="left"> <span style="float: left;font-size: 11px;">&nbsp; {{ \Illuminate\Support\Str::limit($salesOrderLine1->Product_Name,57)}} </span>  </td>
										<!--<td align="left"> <span style="float: left;font-size: 12px;">&nbsp; {{$salesOrderLine1->Product_Name}} </span>  </td> -->
                                        <td height="10"style="text-align:center;"> {{ $salesOrderLine1->ctn_size }} </td>

                                        <td height="10"style="text-align:center;">&nbsp;{{ $salesOrderLine1->ctn }} </td>
                                        <td height="10"style="text-align:center;">&nbsp;{{$salesOrderLine1->pcs}} </td>

                                        <td height="10" align="right"style="text-align:center;"> {{$salesOrderLine1->Rate}} </td>
                                        <td height="10" align="right"style="text-align:center;"> {{ $salesOrderLine1->Total_Item_Price }} </td>
                                        
                                        <td height="10" align="right"style="text-align:center;"> <span>&nbsp;&nbsp;{{$salesOrderLine1->ratio}}</span> </td>

                                        <td height="23" align="right"style="text-align:center;"> {{ $salesOrderLine1->total_discount }} </td>
                                      
                                        <td height="10" align="right"style="text-align:center;"> {{ $salesOrderLine1->net_amount }} </td>
                                    </tr>
                                  
								 
                                    @php($count++)
                                  <!--<tr style="page-break-after: auto"></tr> -->
                                    @endforeach

									
                                    <tr>

                                        <td class="style8" colspan="6">Total:</td>
                                        
                                       
                                        <td class="style8" height="10"
                                            align="right"><?php echo $totalGrossAmt; ?></td>
											
										<td></td>
                                        <td class="style8" height="10"
                                            align="center"><?php echo $TotalDiscount; ?></td>                                                                            
                                        <td class="style8" height="10"
                                            align="right"><?php
                                            if ($newNet != 0) {
                                                echo number_format($newNet, $salesOrder->round);
                                            } else {
                                                echo (double)(($newNet * $salesOrder->round_digit) / $salesOrder->round_digit) - $newNet;
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
                                                            if ($newNet != 0) {
                                                                echo ucwords(convert_number_to_words(round($newNet,2))) . " " .$salesOrder->currency . ' Only';
                                                               // echo ucwords(convert_number_to_words($newNet)) . " " .$salesOrder->currency . ' Only';
                                                            } else {
                                                                echo ucwords(convert_number_to_words((double)(($totalInclVat * $salesOrder->round_digit) / $salesOrder->round_digit))) . " " . $salesOrder->currency . ' Only';
                                                            }
                                                            ?>
                                                            &nbsp;</span></span>
                                        </td>

                                        <td width="60%" height="26" align="right">
                                                    <span id="rpSalesInvoice_spnGrandTotal_0">
                                                        <span class="style8">Payable Amount: <?php
                                                            if ($newNet != 0) {
                                                                echo number_format($newNet, $salesOrder->round);
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
                                        <td>
                                                    <span style="font-size:12px;" class="style8" style="float: left;">&nbsp;<u>Terms and Conditions</u>&nbsp;</span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <ul style="margin: 6px 0px;">
                                               <!-- <li>Received complete invoiced quantity in good condition</li>
                                                <li>Official receipt is mandatory for payments</li>   -->                                             
                                                <li class="style8">All cheques should be crossed and made payable to
                                                     <?php echo $salesOrder->ou_name; ?></li>
												<li class="style8">Bank:Maybank , Account Number: 512745214401 (online)</li>
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
                                                    <td valign="top">
                                                        <span class="style8">Goods Received by (and Seal)</span>
                                                    </td>
                                                </tr>
									
									<tr>
                                        <td width="360" align="left">
                                            <table width="360" height="40" border="2" cellpadding="0" cellspacing="0"
                                                   style="margin: 0px 0px;">
                                                <tr>
                                                    <td valign="top">
                                                        <span class="style8">Customers Signature</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="360" align="right">
                                            <table width="355" height="40" border="2" cellpadding="0" cellspacing="0"
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
                                        <span style="font-size:12px;" class="style5"
                                              style="font-size: 20px; font-weight: bold;">Thank you for your
                                            business</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
 <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script>
	
    $(document).ready(function (){
        $('#invoice-line-10').css("break-after", "page");
        
        let j = 10;
        		
		 for(var i = 1; i < {{$count%21}}; i++)//see that I removed the $ preceeding the `for` keyword, it should not have been there
		 {
			j=j+21
			$(`#invoice-line-${j}`).css("break-after", "page");
		 } 		
    })

    </script>
</html>
