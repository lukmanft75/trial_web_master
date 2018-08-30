<?php
	include_once "../common.php"; 
	
	if(isset($_GET["mode"])){ $_mode = $_GET["mode"]; } else { $_mode = ""; }
	if(isset($_GET["id"])){ $id = $_GET["id"]; } else { $id = ""; }
	if(isset($_GET["efficient_date"])){ $efficient_date = $_GET["efficient_date"]; } else { $efficient_date = ""; }
	
	if(isset($_POST["post_data"])){ parse_str($_POST["post_data"],$_POST); } 
	$remarks = chr(13).chr(10)."Adjustment (eff. ".format_tanggal($efficient_date,"d F Y").") :";
	
	$current = $db->fetch_single_data("joborder","thp",array("id" => $id));
	// echo $_POST["thp"]." :: ".$thp;
	if($_POST["thp"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- THP : prev. ".format_amount($current);
		}
	}
	
	$current = $db->fetch_single_data("joborder","basic_salary",array("id" => $id));
	if($_POST["basic_salary"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Basic Salary : prev. ".format_amount($current);
		}
	}
	
	$current = $db->fetch_single_data("joborder","tax_paid_company",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["tax_paid_company"] == "") $_POST["tax_paid_company"] = 0;
	if($_POST["tax_paid_company"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$current = ($current == "1") ? "Yes":"No";
			$remarks .= chr(13).chr(10)."- Tax Paid Company : prev. ".$current;
		}
	}
	
	foreach($_POST["allowance_price"] as $key => $value){
		$current = $db->fetch_single_data("joborder_allowances","price",array("joborder_id" => $id,"allowance_id" => $_POST["allowances"][$key]));
		$current_tax_paid_company = $db->fetch_single_data("joborder_allowances","tax_paid_company",array("joborder_id" => $id,"allowance_id" => $_POST["allowances"][$key]));
		$value_tax_paid_company = $_POST["tax_paid_company1"][$key];
		if($value_tax_paid_company == "") $value_tax_paid_company = 0;
		if($value != $current || $value_tax_paid_company != $current_tax_paid_company){
			if($_mode == "is_any_changed"){ echo "1"; exit(); }
			if($_mode == "get_remarks"){
				$allowance_name = $db->fetch_single_data("allowances","name",array("id" => $_POST["allowances"][$key]));
				$previous = "";
				if($value != $current) $previous .= format_amount($current)." ";
				if($value_tax_paid_company != $current_tax_paid_company) $previous .= ($current_tax_paid_company != 1) ? "Tax Not Paid By Company" : "Tax Paid By Company"; 
					
				$remarks .= chr(13).chr(10)."- Allowance ".$allowance_name." : prev. ".$previous;
			}
		}
	}
	
	$current = $db->fetch_single_data("joborder","overtime",array("id" => $id));
	if($_POST["overtime"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			if($current == "1") $current = "Follow DEPNAKER";
			if($current == "2") $current = "No OT";
			if($current == "3") $current = "Fix Rate";
			$remarks .= chr(13).chr(10)."- Overtime : prev. ".$current;
		}
	}
	
	foreach($_POST["benefit_percentage"] as $key => $value){
		$current = $db->fetch_single_data("joborder_benefits","percentage",array("joborder_id" => $id,"benefits_id" => $_POST["benefits"][$key]));
		$current_price = $db->fetch_single_data("joborder_benefits","price",array("joborder_id" => $id,"benefits_id" => $_POST["benefits"][$key]));
		$value_price = $_POST["benefit_price"][$key];
		if($value_price == "") $value_price = 0;
		if($value != $current || $value_price != $current_price){
			if($_mode == "is_any_changed"){ echo "1"; exit(); }
			if($_mode == "get_remarks"){
				$benefit_name = $db->fetch_single_data("benefits","name",array("id" => $_POST["benefits"][$key]));
				$previous = "";
				if($value != $current) $previous .= format_amount($current)."% ";
				if($value_price != $current_price) $previous .= "Rp.".format_amount($current_price);
					
				$remarks .= chr(13).chr(10)."- Benefit ".$benefit_name." : prev. ".$previous;
			}
		}
	}
	
	$current = $db->fetch_single_data("joborder","insurace_plan_id",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["insurace_plan_id"] == "") $_POST["insurace_plan_id"] = 0;
	if($_POST["insurace_plan_id"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$current = $db->fetch_single_data("insurance_plan","concat(insurance_corp,' - ',plan)",array("id" => $current));
			if($current == "") $current = "Not Covered";
			$remarks .= chr(13).chr(10)."- Insurace Plan : prev. ".$current;
		}
	}
	
	$current = $db->fetch_single_data("joborder","insurace_family_cover",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["insurace_family_cover"] == "") $_POST["insurace_family_cover"] = 0;
	if($_POST["insurace_family_cover"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$current = ($current == "1") ? "Employee Only":" Cover Family";
			$remarks .= chr(13).chr(10)."- Insurace Family Cover : prev. ".$current;
		}
	}
	
	if(!$_POST["insurance_is_inpatient"]) $_POST["inpatient_ammount"] = 0;
	$current = $db->fetch_single_data("joborder","inpatient_ammount",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["inpatient_ammount"] == "") $_POST["inpatient_ammount"] = 0;
	if($_POST["inpatient_ammount"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Inpatient : prev. ".format_amount($current);
		}
	}
	
	if(!$_POST["insurance_is_outpatient"]) $_POST["outpatient_ammount"] = 0;
	$current = $db->fetch_single_data("joborder","outpatient_ammount",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["outpatient_ammount"] == "") $_POST["outpatient_ammount"] = 0;
	if($_POST["outpatient_ammount"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Outpatient : prev. ".format_amount($current);
		}
	}
	
	if(!$_POST["insurance_is_dental"]) $_POST["dental_ammount"] = 0;
	$current = $db->fetch_single_data("joborder","dental_ammount",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["dental_ammount"] == "") $_POST["dental_ammount"] = 0;
	if($_POST["dental_ammount"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Dental : prev. ".format_amount($current);
		}
	}
	
	if(!$_POST["insurance_is_maternity"]){
		$_POST["maternity_normal"] = 0;
		$_POST["maternity_cecar"] = 0;
		$_POST["maternity_miscarriage"] = 0;
	}
	$current = $db->fetch_single_data("joborder","maternity_normal",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["maternity_normal"] == "") $_POST["maternity_normal"] = 0;
	if($_POST["maternity_normal"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Maternity Normal : prev. ".format_amount($current);
		}
	}
	
	$current = $db->fetch_single_data("joborder","maternity_cecar",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["maternity_cecar"] == "") $_POST["maternity_cecar"] = 0;
	if($_POST["maternity_cecar"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Maternity Cecar : prev. ".format_amount($current);
		}
	}
	
	$current = $db->fetch_single_data("joborder","maternity_miscarriage",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["maternity_miscarriage"] == "") $_POST["maternity_miscarriage"] = 0;
	if($_POST["maternity_miscarriage"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Maternity Miscarriage : prev. ".format_amount($current);
		}
	}
	
	if(!$_POST["insurance_is_glasses"]) $_POST["glasses_ammount"] = 0;
	$current = $db->fetch_single_data("joborder","glasses_ammount",array("id" => $id));
	if($current == "") $current = 0;
	if($_POST["glasses_ammount"] == "") $_POST["glasses_ammount"] = 0;
	if($_POST["glasses_ammount"] != $current){
		if($_mode == "is_any_changed"){ echo "1"; exit(); }
		if($_mode == "get_remarks"){
			$remarks .= chr(13).chr(10)."- Glasses : prev. ".format_amount($current);
		}
	}
	
	if($_mode == "get_remarks") echo $remarks;
?>