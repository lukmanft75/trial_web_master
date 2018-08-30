<?php
	exit();
	include_once "common.php";
	$sql = "";
	$invoices = $db->fetch_all_data("invoice",[],"num LIKE '%/18' ORDER BY num");
	$id = 6560;
	foreach($invoices as $invoice){
		$id++;
		$sql .= "INSERT INTO invoice (id, num, issue_at, due_date, division_id, client_id, currency_id, description, additional_detail, billing_periode, po_no, vat, reimbursement, fee, total_po, tax23, total, inwords, invoice_status_id, receive, paid_at, paid_bank_coa, print_config, created_at, created_by, created_ip, updated_at, updated_by, updated_ip, xtimestamp) VALUES ";
		$sql .= "('".$id."', '".$invoice["num"]."', '".$invoice["issue_at"]."', ".$invoice["due_date"].", ".$invoice["division_id"].", ".$invoice["client_id"].", '".$invoice["currency_id"]."', '".$invoice["description"]."', '".$invoice["additional_detail"]."', '".$invoice["billing_periode"]."', '".$invoice["po_no"]."', ".$invoice["vat"].", ".$invoice["reimbursement"].", ".$invoice["fee"].", ".$invoice["total_po"].", ".$invoice["tax23"].", ".$invoice["total"].", '".$invoice["inwords"]."', ".$invoice["invoice_status_id"].", ".$invoice["receive"].", '".$invoice["paid_at"]."', '".$invoice["paid_bank_coa"]."', '".$invoice["print_config"]."', '".$invoice["created_at"]."', '".$invoice["created_by"]."', '".$invoice["created_ip"]."', '".$invoice["updated_at"]."', '".$invoice["updated_by"]."', '".$invoice["updated_ip"]."', '".$invoice["xtimestamp"]."');<br>";
		$invoice_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$invoice["id"]."'");
		foreach($invoice_details as $invoice_detail){
			$sql .= "INSERT INTO invoice_detail (invoice_id, invoice_num, po_id, po_num, description, currency_id, reimbursement, fee) VALUES ";
			$sql .= "('".$id."', '".$invoice_detail["invoice_num"]."', ".$invoice_detail["po_id"].", '".$invoice_detail["po_num"]."', '".$invoice_detail["description"]."', '".$invoice_detail["currency_id"]."', ".$invoice_detail["reimbursement"].", ".$invoice_detail["fee"].");<br>";
		}
		$sql .= "<br>";
	}
	echo $sql;
?>
