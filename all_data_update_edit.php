<?php include_once "head.php";?>
<div class="bo_title">Edit All Data Update</div>
<?php
	if($_GET["deleting"]){
		$db->addtable("all_data_update");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="all_data_update_list.php";</script> <?php
	}
	if(isset($_POST["save"])){
		$_POST["candidate_id"] = $db->fetch_single_data("joborder","candidate_id",array("id" => $_POST["joborder_id"]));
		//$_POST["code"] = $db->fetch_single_data("candidates","code",array("id" => $_POST["candidate_id"]));
		$_POST["position_id"] = $db->fetch_single_data("joborder","position_id",array("id" => $_POST["joborder_id"]));
		$db->addtable("all_data_update");
		$db->addfield("joborder_id");				$db->addvalue($_POST["joborder_id"]);
		$db->addfield("candidate_id");				$db->addvalue($_POST["candidate_id"]);
		$db->addfield("tax_status_id");				$db->addvalue($_POST["tax_status_id"]);
		$db->addfield("medical_status_id");			$db->addvalue($_POST["medical_status_id"]);
		//$db->addfield("code");						$db->addvalue($_POST["code"]);
		$db->addfield("original_join_date");		$db->addvalue($_POST["original_join_date"]);
		$db->addfield("position_id");				$db->addvalue($_POST["position_id"]);
		$db->addfield("user");						$db->addvalue($_POST["user"]);
		$db->addfield("remarks");		       		$db->addvalue($_POST["remarks"]);
		// $db->addfield("salary_thp");	       		$db->addvalue($_POST["salary_thp"]);
		// $db->addfield("reason_of_termination");	$db->addvalue($_POST["reason_of_termination"]);
		$db->addfield("created_at");		    	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");		    	$db->addvalue($__username);
		$db->addfield("created_ip");		    	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");		    	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		    	$db->addvalue($__username);
		$db->addfield("updated_ip");		    	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->where("id",$_GET["id"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			// javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
			javascript("window.close();");
		} else {
			javascript("alert('Failed Saving Data');");
		}
	}
	
	$db->addtable("all_data_update");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
	$sel_joborder = $f->select_window("joborder_id","Jobs Order",$data["joborder_id"],"joborder","id","concat(id) as id","win_joborder.php");
	$sel_tax_status = $f->select("tax_status_id",$db->fetch_select_data("statuses","id","name",array(),array("id"),"",true),$_GET["position"],"style='height:25px;'");
	$sel_medical_status = $f->select("medical_status_id",$db->fetch_select_data("statuses","id","name",array("only_marital" => "1"),array("id"),"",true),$_GET["position"],"style='height:25px;'");
	$txt_user = $f->input("user",$data["user"]);
	$txt_original_join_date = $f->input("original_join_date",$data["original_join_date"],"type='date'");
	$txt_remarks = $f->input("remarks",$data["remarks"],"style='width:300px;'");
	$datastyle = "style='font-style: italic;font-weight: bold;'";
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Job Order",$sel_joborder));?>
        <?=$t->row(array("Code","<div id='candidate_code' ".$datastyle."></div>"));?>
        <?=$t->row(array("Name","<div id='candidate_name' ".$datastyle."></div>"));?>
        <?=$t->row(array("Date of Birth","<div id='candidate_birthdate' ".$datastyle."></div>"));?>
        <?=$t->row(array("Sex","<div id='candidate_sex' ".$datastyle."></div>"));?>
        <?=$t->row(array("Status","<div id='candidate_status' ".$datastyle."></div>"));?>
        <?=$t->row(array("Tax Status",$sel_tax_status));?>
        <?=$t->row(array("Medical Status",$sel_medical_status));?>
        <?=$t->row(array("Position","<div id='jo_position' ".$datastyle."></div>"));?>
        <?=$t->row(array("User",$txt_user));?>
        <?=$t->row(array("Join Date","<div id='jo_join_start' ".$datastyle."></div>"));?>
        <?=$t->row(array("Original Join Date",$txt_original_join_date));?>
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
	<?=$f->input("save","Save","type='submit'");?>
	<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
	<?=$f->input("deleting","Delete","type='button' onclick=\"if(confirm('Anda yakin ingin menghapus data ini?')){window.location='?id=".$_GET["id"]."&deleting=".$_GET["id"]."';}\"");?>
<?=$f->end();?>
<?php 
	if($_GET["id"] > 0){
		$joborder_id = $db->fetch_single_data("all_data_update","joborder_id",array("id" => $_GET["id"]));
		$candidate_id = $db->fetch_single_data("all_data_update","candidate_id",array("id" => $_GET["id"]));
		$position_id = $db->fetch_single_data("all_data_update","position_id",array("id" => $_GET["id"]));
		$jo_position = $db->fetch_single_data("positions","name",array("id" => $position_id));
		$jo_join_start = format_tanggal($db->fetch_single_data("joborder","join_start",array("id" => $joborder_id)));
		$jo_remarks = $db->fetch_single_data("joborder","remarks",array("id" => $joborder_id));
		$jo_basic_salary = format_amount($db->fetch_single_data("joborder","basic_salary",array("id" => $joborder_id)));
		$jo_meal_transport = format_amount($db->fetch_single_data("joborder","meal_transport",array("id" => $joborder_id)));
		$jo_comm_allowance = format_amount($db->fetch_single_data("joborder","comm_allowance",array("id" => $joborder_id)));
		$jo_fixed_allowance = format_amount($db->fetch_single_data("joborder","fixed_allowance",array("id" => $joborder_id)));
		$candidate_code = $db->fetch_single_data("candidates","code",array("id" => $candidate_id));		
		$candidate_name = $db->fetch_single_data("candidates","name",array("id" => $candidate_id));
		$candidate_ktp = $db->fetch_single_data("candidates","ktp",array("id" => $candidate_id));
		$candidate_sex = $db->fetch_single_data("candidates","sex",array("id" => $candidate_id));
		$candidate_birthdate = $db->fetch_single_data("candidates","birthdate",array("id" => $candidate_id));
		$tax_status_id = $db->fetch_single_data("all_data_update","tax_status_id",array("id" => $_GET["id"]));
		$medical_status_id = $db->fetch_single_data("all_data_update","medical_status_id",array("id" => $_GET["id"]));
		$candidate_address = $db->fetch_single_data("candidates","address",array("id" => $candidate_id));
		$candidate_phone = $db->fetch_single_data("candidates","phone",array("id" => $candidate_id));
		$candidate_jamsostek = $db->fetch_single_data("candidates","phone",array("id" => $candidate_id));
		$candidate_bpjs_kesehatan = $db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => "1","candidate_id" => $candidate_id,"pisa" => "peserta"));
		$candidate_bpjs_ketenagakerjaan = $db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => "2","candidate_id" => $candidate_id,"pisa" => "peserta"));
		$candidate_email = $db->fetch_single_data("candidates","email",array("id" => $candidate_id));
		$candidate_bank_account = $db->fetch_single_data("candidates","concat(bank_name,':',bank_account) as bank",array("id" => $candidate_id));
		$candidate_npwp = $db->fetch_single_data("candidates","npwp",array("id" => $candidate_id));
