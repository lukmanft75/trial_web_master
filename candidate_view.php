<?php include_once "head.php";?>
<div class="bo_title">View Candidate</div>
<?=$f->input("families","Families","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_families_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("educations","Educations","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_educations_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("courses","Courses","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_courses_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("work_experiences","Work Experiences","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_work_experiences_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("relations","Relations","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_relations_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>
<?=$f->input("info","Info","type='button' onclick='$.fancybox.open({ href: \"sub_window/win_candidate_infos_list.php?candidate_id=".$_GET["id"]."\", height: \"80%\", type: \"iframe\" });'");?>

<?php
	$db->addtable("candidates");$db->where("id",$_GET["id"]);$db->limit(1);$candidate = $db->fetch_data();
	$status = $db->fetch_single_data("statuses","name",array("id"=>$candidate["status_id"]));
	$bpjskesehatan = $db->fetch_single_data("bpjs","bpjs_id",array("candidate_id"=>$candidate["id"],"bpjs_type" => '1',"pisa" => "peserta"));
	$bpjskesehatan .= "<img src='icons/search_window.png' style='float:right;position:relative' onclick='$.fancybox.open({ href: \"sub_window/win_bpjs_list.php?candidate_id=".$candidate["id"]."&bpjs_type=1&pisa=peserta\", height: \"80%\", type: \"iframe\" });'>";
	$bpjsketenagakerjaan = $db->fetch_single_data("bpjs","bpjs_id",array("candidate_id"=>$candidate["id"],"bpjs_type" => '2',"pisa" => "peserta"));
	$bpjsketenagakerjaan .= "<img src='icons/search_window.png' style='float:right;position:relative' onclick='$.fancybox.open({ href: \"sub_window/win_bpjs_list.php?candidate_id=".$candidate["id"]."&bpjs_type=2&pisa=peserta\", height: \"80%\", type: \"iframe\" });'>";
?>
<?=$t->start("","editor_content");?>
		<?=$t->row(array("Code",$candidate["code"]));?>
        <?=$t->row(array("Candidate Name",$candidate["name"]));?>
        <?=$t->row(array("Birthdate",$candidate["birthdate"]));?>
        <?=$t->row(array("Sex",$candidate["sex"]));?>
        <?=$t->row(array("Status",$status));?>
        <?=$t->row(array("Address",$candidate["address"]));?>
        <?=$t->row(array("Additional Address 1",$candidate["address_2"]));?>
        <?=$t->row(array("Additional Address 2",$candidate["address_3"]));?>
        <?=$t->row(array("Phone",$candidate["phone"]));?>
        <?=$t->row(array("Emergency Phone",$candidate["phone_2"]));?>
        <?=$t->row(array("Bank Name",$candidate["bank_name"]));?>
        <?=$t->row(array("Bank Account",$candidate["bank_account"]));?>
        <?=$t->row(array("KTP",$candidate["ktp"]));?>
        <?=$t->row(array("NPWP",$candidate["npwp"]));?>
        <?=$t->row(array("BPJS Kesehatan",$bpjskesehatan));?>
        <?=$t->row(array("BPJS Ketenagakerjaan",$bpjsketenagakerjaan));?>
        <?=$t->row(array("Email",$candidate["email"]));?>
        <?=$t->row(array("Attendance Id",$candidate["attendance_id"]));?>
        <?=$t->row(array("Join IndoHR At",$candidate["join_indohr_at"]));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>