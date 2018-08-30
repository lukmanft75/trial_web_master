<!--============================================================================================-->
<?=$t->start("","data_content");?>
<?=$t->header(array("Divisi","Reimbursement","Fee/Total PO","Tax 23","VAT","Sales Order","Outstanding"));?>
<?php
	$td_sum_style = "align='right' style='font-weight:bold;'";
	
	$_reimbursement = 0;
	$_total_po = 0;
	$_tax23 = 0;
	$_vat = 0;
	$_salesorder = 0;
	$_outstanding = 0;
	$db->addtable("divisions");
	$db->order("id");
	foreach($db->fetch_data(true) as $arrs){
		$db->addtable("invoice");$db->awhere("issue_at >= '".$startdate."-01' AND issue_at <=  '".$enddate."-31' AND division_id='".$arrs["id"]."'");
		$reimbursement = 0;
		$total_po = 0;		
		$tax23 = 0;			
		$vat = 0;			
		$salesorder = 0;	
		$outstanding = 0;	
		
		foreach($db->fetch_data(true) as $invoice){
			$reimbursement += $invoice["reimbursement"];
			$total_po += $invoice["total_po"];
			$tax23 += $invoice["tax23"];
			$vat += $invoice["vat"];
			$salesorder += $invoice["total"];
			$outstanding += ($invoice["total"] - $invoice["receive"]);
		}
			
		$_reimbursement += $reimbursement;
		$_total_po += $total_po;
		$_tax23 += $tax23;
		$_vat += $vat;
		$_salesorder += $salesorder;
		$_outstanding += $outstanding;
		
		$__salesorder[$arrs["id"]] = $salesorder;
		$__outstanding[$arrs["id"]] = $outstanding;
		
		echo $t->row(array($arrs["name"],format_amount($reimbursement),format_amount($total_po),format_amount($tax23),format_amount($vat),format_amount($salesorder),format_amount($outstanding)),
					 array("","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'"));
	}
	echo $t->row(array("",format_amount($_reimbursement),format_amount($_total_po),format_amount($_tax23),format_amount($_vat),format_amount($_salesorder),format_amount($_outstanding)),
				 array("",$td_sum_style,$td_sum_style,$td_sum_style,$td_sum_style,$td_sum_style,$td_sum_style));
?>
<?=$t->end();?>
<!--============================================================================================-->


<!--============================================================================================-->
<?php
	$total_value = array();
	$arrheader_bymonth[] = "Divisi";
	$nowdate = $startdate;
	$enddate_1 = date("Y-m",mktime(0,0,0,substr($enddate,5,2) + 1,1,substr($enddate,0,4)));
	while($nowdate != $enddate_1){
		$arrheader_bymonth[] = substr($nowdate,5,2)."/".substr($nowdate,0,4);
		
		$db->addtable("divisions");
		$db->order("id");
		foreach($db->fetch_data(true) as $arrs){
			$reimbursement = 0;
			$total_po = 0;		
			$tax23 = 0;			
			$vat = 0;			
			$salesorder = 0;	
			$outstanding = 0;	
			
			$db->addtable("invoice");$db->awhere("issue_at LIKE '".$nowdate."%' AND division_id='".$arrs["id"]."'");
			foreach($db->fetch_data(true) as $invoice){
				$reimbursement += $invoice["reimbursement"];
				$total_po += $invoice["total_po"];
				$tax23 += $invoice["tax23"];
				$vat += $invoice["vat"];
				$salesorder += $invoice["total"];
				$outstanding += ($invoice["total"] - $invoice["receive"]);
			}
			
			$total_value["reimbursement"][$arrs["id"]][$nowdate] = $reimbursement;
			$total_value["total_po"][$arrs["id"]][$nowdate] = $total_po;
			$total_value["tax23"][$arrs["id"]][$nowdate] = $tax23;
			$total_value["vat"][$arrs["id"]][$nowdate] = $vat;
			$total_value["salesorder"][$arrs["id"]][$nowdate] = $salesorder;
			$total_value["outstanding"][$arrs["id"]][$nowdate] = $outstanding;
		}

		$nowdate = date("Y-m",mktime(0,0,0,(substr($nowdate,5,2) * 1) + 1,1,substr($nowdate,0,4)));	
	}
	$arrheader_bymonth[] = "TOTAL";
