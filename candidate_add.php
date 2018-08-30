<?php include_once "head.php";?>
<?php include_once "scripts/candidates_js.php";?>
<div class="bo_title">Add Candidate</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("candidates");
		$db->addfield("code");			$db->addvalue($_POST["code"]);
		$db->addfield("name");			$db->addvalue($_POST["name"]);
		$db->addfield("birthdate");		$db->addvalue($_POST["birthdate"]);
		$db->addfield("sex");			$db->addvalue($_POST["sex"]);
		$db->addfield("status_id");		$db->addvalue($_POST["status_id"]);
		$db->addfield("address");		$db->addvalue($_POST["address"]);
		$db->addfield("address_2");		$db->addvalue($_POST["address_2"]);
		$db->addfield("address_3");		$db->addvalue($_POST["address_3"]);
		$db->addfield("phone");			$db->addvalue($_POST["phone"]);
		$db->addfield("phone_2");		$db->addvalue($_POST["phone_2"]);
		$db->addfield("bank_name");		$db->addvalue($_POST["bank_name"]);
		$db->addfield("bank_account");	$db->addvalue($_POST["bank_account"]);
		$db->addfield("ktp");			$db->addvalue($_POST["ktp"]);
		$db->addfield("npwp");			$db->addvalue($_POST["npwp"]);
		$db->addfield("email");			$db->addvalue($_POST["email"]);
		$db->addfield("attendance_id");	$db->addvalue($_POST["attendance_id"]);
		$db->addfield("join_indohr_at");$db->addvalue($_POST["join_indohr_at"]);
		$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");	$db->addvalue($__username);
		$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_edit",$_SERVER["PHP_SELF"])."?id=".$inserting["insert_id"]."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$code = $f->input("code",@$_POST["code"],"readonly").$f->input("btn_generate_code","Generate","type='button' onclick=\"generate_code('','code','false');\"");
	$name = $f->input("name",@$_POST["name"]);
	$birthdate = $f->input("birthdate",@$_POST["birthdate"],"type='date'");
	$sex = $f->select("sex",array("M" => "M", "F" => "F"),@$_POST["sex"],"style='height:25px'");
	$status_id = $f->select("status_id",$db->fetch_select_data("statuses","id","name",array(),array(),"",true));
	$address = $f->textarea("address",$_POST["address"]);
	$address_2 = $f->textarea("address_2",$_POST["address_2"]);
	$address_3 = $f->textarea("address_3",$_POST["address_3"]);
	$phone = $f->input("phone",$_POST["phone"]);
	$phone_2 = $f->input("phone_2",$_POST["phone_2"]);
	$bank_name = $f->input("bank_name",$_POST["bank_name"]);
	$bank_account = $f->input("bank_account",$_POST["bank_account"]);
	$ktp = $f->input("ktp",$_POST["ktp"]);
	$npwp = $f->input("npwp",$_POST["npwp"]);
	$email = $f->input("email",$_POST["email"]);
	$attendance_id = $f->input("attendance_id",$_POST["attendance_id"]);
	$join_indohr_at = $f->input("join_indohr_at",$_POST["join_indohr_at"],"type='date'");
	
	
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
         <?=$t->row(array("Email",$email));?>
         <?=$t->row(array("Attendance Id",$attendance_id));?>
         <?=$t->row(array("Join IndoHR At",$join_indohr_at));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>