?>
<script>
		document.getElementById("candidate_code").innerHTML = "<?=$candidate_code;?>";
		document.getElementById("candidate_name").innerHTML = "<?=$candidate_name;?>";
		document.getElementById("candidate_birthdate").innerHTML = "<?=$candidate_birthdate;?>";
		document.getElementById("candidate_sex").innerHTML = "<?=$candidate_sex;?>";
		document.getElementById("tax_status_id").value = "<?=$tax_status_id;?>";
		document.getElementById("medical_status_id").value = "<?=$medical_status_id;?>";
		document.getElementById("jo_position").innerHTML = "<?=$jo_position;?>";
		document.getElementById("jo_join_start").innerHTML = "<?=$jo_join_start;?>";
		document.getElementById("jo_remarks").innerHTML = "<?=$jo_remarks;?>";
		document.getElementById("jo_basic_salary").innerHTML = "<?=$jo_basic_salary;?>";
		document.getElementById("jo_meal_transport").innerHTML = "<?=$jo_meal_transport;?>";
		document.getElementById("jo_comm_allowance").innerHTML = "<?=$jo_comm_allowance;?>";
		document.getElementById("jo_fixed_allowance").innerHTML = "<?=$jo_fixed_allowance;?>";
		document.getElementById("candidate_address").innerHTML = "<?=str_ireplace(array(chr(10),chr(13)),"<br>",$candidate_address);?>";
		document.getElementById("candidate_phone").innerHTML = "<?=$candidate_phone;?>";
		document.getElementById("candidate_ktp").innerHTML = "<?=$candidate_ktp;?>";
		document.getElementById("candidate_jamsostek").innerHTML = "<?=$candidate_jamsostek;?>";
		document.getElementById("candidate_bpjs_kesehatan").innerHTML = "<?=$candidate_bpjs_kesehatan;?>";
		document.getElementById("candidate_bpjs_ketenagakerjaan").innerHTML = "<?=$candidate_bpjs_ketenagakerjaan;?>";
		document.getElementById("candidate_email").innerHTML = "<?=$candidate_email;?>";
		document.getElementById("candidate_bank_account").innerHTML = "<?=$candidate_bank_account;?>";
		document.getElementById("candidate_npwp").innerHTML = "<?=$candidate_npwp;?>";
</script>
<?php } ?>
<?php include_once "footer.php";?>