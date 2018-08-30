<?php
	include_once "../common.php"; 
	if(isset($_GET["mode"])){ $_mode = $_GET["mode"]; } else { $_mode = ""; }
	if(isset($_GET["invoices"])){ $invoices = $_GET["invoices"]; } else { $invoices = ""; }
	if($_mode == "go_paid"){
		$totalTax23 = 0;
		$invoices = explode(";",$invoices);
		echo "<br><table class='table'>";
		echo "<thead><tr><th><b>Invoice Number</b></th><th><b>Outstanding</b></th><th><b>PPH 23</b></th><th><b>Titipan</b></th></tr></thead><tbody>";
		$xx = -1;
		foreach($invoices as $invoice_id){
			if($invoice_id > 0){
				$xx++;
				$nominal = 0;
				$invoce_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$invoice_id."'");
				foreach($invoce_details as $invoce_detail){
					$nominal += $invoce_detail["reimbursement"] + $invoce_detail["fee"];
				}
				$vat = $db->fetch_single_data("invoice","vat",["id" => $invoice_id]);
				$tax23 = $db->fetch_single_data("invoice","tax23",["id" => $invoice_id]);
				$invoice_num = $db->fetch_single_data("invoice","num",["id" => $invoice_id]);
				$nominal += $vat + $tax23;
				$totalTax23 += $tax23;
				echo "<tr>";
				echo "<td nowrap>".$invoice_num."</td>";
				echo "<td align='right'>".format_amount($nominal)."</td>";
				echo "<td><input id='pph23[".$invoice_id."]' name='pph23[".$invoice_id."]' value='0' size='10'></td>";
				echo "<td><input name='titipan[".$invoice_id."]' value='0' size='10'></td>";
				echo "</tr>";
			}
		}
		echo "</tbody></table>";
		if($totalTax23 == 0){
			foreach($invoices as $invoice_id){
				if($invoice_id > 0){
					$nominal = 0;
					$invoce_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$invoice_id."'");
					foreach($invoce_details as $invoce_detail){
						$nominal += $invoce_detail["reimbursement"] + $invoce_detail["fee"];
					}
					$tax23 = $nominal * -2/100;
					?> <script> document.getElementById("pph23[<?=$invoice_id;?>]").value = "<?=$tax23;?>"; </script> <?php
				}
			}
		} else {
			foreach($invoices as $invoice_id){
				if($invoice_id > 0){
					$tax23 = $db->fetch_single_data("invoice","tax23",["id" => $invoice_id]);
					?> <script> document.getElementById("pph23[<?=$invoice_id;?>]").value = "<?=$tax23;?>"; </script> <?php
				}
			}
		}
	}
?>