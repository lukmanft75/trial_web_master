<?php include_once "head.php";?>
<div class="bo_title">Add User</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("users");
		$db->addfield("group_id");				$db->addvalue(@$_POST["group_id"]);
		$db->addfield("email");					$db->addvalue(@$_POST["email"]);
		$db->addfield("password");				$db->addvalue(base64_encode(@$_POST["password"]));
		$db->addfield("attendance_id");			$db->addvalue(@$_POST["attendance_id"]);
		$db->addfield("leave_num");				$db->addvalue(@$_POST["leave_num"]);
		$db->addfield("name");					$db->addvalue(@$_POST["name"]);
		$db->addfield("job_title");				$db->addvalue(@$_POST["job_title"]);
		$db->addfield("job_division");			$db->addvalue(@$_POST["job_division"]);
		$db->addfield("forbidden_chr_dashboards");$db->addvalue(@$_POST["forbidden_chr_dashboards"]);
		$db->addfield("created_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");			$db->addvalue($__username);
		$db->addfield("created_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$insert_id = $inserting["insert_id"];
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			echo $inserting["error"];
			javascript("alert('Saving data failed');");
		}
	}
	
	$txt_email 						= $f->input("email",@$_POST["email"]);
	$sel_group 						= $f->select("group_id",$db->fetch_select_data("groups","id","name",null,array("name")));
	$txt_password 					= $f->input("password","","type='password'");
	$txt_name 						= $f->input("name",@$_POST["name"]);
	$txt_attendance_id 				= $f->input("attendance_id",@$_POST["attendance_id"]);
	$txt_leave_num 					= $f->input("leave_num",@$_POST["leave_num"]);
	$txt_job_title 					= $f->input("job_title",@$_POST["job_title"]);
	$txt_job_division 				= $f->input("job_division",@$_POST["job_division"]);
	$txt_forbidden_chr_dashboards	= $f->input("forbidden_chr_dashboards",@$_POST["forbidden_chr_dashboards"]);
?>
<?=$f->start();?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Group",$sel_group));?>
		<?=$t->row(array("Email",$txt_email));?>
		<?=$t->row(array("Password",$txt_password));?>
		<?=$t->row(array("Name",$txt_name));?>
		<?=$t->row(array("Attendance ID",$txt_attendance_id));?>
		<?=$t->row(array("Leave Quota",$txt_leave_num));?>
		<?=$t->row(array("Job Title",$txt_job_title));?>
		<?=$t->row(array("Job Division",$txt_job_division));?>
		<?=$t->row(array("Forbidden Chr Dashboards",$txt_forbidden_chr_dashboards));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>