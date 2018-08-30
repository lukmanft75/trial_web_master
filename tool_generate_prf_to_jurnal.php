<?php
	include_once "common.php";
	if($__user_id != 1) exit();
	exit();//sudah tidak dipakai tools ini
	$prfs = $db->fetch_all_data("prf",[],"paid_by <> '' AND paid_by IS NOT NULL");
	foreach($prfs as $prf){
		$db->addtable("transactions");	$db->where("description","%{prf_id:".$prf["id"]."%","s","LIKE");$db->delete_();
		$db->addtable("jurnals");		$db->where("description","%{prf_id:".$prf["id"]."%","s","LIKE");$db->delete_();
		$db->addtable("jurnal_details");$db->where("description","%{prf_id:".$prf["id"]."%","s","LIKE");$db->delete_();
		
		$prf_maker_by = $prf["maker_by"];
		$prf_maker_at = $prf["maker_at"];
		$prf_created_ip = $prf["created_ip"];
		$prf_purpose = $prf["purpose"];
		$debit = $prf["nominal"] - $prf["deduct_nominal"];
		if(substr($prf["code"],3,1) != " ") $prf["code"] = substr($prf["code"],0,3)." ".substr($prf["code"],3,3);
		$bank_id = $db->fetch_single_data("banks","id",["code"=>$prf["code"]]);
		$description = "PRF Payment {prf_id:".$prf["id"]."} Requested By $prf_maker_by : $prf_purpose";
		$db->addtable("transactions");
		$db->addfield("trx_date");		$db->addvalue($prf_maker_at);
		$db->addfield("description");	$db->addvalue($description);
		$db->addfield("currency_id");	$db->addvalue("IDR");
		$db->addfield("debit");			$db->addvalue($debit);
		$db->addfield("bank_id");		$db->addvalue($bank_id);
		$db->addfield("created_at"); 	$db->addvalue($prf_maker_at);
		$db->addfield("created_by"); 	$db->addvalue($prf_maker_by);
		$db->addfield("created_ip"); 	$db->addvalue($prf_created_ip);
		$db->addfield("updated_at"); 	$db->addvalue($prf_maker_at);
		$db->addfield("updated_by"); 	$db->addvalue($prf_maker_by);
		$db->addfield("updated_ip"); 	$db->addvalue($prf_created_ip);
		$inserting = $db->insert();
		if($inserting["affected_rows"] > 0){
			$transaction_id = $inserting["insert_id"];
			$db->addtable("jurnals");
			$db->addfield("tanggal");			$db->addvalue($prf_maker_at);
			$db->addfield("transaction_id");	$db->addvalue($transaction_id);
			$db->addfield("description");		$db->addvalue($description);
			$db->addfield("currency_id");		$db->addvalue("IDR");
			$db->addfield("bank_id");			$db->addvalue($bank_id);
			$db->addfield("status");			$db->addvalue(1);
			$db->addfield("created_at");		$db->addvalue($prf_maker_at);
			$db->addfield("created_by");		$db->addvalue($prf_maker_by);
			$db->addfield("created_ip");		$db->addvalue($prf_created_ip);
			$db->addfield("updated_at");		$db->addvalue($prf_maker_at);
			$db->addfield("updated_by");		$db->addvalue($prf_maker_by);
			$db->addfield("updated_ip");		$db->addvalue($prf_created_ip);
			$inserting = $db->insert();
			if($inserting["affected_rows"] > 0){
				$jurnal_id = $inserting["insert_id"];
				$coaDebit = $db->fetch_single_data("prf","coa",["id"=>$prf["id"]]);
				$coaKredit = $db->fetch_single_data("banks","coa",["id" => $bank_id]);
				$db->addtable("jurnal_details");
				$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
				$db->addfield("coa");			$db->addvalue($coaDebit);
				$db->addfield("description");	$db->addvalue($description);
				$db->addfield("debit");			$db->addvalue($debit);
				$db->insert();
				$db->addtable("jurnal_details");
				$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
				$db->addfield("coa");			$db->addvalue($coaKredit);
				$db->addfield("description");	$db->addvalue($description);
				$db->addfield("kredit");		$db->addvalue($debit);
				$db->insert();
			}
		}
	}
?>
