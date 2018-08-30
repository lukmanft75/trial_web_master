<?php include_once "head.php";?>
<div class="bo_title">Add All Data Update</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("all_data_update");$db->where("joborder_id",$_POST["joborder_id"]);
		if(count($db->fetch_data(true)) > 0){
			javascript("alert('Job order already used');");
		} else {
			$_POST["candidate_id"] = $db->fetch_single_data("joborder","candidate_id",array("id" => $_POST["joborder_id"]));
			$_POST["code"] = $db->fetch_single_data("candidates","code",array("id" => $_POST["candidate_id"]));
			if($_POST["code"] != ""){
				$_POST["position_id"] = $db->fetch_single_data("joborder","position_id",array("id" => $_POST["joborder_id"]));
				$db->addtable("all_data_update");
				$db->addfield("joborder_id");				$db->addvalue($_POST["joborder_id"]);
				$db->addfield("candidate_id");				$db->addvalue($_POST["candidate_id"]);
				$db->addfield("tax_status_id");				$db->addvalue($_POST["tax_status_id"]);
				$db->addfield("medical_status_id");			$db->addvalue($_POST["medical_status_id"]);
				$db->addfield("original_join_date");		$db->addvalue($_POST["original_join_date"]);
				$db->addfield("code");						$db->addvalue($_POST["code"]);
				$db->addfield("homebase_ids");				$db->addvalue("|".$_POST["homebase_id"]."|");
				$db->addfield("position_ids");				$db->addvalue("|".$_POST["position_id"]."|");
				$db->addfield("user");						$db->addvalue($_POST["user"]);
				$db->addfield("project_ids");				$db->addvalue("|".$_POST["project_id"]."|");
				$db->addfield("remarks");		       		$db->addvalue($_POST["remarks"]);
				// $db->addfield("salary_thp");	       		$db->addvalue($_POST["salary_thp"]);
				// $db->addfield("reason_of_termination");	$db->addvalue($_POST["reason_of_termination"]);
				$db->addfield("created_at");		    	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("created_by");		    	$db->addvalue($__username);
				$db->addfield("created_ip");		    	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->addfield("updated_at");		    	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");		    	$db->addvalue($__username);
				$db->addfield("updated_ip");		    	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$inserting = $db->insert();
				if($inserting["affected_rows"] >= 0){
					javascript("alert('Data Saved');");
					javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
				} else {
					javascript("alert('Failed Saving Data');");
				}
			} else {
				javascript("alert('Please generate Candidate Code from Candidate Menu');");
			}
		}
	}
	
	$sel_joborder = $f->select_window("joborder_id","Jobs Order","","joborder","id","concat(id) as id","win_joborder.php");
	$sel_tax_status = $f->select("tax_status_id",$db->fetch_select_data("statuses","id","name",array(),array("id"),"",true),$_GET["position"],"style='height:25px;'");
	$sel_medical_status = $f->select("medical_status_id",$db->fetch_select_data("statuses","id","name",array("only_marital" => "1"),array("id"),"",true),$_GET["position"],"style='height:25px;'");
	$txt_user = $f->input("user");
	$txt_original_join_date = $f->input("original_join_date","","type='date'");
	$sel_homebase = $f->select("homebase_id",$db->fetch_select_data("homebases","id","name",array(),array("id"),"",true),$_GET["homebase_id"],"style='height:25px;'");
	$sel_project = $f->select("project_id",$db->fetch_select_data("projects","id","name",array(),array("id"),"",true),$_GET["project_id"],"style='height:25px;'");
	$txt_remarks = $f->input("remarks","","style='width:300px;'");
	$datastyle = "style='font-style: italic;font-weight: bold;'";
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Job Order",$sel_joborder));?>
        <?=$t->row(array("Code","<div id='candidate_code' ".$datastyle."></div>"));?>
        <?=$t->row(array("Name","<div id='candidate_name' ".$datastyle."></div>"));?>
        <?=$t->row(array("Date of Birth","<div id='candidate_birthdate' ".$datastyle."></div>"));?>
        <?=$t->row(array("Sex","<div id='candidate_sex' ".$datastyle."></div>"));?>
        <?=$t->row(array("Tax Status",$sel_tax_status));?>
        <?=$t->row(array("Medical Status",$sel_medical_status));?>
        <?=$t->row(array("Position","<div id='jo_position' ".$datastyle."></div>"));?>
        <?=$t->row(array("User",$txt_user));?>
        <?=$t->row(array("Original Join Date",$txt_original_join_date));?>
        <?=$t->row(array("Join Date","<div id='jo_join_start' ".$datastyle."></div>"));?>
        <?=$t->row(array("Homebase",$sel_homebase));?>
        <?=$t->row(array("Project",$sel_project));?>
        <?=$t->row(array("Remarks","<div id='jo_remarks' ".$datastyle."></div>"));?>
        <?=$t->row(array("Basic Salary","<div id='jo_basic_salary' ".$datastyle."></div>"));?>
        <?=$t->row(array("Meal & Transport","<div id='jo_meal_transport' ".$datastyle."></div>"));?>
        <?=$t->row(array("Communication Allow","<div id='jo_comm_allowance' ".$datastyle."></div>"));?>
        <?=$t->row(array("Fixed Allow","<div id='jo_fixed_allowance' ".$datastyle."></div>"));?>
        <?=$t->row(array("Address","<div id='candidate_address' ".$datastyle."></div>"));?>
        <?=$t->row(array("Phone","<div id='candidate_phone' ".$datastyle."></div>"));?>
        <?=$t->row(array("KTP","<div id='candidate_ktp' ".$datastyle."></div>"));?>
        <?=$t->row(array("Jamsostek","<div id='candidate_jamsostek' ".$datastyle."></div>"));?>
        <?=$t->row(array("BPJS Kesehatan","<div id='candidate_bpjs_kesehatan' ".$datastyle."></div>"));?>
        <?=$t->row(array("BPJS Ketenagakerjaan","<div id='candidate_bpjs_ketenagakerjaan' ".$datastyle."></div>"));?>
        <?=$t->row(array("Email","<div id='candidate_email' ".$datastyle."></div>"));?>
        <?=$t->row(array("Bank Account","<div id='candidate_bank_account' ".$datastyle."></div>"));?>
        <?=$t->row(array("NPWP","<div id='candidate_npwp' ".$datastyle."></div>"));?>
        <?=$t->row(array("Remarks",$txt_remarks));?>
	<?=$t->end();?>
	
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>