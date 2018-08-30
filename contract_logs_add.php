<?php include_once "head.php";?>
<div class="bo_title">Add Contract Logs</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("contract_logs");	
		$db->addfield("name");					$db->addvalue($_POST["name"]);
		$db->addfield("storage_position");		$db->addvalue($_POST["storage_position"]);
		$db->addfield("distributed_at");		$db->addvalue($_POST["distributed_at"]);
		$db->addfield("signed_by_employee_at");	$db->addvalue($_POST["signed_by_employee_at"]);
		$db->addfield("received_by_indohr_at");	$db->addvalue($_POST["received_by_indohr_at"]);
		$db->addfield("created_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");			$db->addvalue($__username);
		$db->addfield("created_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$contract_log_id = $inserting["insert_id"];
			if($_FILES["softcopy"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
				$softcopy_name = $contract_log_id.".".$_ext;
				move_uploaded_file($_FILES['softcopy']['tmp_name'],"files_contracts/".$softcopy_name);
				$db->addtable("contract_logs");	$db->where("id",$contract_log_id);
				$db->addfield("softcopy");		$db->addvalue($softcopy_name);
				$db->update();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$name 					= $f->input("name","","size='50'");
	$storage_position 		= $f->input("storage_position");
	$softcopy 				= $f->input("softcopy","","type='file'");
	$distributed_at 		= $f->input("distributed_at","","type='date'");
	$signed_by_employee_at 	= $f->input("signed_by_employee_at","","type='date'");
	$received_by_indohr_at 	= $f->input("received_by_indohr_at","","type='date'");
?>

<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Name",$name));?>
         <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
         <?=$t->row(array("Distributed At",$distributed_at));?>
         <?=$t->row(array("Signed by employee At",$signed_by_employee_at));?>
         <?=$t->row(array("Received At",$received_by_indohr_at));?>
         <?=$t->row(array("Softcopy File",$softcopy));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>