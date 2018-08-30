<?php include_once "head.php";?>
<?php
	$template_id = $_POST["template_id"];
	$periode_1 = $_POST["periode_1"];
	$periode_2 = $_POST["periode_2"];
	$periode_count = $_POST["periode_count"];
	$periode_loop = $_POST["periode_loop"];
	$templates_0 = $db->fetch_all_data("finance_report_template_detail",[],"finance_report_template_id = '".$template_id."' AND sayap='0'","seqno");
	$templates_1 = $db->fetch_all_data("finance_report_template_detail",[],"finance_report_template_id = '".$template_id."' AND sayap='1'","seqno");
	if(count($templates_0) > count($templates_1)){ $maxrow = count($templates_0); } else {$maxrow = count($templates_1);}
?>
<table width="100%"><tr><td align="center"><b>PT. INDO HUMAN RESOURCE</b></td></tr></table>
<table width="100%"><tr><td align="center"><b><?=$db->fetch_single_data("finance_report_template","name",["id"=>$template_id]);?></b></td></tr></table>
<table width="100%"><tr><td align="center">Periode : <?=format_tanggal($periode_1,"dMY")?> - <?=format_tanggal($periode_2,"dMY")?></b></td></tr></table>
<br>
<?=$t->start("","data_content");?>
	<?=$t->row([]);?>
	<?php 
		for($seqno = 0;$seqno < $maxrow;$seqno++){ 
			$caption_0 = $templates_0[$seqno]["caption"];
			$coa = $templates_0[$seqno]["coa"];
			$formula = $templates_0[$seqno]["formula"];
			
			if($caption_0 != ""){
				$caption_0 = "<b>".$caption_0."</b>";
			} else {
				$caption_0 = "&nbsp;&nbsp;&nbsp;&nbsp;".$coa."&nbsp;&nbsp;&nbsp;&nbsp;".$db->fetch_single_data("coa","description",["coa" => $coa]);
			}
			
			if($coa != ""){
				$coa_child = str_replace("||","','",sel_to_pipe($db->fetch_select_data("coa","description","coa",["parent" => $coa])));
				$coa_child = str_replace("|","'",$coa_child);
				$amount = $db->fetch_single_data("jurnal_details","concat(sum(debit-kredit)) as amount",["coa" => $coa_child.":IN","jurnal_id" => "SELECT id FROM jurnals WHERE tanggal BETWEEN '".$periode_1."' AND '".$periode_2."':IN"]);
				eval("$formula = \"$amount\";");
				$amount = format_amount($amount);
			} else {
				eval($formula.";");
				$formula = trim(explode("=",$formula)[0]);
				eval("\$amount = \"$formula\";");
				$amount = "<b>".format_amount($amount)."</b>";
			}
			
			$row_contents = [$caption_0,"&nbsp;&nbsp;&nbsp;",$amount];
			$row_attr = ["nowrap width='20%'","nowrap width='2%'","align='right' width='27%'"];
			
			if(count($templates_1) > 1){
				$caption_1 = $templates_1[$seqno]["caption"];
				$coa = $templates_1[$seqno]["coa"];
				$formula = $templates_1[$seqno]["formula"];
				
				if($caption_1 != ""){
					$caption_1 = "<b>".$caption_1."</b>";
				} else {
					$caption_1 = "&nbsp;&nbsp;&nbsp;&nbsp;".$templates_1[$seqno]["coa"]."&nbsp;&nbsp;&nbsp;&nbsp;".$db->fetch_single_data("coa","description",["coa" => $templates_1[$seqno]["coa"]]);
				}
				
				if($coa != ""){
					$coa_child = str_replace("||","','",sel_to_pipe($db->fetch_select_data("coa","description","coa",["parent" => $coa])));
					$coa_child = str_replace("|","'",$coa_child);
					$amount = $db->fetch_single_data("jurnal_details","concat(sum(debit-kredit)) as amount",["coa" => $coa_child.":IN","jurnal_id" => "SELECT id FROM jurnals WHERE tanggal BETWEEN '".$periode_1."' AND '".$periode_2."':IN"]);
					eval("$formula = \"$amount\";");
					$amount = format_amount($amount);
				}else{
					eval($formula.";");
					$formula = trim(explode("=",$formula)[0]);
					eval("\$amount = \"$formula\";");
					$amount = "<b>".format_amount($amount)."</b>";
				}
				
				array_push($row_contents,"&nbsp;&nbsp;&nbsp;",$caption_1,"&nbsp;&nbsp;&nbsp;",$amount);
				array_push($row_attr,"nowrap width='2%'","nowrap width='20%'","nowrap width='2%'","align='right' width='27%'");
			}
			echo $t->row($row_contents,$row_attr);
		} 
	?>
<?=$t->end();?>
<?php include_once "footer.php";?>