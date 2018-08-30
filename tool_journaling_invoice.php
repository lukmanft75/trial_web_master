<?php
	include_once "common.php";
	if($__user_id != 1) exit();
	// exit();//sudah tidak dipakai tools ini
	if($_GET["ids"]!=""){
		$invoices = $db->fetch_all_data("invoice",[],"id IN (".$_GET["ids"].")");
	} else if($_GET["date1"] != "" && $_GET["date2"] != ""){
		$invoices = $db->fetch_all_data("invoice",[],"DATE(issue_at) BETWEEN '".$_GET["date1"]."' AND '".$_GET["date2"]."'");
	} else if($_GET["date1"] != ""){
		$invoices = $db->fetch_all_data("invoice",[],"DATE(issue_at) = '".$_GET["date1"]."'");
	} else {
		$invoices = $db->fetch_all_data("invoice",[],"updated_at >= date(NOW())");
	}
	$total_ar = 0;
	$total_payment = 0;
	foreach($invoices as $invoice){
		echo $invoice["id"];
		
		//remove jurnals & jurnal_details yg A/R maupun Payment
		/* if($_GET["overwrite_all"] == 1){
			$jurnals = $db->fetch_all_data("jurnals",["id"],"invoice_num = '".$invoice["num"]."'");
			foreach($jurnals as $jurnal){
				$db->addtable("jurnals");		$db->where("id" => $jurnal["id"]);			$db->delete_();
				$db->addtable("jurnal_details");$db->where("jurnal_id" => $jurnal["id"]);	$db->delete_();
			}
		} */
		
		$reimbursement = 0;
		$fee = 0;
		$invoce_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$invoice["id"]."'");
		foreach($invoce_details as $invoce_detail){
			$reimbursement += $invoce_detail["reimbursement"];
			$fee += $invoce_detail["fee"];
		}
		
		$jurnal_id = $db->fetch_all_data("jurnals",["id"],"invoice_num = '".$invoice["num"]."' AND description LIKE 'Account Receivable Invoice No:%'")[0]["id"];
		//// if($jurnal_id <= 0){ //belum ada jurnal
			//insert jurnals & jurnal_details invoice create
			$description = "Account Receivable Invoice No: ".$invoice["num"]." -- ".$invoice["description"];
			$db->addtable("jurnals");
			$db->addfield("tanggal");		$db->addvalue(substr($invoice["issue_at"],0,10));
			$db->addfield("invoice_id");	$db->addvalue($invoice["id"]);
			$db->addfield("invoice_num");	$db->addvalue($invoice["num"]);
			$db->addfield("description");	$db->addvalue($description);
			$db->addfield("status");		$db->addvalue("1");
			$db->addfield("isapproved");	$db->addvalue("1");
			$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("created_by");	$db->addvalue($__username);
			$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");	$db->addvalue($__username);
			$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$jurnal_insert = $db->insert();
			if($jurnal_insert["affected_rows"] >= 0){
				$jurnal_id = $jurnal_insert["insert_id"];
				echo " ==> A/R:".$jurnal_id;
				$total_ar++;
				if($fee !=0){//dengan reimbursement ataupun tidak
					$vat = 0;
					$tax23 = 0;
					if($invoice["vat"] != 0) $vat = ($reimbursement + $fee)/10;
					if($invoice["tax23"] != 0) $tax23 = ($reimbursement + $fee)/50;
					
					$nominal = $reimbursement + $fee + $vat - $tax23;
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue($__coa["Piutang Usaha"]);
					$db->addfield("description");	$db->addvalue($description);
					$db->addfield("debit");			$db->addvalue($nominal);
					$db->insert();
					
					$nominal = $reimbursement + $fee;
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue("");
					$db->addfield("description");	$db->addvalue($description);
					$db->addfield("kredit");		$db->addvalue($nominal);
					$db->insert();
					
					if($invoice["vat"] != 0) {
						$nominal = ($reimbursement + $fee)/10;
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue($__coa["Hutang PPN"]);
						$db->addfield("description");	$db->addvalue($description);
						$db->addfield("kredit");		$db->addvalue($nominal);
						$db->insert();
					}
					
					if($invoice["tax23"] != 0){
						$nominal = ($reimbursement + $fee)/50;
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue($__coa["BDD PPh23"]);
						$db->addfield("description");	$db->addvalue($description);
						$db->addfield("debit");			$db->addvalue($nominal);
						$db->insert();
					}
				} else if($reimbursement != 0 && $fee == 0){//reimbursement saja
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue("");
					$db->addfield("description");	$db->addvalue($description);
					$db->addfield("debit");			$db->addvalue($reimbursement);
					$db->insert();
					
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue("");
					$db->addfield("description");	$db->addvalue($description);
					$db->addfield("kredit");		$db->addvalue($reimbursement);
					$db->insert();
				}
			}
		//// }		
		
		if($invoice["invoice_status_id"] == "1"){ //PAID
			$jurnal_id = $db->fetch_all_data("jurnals",["id"],"invoice_num = '".$invoice["num"]."' AND description LIKE 'Payment Invoice No:%'")[0]["id"];
			//// if($jurnal_id <= 0){ //belum ada jurnal
				//insert jurnals & jurnal_details invoice payment
				$nominal = 0;
				$invoce_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$invoice["id"]."'");
				foreach($invoce_details as $invoce_detail){
					$nominal += $invoce_detail["reimbursement"] + $invoce_detail["fee"];
				}
				$invoiceDescription = $db->fetch_single_data("invoice","description",["id" => $invoice["id"]]);
				$nominal += $invoice["vat"] + $invoice["tax23"];
				$description = "Payment Invoice No: ".$invoice["num"]." -- ".$invoiceDescription;
				$ar_jurnal_id = $db->fetch_single_data("jurnals","id",["invoice_id" => $invoice["id"],"description" => "Account Receivable Invoice No%:LIKE"]);
				$coaKredit = $db->fetch_single_data("jurnal_details","coa",["jurnal_id"=>$ar_jurnal_id,"debit"=>"0:<>","coa"=>"(SELECT coa FROM coa WHERE description LIKE 'piutang%'):IN"]);
				$db->addtable("jurnals");
				$db->addfield("tanggal");		$db->addvalue(substr($invoice["paid_at"],0,10));
				$db->addfield("invoice_id");	$db->addvalue($invoice["id"]);
				$db->addfield("invoice_num");	$db->addvalue($invoice["num"]);
				$db->addfield("description");	$db->addvalue($description);
				$db->addfield("status");		$db->addvalue("1");
				$db->addfield("isapproved");	$db->addvalue("1");
				$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("created_by");	$db->addvalue($__username);
				$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");	$db->addvalue($__username);
				$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$jurnal_insert = $db->insert();
				if($jurnal_insert["affected_rows"] >= 0){
					$jurnal_id = $jurnal_insert["insert_id"];
					echo " ==> Payment:".$jurnal_id;
					$total_payment++;
					$payment_pph23 = 0;
					$payment_titipan = 0;
					if($nominal > $invoice["receive"]) $payment_pph23 = $nominal - $invoice["receive"];
					if($nominal < $invoice["receive"]) $payment_titipan = $invoice["receive"] - $nominal;
					
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue("");
					$db->addfield("description");	$db->addvalue($description);
					$db->addfield("debit");			$db->addvalue($nominal - $payment_pph23 - $payment_titipan);
					$db->insert();
					
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue($coaKredit);
					$db->addfield("description");	$db->addvalue($description);
					$db->addfield("kredit");		$db->addvalue($nominal);
					$db->insert();
					
					if($payment_pph23 > 0){
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue($__coa["BDD PPh23"]);
						$db->addfield("description");	$db->addvalue($description);
						$db->addfield("debit");			$db->addvalue($payment_pph23);
						$db->insert();
					}	

					if($payment_titipan > 0){
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue("");
						$db->addfield("description");	$db->addvalue("Titipan - ".$description);
						$db->addfield("debit");			$db->addvalue($payment_titipan);
						$db->insert();
					}
				}
			//// }
		}
		echo "<br>";
	}
	echo "<br>Total A/R : ".$total_ar;
	echo "<br>Total Payment : ".$total_payment;
?>
