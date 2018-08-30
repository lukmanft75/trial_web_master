<?php include_once "head.php";?>
<div class="bo_title">View All Data Update</div>
<?php	
	$db->addtable("all_data_update");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	$sel_joborder = $data["joborder_id"];
	$txt_user = $data["user"];
	$txt_original_join_date = format_tanggal($data["original_join_date"]);
	$sel_project = $db->fetch_single_data("projects","name",array("id" => $data["project_id"]));
	
	
	$last_joborder_id = $db->fetch_single_data("joborder","id",array("joborder_id" => $data["joborder_id"]),array("created_at DESC")) * 1;
	if($last_joborder_id == 0) $last_joborder_id = $data["joborder_id"];
	$end_date = $db->fetch_single_data("joborder","join_end",array("id"=>$last_joborder_id));
	$txt_least_day = 0;
	if($end_date > date("Y-m-d")) $txt_least_day = day_diff($end_date,date("Y-m-d"));
	$txt_remarks = $data["remarks"];
	$datastyle = "style='font-style: italic;font-weight: bold;'";
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Job Order",$sel_joborder));?>
        <?=$t->row(array("Code","<div id='candidate_code' ".$datastyle."></div>"));?>
        <?=$t->row(array("Name","<div id='candidate_name' ".$datastyle."></div>"));?>
        <?=$t->row(array("Date of Birth","<div id='candidate_birthdate' ".$datastyle."></div>"));?>
        <?=$t->row(array("Sex","<div id='candidate_sex' ".$datastyle."></div>"));?>
        <?=$t->row(array("Tax Status","<div id='tax_status' ".$datastyle."></div>"));?>
        <?=$t->row(array("Medical Status","<div id='medical_status' ".$datastyle."></div>"));?>
        <?=$t->row(array("Position","<div id='jo_position' ".$datastyle."></div>"));?>
        <?=$t->row(array("User",$txt_user));?>
        <?=$t->row(array("Join Date","<div id='jo_join_start' ".$datastyle."></div>"));?>
        <?=$t->row(array("Original Join Date",$txt_original_join_date));?>
        <?=$t->row(array("Project",$sel_project));?>
        <?=$t->row(array("Least Day",$txt_least_day));?>
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
	<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
	<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?=$f->end();?>
<?php 
	if($_GET["id"] > 0){
		$joborder_id = $db->fetch_single_data("all_data_update","joborder_id",array("id" => $_GET["id"]));
		$candidate_id = $db->fetch_single_data("all_data_update","candidate_id",array("id" => $_GET["id"]));
		$tax_status_id = $db->fetch_single_data("all_data_update","tax_status_id",array("id" => $_GET["id"]));
		$medical_status_id = $db->fetch_single_data("all_data_update","medical_status_id",array("id" => $_GET["id"]));
		$tax_status = $db->fetch_single_data("statuses","name",array("id" => $tax_status_id));
		$medical_status = $db->fetch_single_data("statuses","name",array("id" => $medical_status_id));
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
		$status_id = $db->fetch_single_data("candidates","status_id",array("id" => $candidate_id));
		$candidate_status = $db->fetch_single_data("statuses","name",array("id" => $status_id));
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
		document.getElementById("tax_status").innerHTML = "<?=$tax_status;?>";
		document.getElementById("medical_status").innerHTML = "<?=$medical_status;?>";
		document.getElementById("jo_position").innerHTML = "<?=$jo_position;?>";
		document.getElementById("jo_join_start").innerHTML = "<?=$jo_join_start;?>";
		document.getElementById("jo_remarks").innerHTML = "<?=$jo_remarks;?>";
		document.getElementById("jo_basic_salary").innerHTML = "<?=$jo_basic_salary;?>";
		document.getElementById("jo_meal_transport").innerHTML = "<?=$jo_meal_transport;?>";
		document.getElementById("jo_comm_allowance").innerHTML = "<?=$jo_comm_allowance;?>";
		document.getElementById("jo_fixed_allowance").innerHTML = "<?=$jo_fixed_allowance;?>";
		document.getElementById("candidate_address").innerHTML = "<?=$candidate_address;?>";
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