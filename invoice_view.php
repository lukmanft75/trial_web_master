<?php include_once "head.php";
		include_once "func.convert_number_to_words.php";

	$db->addtable("invoice");$db->where("id",$_GET["id"]);$db->limit(1);$invoice = $db->fetch_data();
	//metode penghitungan 2 digit di blakang koma tanpa pembulatan
	// if($invoice["vat"] != 0) $invoice["vat"] = substr(number_format($invoice["vat"],3,".",""),0,-1);
	// if($invoice["total"] != 0) $invoice["total"] = substr(number_format($invoice["total"],3,".",""),0,-1);
	
	//metode penghitungan 2 digit di blakang koma dengan pembulatan
	if($invoice["vat"] != 0) $invoice["vat"] = number_format($invoice["vat"],2,".","");
	if($invoice["total"] != 0) $invoice["total"] = number_format($invoice["total"],2,".","");
	
	$print_config = explode("|",$invoice["print_config"]);
	
    $client = $db->fetch_single_data("clients","name",array("id"=>$invoice["client_id"]));
	$client .= "<br>".$db->fetch_single_data("clients","address",array("id"=>$invoice["client_id"]));
	if($print_config[1]){ 
		$print_config[5] = ($print_config[5] != "") ? $print_config[5]:$db->fetch_single_data("clients","pic",array("id"=>$invoice["client_id"]));
		$client .= "<br><br>Attn : ".$print_config[5];
	}
	if($print_config[2]){ 
		$invoice["num"] = substr_replace( $invoice["num"], '20', -2, 0 ); 
	}
	
	$decimalnum = 0;
	$curr_say = "";
	$_curr_say = "";
	$currency_id = $invoice["currency_id"];
	if($currency_id == "IDR") $currency = "Rupiah";
	if($currency_id == "USD"){ 
		$currency_id = "$";
		$decimalnum = 2;
		$curr_say = "US dollars, ";
		$_curr_say = " Only";
	}
	$po_no = $invoice["po_no"];
	$wcc_no = $db->fetch_single_data("wcc","wcc_no",array("po_no" => $po_no));
	$total_invoice_inwords = "#".$curr_say.ucwords(convert_number_to_words($invoice["total"],$decimalnum))." ".$currency.$_curr_say."#";
	$fontsize = 20;
	$rowspace = 24;