?>
<!--============================================================================================-->

<!--============================================================================================-->
<?php
	$arr_posts["reimbursement"] = "Reimbursement";
	$arr_posts["total_po"] = "Fee/Total PO";
	$arr_posts["tax23"] = "Tax 23";
	$arr_posts["vat"] = "VAT";
	$arr_posts["salesorder"] = "Sales Order";
	$arr_posts["outstanding"] = "Outstanding";
	foreach($total_value as $post_value => $arr_values){
?>
		<br><br>
		<h3><b><?=$arr_posts[$post_value];?></b></h3>
		<?=$t->start("","data_content");?>
		<?=$t->header($arrheader_bymonth);?>
		<?php
			$arr_cols_total = array();
			$db->addtable("divisions");
			$db->order("id");
			foreach($db->fetch_data(true) as $arrs){
				$arr_rows = array();
				$ii = 0;
				$arr_rows[$ii] = $arrs["name"];
				$arr_cols_total[$ii] = "";
				$arr_row_style[$ii] = "";
				$arr_row_total_style[$ii] = "";
				$subtotal = 0;
				foreach($total_value[$post_value][$arrs["id"]] as $nowdate => $value){
					$ii++;
					$subtotal += $value;
					$arr_rows[$ii] = format_amount($value);
					$arr_cols_total[$ii] += $value;
					$arr_row_style[$ii] = "align='right'";
					$arr_row_total_style[$ii] = $td_sum_style;
				}
				$arr_rows[$ii+1] = format_amount($subtotal);
				$arr_row_style[$ii+1] = $td_sum_style;
				echo $t->row($arr_rows,$arr_row_style);
			}
			
			$TOTAL = 0;
			foreach($arr_cols_total as $key => $value){
				if($value != 0){
					$arr_cols_total[$key] = format_amount($value);
					$TOTAL += $value;
				}
			}
			$arr_cols_total[$key+1] = format_amount($TOTAL);
			$arr_row_total_style[$key+1] = $td_sum_style;
			
			echo $t->row($arr_cols_total,$arr_row_total_style);
		?>
		<?=$t->end();?>
<?php
	}
?>
<!--============================================================================================-->

<!--============================================================================================-->
<br><br>
<?=$t->start("","data_content");?>
<?=$t->header(array("Divisi","Client","Sales Order","Outstanding"));?>
<?php
	$arr_row_total_style = array("","",$td_sum_style,$td_sum_style);
	$arr_row_style = array("","","align='right'","align='right'");
	$db->addtable("divisions");
	$db->order("id");
	foreach($db->fetch_data(true) as $arrs){
		echo $t->row(array($arrs["name"],"",format_amount($__salesorder[$arrs["id"]]),format_amount($__outstanding[$arrs["id"]])),$arr_row_total_style);
		$db->addtable("invoice");$db->addfield("distinct(concat(client_id)) as client_id");$db->awhere("issue_at >= '".$startdate."-01' AND issue_at <=  '".$enddate."-31' AND division_id='".$arrs["id"]."'");
		foreach($db->fetch_data(true) as $clients){
			$client_name = $db->fetch_single_data("clients","name",array("id" => $clients["client_id"]));
			$db->addtable("invoice");
			$db->addfield("distinct(concat(client_id)) as client_id");
			$db->addfield("sum(concat(total)) as total");
			$db->addfield("sum(concat(total - receive)) as outstanding");
			$db->awhere("issue_at >= '".$startdate."-01' AND issue_at <=  '".$enddate."-31' AND division_id='".$arrs["id"]."' AND client_id = '".$clients["client_id"]."'");
			$invoices = $db->fetch_data();
			echo $t->row(array("",$client_name,format_amount($invoices["total"]),format_amount($invoices["outstanding"])),$arr_row_style);
		}
		echo $t->row(array("","","",""),array("style='height:5px;'"));
	}
	echo $t->row(array("<b>TOTAL</b>","",format_amount($_salesorder),format_amount($_outstanding)),$arr_row_total_style);
?>
<?=$t->end("","data_content");?>
<!--============================================================================================-->