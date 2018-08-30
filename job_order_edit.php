<?php include_once "head.php";?>
<div class="bo_title" id="bo_title">Edit Job Order</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("joborder");				$db->where("id",@$_GET["id"]);
		if ($_POST["pkwt_for"] == "999") 		$_POST["pkwt_for"] = "";
		$db->addfield("pkwt_for");				$db->addvalue(@$_POST["pkwt_for"]);
		$db->addfield("client_id");				$db->addvalue(@$_POST["client_id"]);
		$db->addfield("position_id");			$db->addvalue(@$_POST["position_id"]);
		$db->addfield("report_to");				$db->addvalue(@$_POST["report_to"]);
		$db->addfield("candidate_id");			$db->addvalue(@$_POST["candidate_id"]);
		$db->addfield("join_start");			$db->addvalue(@$_POST["join_start"]);
		$db->addfield("join_end");				$db->addvalue(@$_POST["join_end"]);
		$db->addfield("status_category_id");	$db->addvalue(@$_POST["status_category_id"]);
		$db->addfield("reason_of_hiring");		$db->addvalue(@$_POST["reason_of_hiring"]);
		$db->addfield("w_hours_start");		    $db->addvalue(@$_POST["w_hours_start"]);
		$db->addfield("w_hours_end");		    $db->addvalue(@$_POST["w_hours_end"]);
		$db->addfield("remarks");		        $db->addvalue(@$_POST["remarks"]);
		$db->addfield("thp");	       			$db->addvalue(@$_POST["thp"]);
		$db->addfield("basic_salary");	       	$db->addvalue(@$_POST["basic_salary"]);
		$db->addfield("overtime");		        $db->addvalue(@$_POST["overtime"]);
		if(@$_POST["insurace_plan_id"] > 0) 	$_POST["asuransi"] = 1;
		$db->addfield("asuransi");		        $db->addvalue(@$_POST["asuransi"]);
		$db->addfield("contract_status");		$db->addvalue(@$_POST["contract_status"]);
		$db->addfield("fixed_allowance");		$db->addvalue(@$_POST["fixed_allowance"]);
		$db->addfield("tax_paid_company");		$db->addvalue(@$_POST["tax_paid_company"]);
		$db->addfield("insurace_plan_id");		$db->addvalue(@$_POST["insurace_plan_id"]);
		$db->addfield("insurace_family_cover");	$db->addvalue(@$_POST["insurace_family_cover"]);
		$db->addfield("insurance_is_inpatient");$db->addvalue(@$_POST["insurance_is_inpatient"]);
		$db->addfield("insurance_is_outpatient");$db->addvalue(@$_POST["insurance_is_outpatient"] + @$_POST["insurance_is_outpatient_allin"]);
		$db->addfield("insurance_is_dental");	$db->addvalue(@$_POST["insurance_is_dental"]);
		$db->addfield("insurance_is_maternity");$db->addvalue(@$_POST["insurance_is_maternity"]);
		$db->addfield("insurance_is_glasses");	$db->addvalue(@$_POST["insurance_is_glasses"]);
		$db->addfield("inpatient_ammount");		$db->addvalue(@$_POST["inpatient_ammount"]);
		$db->addfield("outpatient_ammount");	$db->addvalue(@$_POST["outpatient_ammount"]);
		$db->addfield("dental_ammount");		$db->addvalue(@$_POST["dental_ammount"]);
		$db->addfield("maternity_normal");		$db->addvalue(@$_POST["maternity_normal"]);
		$db->addfield("maternity_cecar");		$db->addvalue(@$_POST["maternity_cecar"]);
		$db->addfield("maternity_miscarriage");	$db->addvalue(@$_POST["maternity_miscarriage"]);
		$db->addfield("glasses_ammount");		$db->addvalue(@$_POST["glasses_ammount"]);
		$db->addfield("lebaran");		        $db->addvalue(@$_POST["lebaran"]);
		$db->addfield("other_benefits");		$db->addvalue(@$_POST["other_benefits"]);
		$db->addfield("approved_at");		    $db->addvalue(@$_POST["approved_at"]);
		$db->addfield("approved_position");		$db->addvalue(@$_POST["approved_position"]);
		$db->addfield("approved_by");		    $db->addvalue(@$_POST["approved_by"]);
		$db->addfield("checked_at");		    $db->addvalue(@$_POST["checked_at"]);
		$db->addfield("checked_position");		$db->addvalue(@$_POST["checked_position"]);
		$db->addfield("checked_by");		    $db->addvalue(@$_POST["checked_by"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
        $updating = $db->update();
		if($updating["affected_rows"] >= 0){
			$db->addtable("joborder_allowances");
			$db->where("joborder_id",@$_GET["id"]);
			$db->delete_();
			foreach($_POST["allowances"] as $key => $allowance_id){
				if($allowance_id > 0){					
					$db->addtable("joborder_allowances");
					$db->addfield("joborder_id");$db->addvalue(@$_GET["id"]);
					$db->addfield("allowance_id");		$db->addvalue($allowance_id);
					$db->addfield("price");				$db->addvalue($_POST["allowance_price"][$key]);
					$db->addfield("tax_paid_company");	$db->addvalue($_POST["tax_paid_company1"][$key]);
					$db->insert();
				}
			}
			
			$db->addtable("joborder_benefits");
			$db->where("joborder_id",@$_GET["id"]);
			$db->delete_();
			foreach($_POST["benefits"] as $key => $benefits_id){
				if($benefits_id > 0){
					$db->addtable("joborder_benefits");
					$db->addfield("joborder_id");	$db->addvalue(@$_GET["id"]);
					$db->addfield("benefits_id");	$db->addvalue($benefits_id);
					$db->addfield("percentage");	$db->addvalue($_POST["benefit_percentage"][$key]);
					$db->addfield("price");			$db->addvalue($_POST["benefit_price"][$key]);
					$db->insert();
				}
			}
			
			javascript("alert('Data Saved');");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("joborder");$db->where("id",$_GET["id"]);$db->limit(1);$jo = $db->fetch_data();
    
    $sel_client = $f->select_window("client_id","Clients",$jo["client_id"],"clients","id","name");
	if ($jo["pkwt_for"] === "") {$jo["pkwt_for"] = "999";}
	$sel_pkwt_for = $f->select("pkwt_for",array("999" => "","0" => "I","1" => "II","2" => "III","break" => "Break"),$jo["pkwt_for"]);
	$sel_position = $f->select_window("position_id","Positions",$jo["position_id"],"positions","id","name");
    $txt_report_to = $f->input("report_to",$jo["report_to"]);
	$sel_candidate = $f->select_window("candidate_id","Candidates",$jo["candidate_id"],"candidates","id","name","win_candidate.php");
    $cal_join_start = $f->input("join_start",$jo["join_start"],"type='date'");
    $cal_join_end = $f->input("join_end",$jo["join_end"],"type='date'");
    $sel_status_category = $f->select("status_category_id",$db->fetch_select_data("status_categories","id","name",array(),array("name")),$jo["status_category_id"]);
    $txt_reason_of_hiring = $f->textarea("reason_of_hiring",$jo["reason_of_hiring"]);
	$w_hours_start = $f->input("w_hours_start",$jo["w_hours_start"],"type='time' style='width:120px;'");
	$w_hours_end = $f->input("w_hours_end",$jo["w_hours_end"],"type='time' style='width:120px;'");
	$w_hours = $w_hours_start." - ".$w_hours_end; 
    $txt_remarks = $f->textarea("remarks",$jo["remarks"],"style='width:600px;height:200px;'");
    $txt_thp = $f->input("thp",$jo["thp"],"type='number'");
    $txt_basic_salary = $f->input("basic_salary",$jo["basic_salary"],"type='number'");
	
	$plusminbutton = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('allowance_rows','row_allowance_');\"")."&nbsp;";
	$plusminbutton .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('allowance_rows','row_allowance_');\"");
	$sel_allowances = $f->select("allowances[0]",$db->fetch_select_data("allowances","id","name",array(),array("id"),"",true));
	$txt_allowances = $f->input("allowance_price[0]","","type='number' step='0.01' style='width:150px;'");
	$sel_tax_paid_company1 = $f->select("tax_paid_company1[0]",array("" => "","1" => "Yes","2" => "No"));
	
	$allowance_rows = $t->start("width='100%'","allowance_rows");
	$allowance_rows .= $t->row(array($plusminbutton."<br>No.","Allowance Name","Value","Tax paid by company"),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"));
	$allowance_rows .= $t->row(array("<div id=\"firstno\">1</div>",$sel_allowances,$txt_allowances,$sel_tax_paid_company1),array("nowrap'"),"id=\"row_allowance_0\"");
	$allowance_rows .= $t->end();
	
	$chk_overtime[1] = ($jo["overtime"] == 1)?"checked" : "";
	$chk_overtime[2] = ($jo["overtime"] == 2)?"checked" : "";
	$chk_overtime[3] = ($jo["overtime"] == 3)?"checked" : "";
	$sel_overtime = $f->input("overtime","1",$chk_overtime[1]." type='radio'")." Follow DEPNAKER";
	$sel_overtime .= $f->input("overtime","2",$chk_overtime[2]." type='radio'")." No OT";
	$sel_overtime .= $f->input("overtime","3",$chk_overtime[3]." type='radio'")." Fix Rate";
	
	$chk_contract_status[1] = ($jo["contract_status"] == 1)?"checked" : "";
	$chk_contract_status[2] = ($jo["contract_status"] == 2)?"checked" : "";
	$sel_contract_status = $f->input("contract_status","1",$chk_contract_status[1]." type='radio'")." Contract (PKWT)";
	$sel_contract_status .= $f->input("contract_status","2",$chk_contract_status[2]." type='radio'")." Permanent (PKWTT)";
	
    $txt_fixed_allowance = $f->input("fixed_allowance",$jo["fixed_allowance"],"type='number'");
	
	$chk_tax_paid_company[1] = ($jo["tax_paid_company"] == 1)?"checked" : "";
	$chk_tax_paid_company[2] = ($jo["tax_paid_company"] == 2)?"checked" : "";
	$sel_tax_paid_company = $f->input("tax_paid_company","1",$chk_tax_paid_company[1]." type='radio'")." Yes";
	$sel_tax_paid_company .= $f->input("tax_paid_company","2",$chk_tax_paid_company[2]." type='radio'")." No";
	
	$plusminbutton = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('benefit_rows','benefit_detail_');\"")."&nbsp;";
	$plusminbutton .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('benefit_rows','benefit_detail_');\"");
	$sel_benefits = $f->select("benefits[0]",$db->fetch_select_data("benefits","id","name",array(),array("id"),"",true));
	$txt_benefits_percentage = $f->input("benefit_percentage[0]","","type='number' step='0.01' style='width:50px;'");
	$txt_benefits = $f->input("benefit_price[0]","","type='number' step='0.01' style='width:150px;'");
	
	$benefit_rows = $t->start("width='100%'","benefit_rows");
	$benefit_rows .= $t->row(array($plusminbutton."<br>No.","benefit Name","Percentage (%)","Price"),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"));
	$benefit_rows .= $t->row(array("<div id=\"firstno\">1</div>",$sel_benefits,$txt_benefits_percentage,$txt_benefits),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"),"id=\"benefit_detail_0\"");
	$benefit_rows .= $t->end();
	
	$insurace_plans = $db->fetch_select_data("insurance_plan","id","concat(insurance_corp,' - ',plan) as name",array(),array("id"));
	$insurace_plans[""] = "-Not Covered-";
	ksort($insurace_plans);
	$sel_insurace_plans = $f->select("insurace_plan_id",$insurace_plans,$jo["insurace_plan_id"]);
	
	$chk_insurace_family_cover[1] = ($jo["insurace_family_cover"] == 1)?"checked" : "";
	$chk_insurace_family_cover[2] = ($jo["insurace_family_cover"] == 2)?"checked" : "";
	$rad_insurace_family_cover = $f->input("insurace_family_cover","1",$chk_insurace_family_cover[1]." type='radio'")." Employee Only";
	$rad_insurace_family_cover .= $f->input("insurace_family_cover","2",$chk_insurace_family_cover[2]." type='radio'")." Cover Family";
	
	$ischk_insurance_is_inpatient = ($jo["insurance_is_inpatient"] == 1)?"checked" : "";
	$ischk_insurance_is_outpatient = ($jo["insurance_is_outpatient"] >= 1)?"checked" : "";
	$ischk_insurance_is_outpatient_allin = ($jo["insurance_is_outpatient"] > 1)?"checked" : "";
	$ischk_insurance_is_dental = ($jo["insurance_is_dental"] == 1)?"checked" : "";
	$ischk_insurance_is_maternity = ($jo["insurance_is_maternity"] == 1)?"checked" : "";
	$ischk_insurance_is_glasses = ($jo["insurance_is_glasses"] == 1)?"checked" : "";
	$chk_insurance_is_inpatient = $f->input("insurance_is_inpatient","1",$ischk_insurance_is_inpatient." type='checkbox'")." Inpatient";
	$chk_insurance_is_outpatient = $f->input("insurance_is_outpatient","1",$ischk_insurance_is_outpatient." type='checkbox'")." Outpatient";
	$chk_insurance_is_outpatient_allin = $f->input("insurance_is_outpatient_allin","1",$ischk_insurance_is_outpatient_allin." type='checkbox'")." All In";
	$chk_insurance_is_dental = $f->input("insurance_is_dental","1",$ischk_insurance_is_dental." type='checkbox'")." Dental";
	$chk_insurance_is_maternity = $f->input("insurance_is_maternity","1",$ischk_insurance_is_maternity." type='checkbox'")." Maternity";
	$chk_insurance_is_glasses = $f->input("insurance_is_glasses","1",$ischk_insurance_is_glasses." type='checkbox'")." Glasses";
	$txt_inpatient_ammount = $f->input("inpatient_ammount",$jo["inpatient_ammount"],"type='number' step='1'");
	$txt_outpatient_ammount = $f->input("outpatient_ammount",$jo["outpatient_ammount"],"type='number' step='1'");
	$txt_dental_ammount = $f->input("dental_ammount",$jo["dental_ammount"],"type='number' step='1'");
	$txt_maternity_normal = $f->input("maternity_normal",$jo["maternity_normal"],"type='number' step='1'");
	$txt_maternity_cecar = $f->input("maternity_cecar",$jo["maternity_cecar"],"type='number' step='1'");
	$txt_maternity_miscarriage = $f->input("maternity_miscarriage",$jo["maternity_miscarriage"],"type='number' step='1'");
	$txt_glasses_ammount = $f->input("glasses_ammount",$jo["glasses_ammount"],"type='number' step='1'");
	
	$txt_lebaran = $f->input("lebaran",$jo["lebaran"]);
    $txt_other_benefits = $f->textarea("other_benefits",$jo["other_benefits"]);
    $cal_approved_at = $f->input("approved_at",$jo["approved_at"],"type='date'");
    $txt_approved_position = $f->input("approved_position",$jo["approved_position"]);
    $txt_approved_by = $f->input("approved_by",$jo["approved_by"]);
    $cal_checked_at = $f->input("checked_at",$jo["checked_at"],"type='date'");
    $txt_checked_position = $f->input("checked_position",$jo["checked_position"]);
    $txt_checked_by = $f->input("checked_by",$jo["checked_by"]);
	
?>

<?php include_once "job_order_add_edit_form.php"; ?>

<?php
	$db->addtable("joborder_allowances");$db->where("joborder_id",@$_GET["id"]);
	foreach($db->fetch_data(true) as $key => $_data){
		?>
			<script> 
				adding_row('allowance_rows','row_allowance_'); 
				document.getElementById("allowances[<?=$key;?>]").value = "<?=$_data["allowance_id"];?>";
				document.getElementById("allowance_price[<?=$key;?>]").value = "<?=$_data["price"];?>";
				document.getElementById("tax_paid_company1[<?=$key;?>]").value = "<?=$_data["tax_paid_company"];?>";
			</script> 
		<?php
	}
	$db->addtable("joborder_benefits");$db->where("joborder_id",@$_GET["id"]);
	foreach($db->fetch_data(true) as $key => $_data){
		?>
			<script> 
				adding_row('benefit_rows','benefit_detail_'); 
				document.getElementById("benefits[<?=$key;?>]").value = "<?=$_data["benefits_id"];?>";
				document.getElementById("benefit_percentage[<?=$key;?>]").value = "<?=$_data["percentage"];?>";
				document.getElementById("benefit_price[<?=$key;?>]").value = "<?=$_data["price"];?>";
			</script> 
		<?php
	}
?>
<?php include_once "footer.php";?>