<?php if($_POST["export"]){
		$_exportname = "GeneralLedger.xls";
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$_exportname);
		header("Pragma: no-cache");
		header("Expires: 0");
		$_GET["do_filter"]="Load";
		$_isexport = true;
		$_tableattr = "border='1'";
		$titleColspan = "colspan='6' ";
	}
	include_once "head.php";
?>
<div class="bo_title">GENERAL LEDGER</div>
<?php 
	if(!isset($_POST["load"]) && !isset($_POST["export"])){
		echo $f->start("","POST","finance_report_general_ledger_list.php","target='_blank'");
			echo $t->start("","editor_content");
				echo $t->row(["COA",$f->select("coa",$db->fetch_select_data("coa","coa","concat(coa,' -- ',description)",[],"coa","",true))]);
				echo $t->row(["Periode",$f->input("periode_1",date("Y-01-01"),"type='date'")." - ".$f->input("periode_2",date("Y-12-31"),"type='date'")]);
				echo $t->row(["Show Childs",$f->input("show_childs","1","type='checkbox' checked")]);
			echo $t->end();
			echo $f->input("load","Load","type='submit'");
			echo "&nbsp;&nbsp;&nbsp;";
			echo $f->input("export","Export","type='submit'");
		echo $f->end();
	} else {
			$periode_1 = $_POST["periode_1"];
			$periode_2 = $_POST["periode_2"];
			$coa = $_POST["coa"];
			$show_childs = $_POST["show_childs"];
		?>
		<table width="100%"><tr><td align="center" <?=$titleColspan;?>><b>PT. INDO HUMAN RESOURCE</b></td></tr></table>
		<table width="100%"><tr><td align="center" <?=$titleColspan;?>><b>GENERAL LEDGER</b></td></tr></table>
		<?php if($coa != ""){ ?>
		<table width="100%"><tr><td align="center" <?=$titleColspan;?>><b><?=$coa;?> -- <?=$db->fetch_single_data("coa","description",["coa" => $coa]);?></b></td></tr></table>
		<?php } ?>
		<table width="100%"><tr><td align="center" <?=$titleColspan;?>>Periode : <?=format_tanggal($periode_1,"dMY")?> - <?=format_tanggal($periode_2,"dMY")?></td></tr></table>
		<br>
		<?=$t->start($_tableattr,"data_content");?>
			<?=$t->header(["No","Tanggal","COA","Description","Debit","Credit"],["valign='top' width='1%'","valign='top' width='10%'",""]);?>
			<?php
				$no=0;
				if($coa != ""){
					if($show_childs){
						$addWhere = "AND id IN (SELECT jurnal_id FROM jurnal_details WHERE coa='$coa' OR coa IN (SELECT coa FROM coa WHERE parent='$coa'))";
						$addWhere2 = "AND (coa='$coa' OR coa IN (SELECT coa FROM coa WHERE parent='$coa'))";
					} else {
						$addWhere = "AND id IN (SELECT jurnal_id FROM jurnal_details WHERE coa='$coa')";
						$addWhere2 = "AND coa='$coa'";
					}
				} else {
					$addWhere = "";
					$addWhere2 = "";
				}
				
				$totalDebit = 0;
				$totalKredit = 0;
				$jurnals = $db->fetch_all_data("jurnals",[],"tanggal between '$periode_1' AND '$periode_2' $addWhere","tanggal");
				foreach($jurnals as $jurnal){
					$jurnalDetails = $db->fetch_all_data("jurnal_details",[],"jurnal_id='".$jurnal["id"]."' $addWhere2","id");
					foreach($jurnalDetails as $jurnalDetail){
						$no++;
						$tanggal = format_tanggal($jurnal["tanggal"]);
						$coa = $jurnalDetail["coa"]." -- ".$db->fetch_single_data("coa","description",["coa" => $jurnalDetail["coa"]]);
						$description = $jurnalDetail["description"];
						if($jurnalDetail["debit"] != 0) $debit = format_amount($jurnalDetail["debit"]);
						else $debit = "";
						if($jurnalDetail["kredit"] != 0) $kredit = format_amount($jurnalDetail["kredit"]);
						else $kredit = "";
						$totalDebit += $jurnalDetail["debit"];
						$totalKredit += $jurnalDetail["kredit"];
						$row_attr = "title=\"klik untuk melihat detail journal\" style=\"cursor:pointer;\" onclick=\"window.open('jurnals_edit.php?id=".$jurnalDetail["jurnal_id"]."');\"";
						
						echo $t->row([$no,$tanggal,$coa,$description,$debit,$kredit],["valign='top' align='right'","valign='top' nowrap","valign='top'","valign='top'","valign='top' align='right'","valign='top' align='right'"],$row_attr);
					}
				}
				echo $t->row(["<b>TOTAL</b>","<b>".format_amount($totalDebit)."</b>","<b>".format_amount($totalKredit)."</b>"],["colspan='4' align='center'","align='right'","align='right'"]);
			?>
		<?=$t->end();?>
		<?php
	}
?>
<?php include_once "footer.php";?>