?>
	<?php for($row = 0; $row < 1200; $row+=20){ ?>
		<?php for($col = 0; $col < 1000; $col+=20){ ?>
			<?php
				if($col == 0 && $row == 0) $caps = 0;
				if($col == 0 && $row > 0) $caps = $row;
				if($col > 0 && $row == 0) $caps = $col;
				if($col > 0 && $row > 0) $caps = "";
			?>
			<div style="position:absolute;top:<?=$row;?>px;left:<?=$col;?>px;visibility:hidden;">.</div>
			<div style="position:absolute;top:<?=($row+15);?>px;left:<?=$col;?>px;font-size:10px;visibility:hidden;"><?=$caps;?></div>
		<?php } ?>
	<?php } ?>
	<?php
		$arr_issue_at = explode("-",$invoice["issue_at"]);
		if($print_config[3] == "") $print_config[3] = "1";
		if($print_config[3] == "2") $invoice["due_date"] = "Nett ".$invoice["due_date"]." Day(s)";
		if($print_config[3] == "1") $invoice["due_date"] = $invoice["due_date"]." Day(s)";
		if($print_config[3] == "3") $invoice["due_date"] = date("d F Y",mktime(0,0,0,$arr_issue_at[1],$arr_issue_at[2] + $invoice["due_date"],$arr_issue_at[0]));
		if($invoice["due_date"] == -1) $invoice["due_date"] = "Immediate";
	?>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;top:330px;left:25px;width:450px;"><?=$client;?></div>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;top:340px;left:720px;"><?=format_tanggal($invoice["issue_at"],"dFY");?></div>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;top:390px;left:720px;"><?=$invoice["num"];?></div>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;top:430px;left:720px;"><?=$invoice["due_date"];?></div>
	<?php 
		$top_detail = 570;		
		$rowspace = 18;
		$fontsize = 18;	

		if($po_no != ""){
			$top_detail += $rowspace;
			?><div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;"><b>PO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_no;?></b></div><?php
		}
		if($wcc_no != ""){
			$top_detail += $rowspace;
			?><div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$wcc_no;?></div><?php
		}
			
		$db->addtable("invoice_detail");$db->where("invoice_id",$_GET["id"]);$db->where("after_tax_rate",0);
		$no = 0;
		$_subtotal = 0;
		foreach($db->fetch_data(true) as $invoice_detail){
			$no++;
			$top_detail += $rowspace;
			$_subtotal += ($invoice_detail["reimbursement"]+$invoice_detail["fee"]);
	?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;"><?=$invoice_detail["description"];?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:560px;text-align: right;width:200px;"><?=$currency_id;?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:730px;text-align: right;width:200px;"><?=format_amount($invoice_detail["reimbursement"]+$invoice_detail["fee"],$decimalnum);?></div>
	<?php 
			$br_count = count(explode("<br>",strtolower($invoice_detail["description"])));
			if($br_count > 1){
				for($xx = 1; $xx < $br_count; $xx++){ $top_detail += $rowspace+10; }
			}
		}
		
		if($no > 1){//jika detail lebih dari 1 points
			$top_detail += $rowspace;
	?>
			<?php $top_detail += 5;?>
			<div style="position:absolute;top:<?=$top_detail;?>px;left:730px;text-align: right;width:200px;border-top:1px solid black;"></div>
			<?php $top_detail += 5;?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:480px;text-align: right;width:200px;">SUBTOTAL</div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:560px;text-align: right;width:200px;"><?=$currency_id;?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:730px;text-align: right;width:200px;"><?=format_amount($_subtotal,$decimalnum);?></div>
	<?php
			$top_detail += $rowspace;
		}
		
		if($invoice["additional_detail"] != ""){
			$top_detail += $rowspace;
			$invoice["additional_detail"] = str_replace(chr(10),"<br>",$invoice["additional_detail"]);
			
			?><div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;"><?=$invoice["additional_detail"];?></div><?php
			
			$br_count = count(explode("<br>",strtolower($invoice["additional_detail"])));
			if($br_count > 1){
				for($xx = 1; $xx < $br_count; $xx++){ $top_detail += $rowspace; }
			}
		}
		
		$tax_rate_wrote = false;
		if($invoice["vat"] != 0){
			$top_detail += $rowspace;
			if($top_detail < 666+(3*24)) $top_detail = 666+(3*24);
	?>
			<?php 
				if(!$tax_rate_wrote){ 
					$top_detail += $rowspace;
					$tax_rate_wrote = true;
			?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;">Tax Rate : </div>
			<?php } ?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:120px;">VAT (10%)</div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:560px;text-align: right;width:200px;"><?=$currency_id;?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:730px;text-align: right;width:200px;"><?=format_amount($invoice["vat"],$decimalnum);?></div>
	<?php
		}
		
		if($invoice["tax23"] != 0){
			$top_detail += $rowspace;
			if($top_detail > 890) $top_detail = 890;
	?>
			<?php 
				if(!$tax_rate_wrote){ 
					$top_detail += $rowspace;
					$tax_rate_wrote = true;
			?>
				<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;">Tax Rate : </div>
			<?php } ?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:120px;">PPh 23 (2%)</div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:560px;text-align: right;width:200px;"><?=$currency_id;?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:730px;text-align: right;width:200px;"><?=format_amount($invoice["tax23"],$decimalnum);?></div>
	<?php
		}

		$db->addtable("invoice_detail");$db->where("invoice_id",$_GET["id"]);$db->where("after_tax_rate",1);
		if(count($invoice_detail) > 0) $top_detail += $rowspace;
		foreach($db->fetch_data(true) as $invoice_detail){
			$top_detail += $rowspace;
			$_subtotal += ($invoice_detail["reimbursement"]+$invoice_detail["fee"]);
	?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;"><?=$invoice_detail["description"];?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:560px;text-align: right;width:200px;"><?=$currency_id;?></div>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:730px;text-align: right;width:200px;"><?=format_amount($invoice_detail["reimbursement"]+$invoice_detail["fee"],$decimalnum);?></div>
	<?php 
			$br_count = count(explode("<br>",strtolower($invoice_detail["description"])));
			if($br_count > 1){
				for($xx = 1; $xx < $br_count; $xx++){ $top_detail += $rowspace+10; }
			}
		}
		
		if($print_config[4]){
			if($top_detail < 808) $top_detail = ($rowspace * 16) + 520;
			else $top_detail += $rowspace + $rowspace;
	?>
			<div style="font-size:<?=$fontsize;?>px;position:absolute;top:<?=$top_detail;?>px;left:25px;">(Detail Explanations as Attached)</div>
	<?php
		}
	?>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;top:960px;left:560px;text-align: right;width:200px;"><b><?=$currency_id;?></b></div>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;top:960px;left:730px;text-align: right;width:200px;"><b><?=format_amount($invoice["total"],$decimalnum);?></b></div>
	<div style="font-size:<?=$fontsize;?>px;position:absolute;width:85%;height:70px;top:1062px;left:20px;vertical-align: middle;text-align:center;">
		<table cellpadding="1" cellspacing="1" style="height:70px;" width="100%"><tr><td valign="middle" align="center">
		<b><?=$total_invoice_inwords;?></b>
		</td></tr></table>
	</div>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
&nbsp;
<?=$f->input("print","Print","type='button' onclick=\"window.open('invoice_print.php?id=".$_GET["id"]."','_blank');\"");?>
