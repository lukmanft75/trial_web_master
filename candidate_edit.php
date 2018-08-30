<?php include_once "head.php";?>
<?php include_once "scripts/candidates_js.php";?>
<div class="bo_title">Edit Candidate</div>
<?=$f->input("families","Families","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_families_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("educations","Educations","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_educations_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("courses","Courses","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_courses_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("work_experiences","Work Experiences","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_work_experiences_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("relations","Relations","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_relations_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("info","Info","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_infos_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>

<?php
	if(isset($_POST["save"])){
		$db->addtable("candidates");			$db->where("id",$_GET["id"]);
		$db->addfield("code");					$db->addvalue($_POST["code"]);
        $db->addfield("name");					$db->addvalue($_POST["name"]);
        $db->addfield("birthdate");				$db->addvalue($_POST["birthdate"]);
        $db->addfield("sex");					$db->addvalue($_POST["sex"]);
        $db->addfield("status_id");				$db->addvalue($_POST["status_id"]);
        $db->addfield("address");				$db->addvalue($_POST["address"]);
        $db->addfield("address_2");				$db->addvalue($_POST["address_2"]);
        $db->addfield("address_3");				$db->addvalue($_POST["address_3"]);
        $db->addfield("phone");					$db->addvalue($_POST["phone"]);
        $db->addfield("phone_2");				$db->addvalue($_POST["phone_2"]);
        $db->addfield("bank_name");				$db->addvalue($_POST["bank_name"]);
        $db->addfield("bank_account");			$db->addvalue($_POST["bank_account"]);
        $db->addfield("ktp");					$db->addvalue($_POST["ktp"]);
        $db->addfield("npwp");					$db->addvalue($_POST["npwp"]);
        $db->addfield("email");					$db->addvalue($_POST["email"]);
        $db->addfield("attendance_id");			$db->addvalue($_POST["attendance_id"]);
        $db->addfield("join_indohr_at");		$db->addvalue($_POST["join_indohr_at"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
        $updating = $db->update();
       
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("candidates");$db->where("id",$_GET["id"]);$db->limit(1);$candidate = $db->fetch_data();
    
    $code = $f->input("code",$candidate["code"],"readonly").$f->input("btn_generate_code","Generate","type='button' onclick=\"generate_code('','code','false');\"");
	$name = $f->input("name",$candidate["name"]);
	$birthdate = $f->input("birthdate",$candidate["birthdate"],"type='date'");
	$sex = $f->select("sex",array("M" => "M", "F" => "F"),$candidate["sex"],"style='height:25px'");
	$status_id = $f->select("status_id",$db->fetch_select_data("statuses","id","name",array(),array()),$candidate["status_id"]);
	$address = $f->textarea("address",$candidate["address"]);
	$address_2 = $f->textarea("address_2",$candidate["address_2"]);
	$address_3 = $f->textarea("address_3",$candidate["address_3"]);
	$phone = $f->input("phone",$candidate["phone"]);
	$phone_2 = $f->input("phone_2",$candidate["phone_2"]);
	$bank_name = $f->input("bank_name",$candidate["bank_name"]);
	$bank_account = $f->input("bank_account",$candidate["bank_account"]);
	$ktp = $f->input("ktp",$candidate["ktp"]);
	$npwp = $f->input("npwp",$candidate["npwp"]);
	$email = $f->input("email",$candidate["email"]);
	$attendance_id = $f->input("attendance_id",$candidate["attendance_id"]);
	$join_indohr_at = $f->input("join_indohr_at",$candidate["join_indohr_at"],"type='date'");
	$bpjskesehatan = $db->fetch_single_data("bpjs","bpjs_id",array("candidate_id"=>$candidate["id"],"bpjs_type" => '1',"pisa" => "peserta"));
	$bpjskesehatan .= "<img src='icons/search_window.png' style='float:right;position:relative' onclick='$.fancybox.open({ href: \"sub_window/win_bpjs_list.php?candidate_id=".$candidate["id"]."&bpjs_type=1&pisa=peserta\", height: \"80%\", type: \"iframe\" });'>";
	$bpjsketenagakerjaan = $db->fetch_single_data("bpjs","bpjs_id",array("candidate_id"=>$candidate["id"],"bpjs_type" => '2',"pisa" => "peserta"));
	$bpjsketenagakerjaan .= "<img src='icons/search_window.png' style='float:right;position:relative' onclick='$.fancybox.open({ href: \"sub_window/win_bpjs_list.php?candidate_id=".$candidate["id"]."&bpjs_type=2&pisa=peserta\", height: \"80%\", type: \"iframe\" });'>";
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
    <?=$t->row(array("Code",$code));?>
     <?=$t->row(array("Name",$name));?>
     <?=$t->row(array("Birthdate",$birthdate));?>
     <?=$t->row(array("Sex",$sex));?>
     <?=$t->row(array("Status",$status_id));?>
     <?=$t->row(array("Address",$address));?>
	 <?=$t->row(array("Additional Address 1",$address_2));?>
	 <?=$t->row(array("Additional Address 2",$address_3));?>
     <?=$t->row(array("Phone",$phone));?>
     <?=$t->row(array("Emergency Phone",$phone_2));?>
     <?=$t->row(array("Bank Name",$bank_name));?>
     <?=$t->row(array("Bank Account",$bank_account));?>
     <?=$t->row(array("KTP",$ktp));?>
     <?=$t->row(array("NPWP",$npwp));?>
     <?=$t->row(array("BPJS Kesehatan",$bpjskesehatan));?>
     <?=$t->row(array("BPJS Ketenagakerjaan",$bpjsketenagakerjaan));?>
     <?=$t->row(array("Email",$email));?>
     <?=$t->row(array("Attendance Id",$attendance_id));?>
     <?=$t->row(array("Join IndoHR At",$join_indohr_at));?>
     <?=$t->row(array("&nbsp;"));?>
    <?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>