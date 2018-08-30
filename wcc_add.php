<?php include_once "head.php";?>
<div class="bo_title">Add WCC</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("wcc");	
		$db->addfield("wcc_no");				$db->addvalue($_POST["wcc_no"]);
		$db->addfield("po_no");					$db->addvalue($_POST["po_no"]);
		$db->addfield("storage_position");		$db->addvalue($_POST["storage_position"]);
		$db->addfield("created_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");			$db->addvalue($__username);
		$db->addfield("created_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$wcc_id = $inserting["insert_id"];
			if($_FILES["softcopy"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
				$softcopy_name = $wcc_id.".".$_ext;
				move_uploaded_file($_FILES['softcopy']['tmp_name'],"files_wcc/".$softcopy_name);
				$db->addtable("wcc");			$db->where("id",$wcc_id);
				$db->addfield("softcopy");		$db->addvalue($softcopy_name);
				$db->update();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$wcc_no 				= $f->input("wcc_no","","size='25'");
	$po_no 					= $f->input("po_no","","size='25'");
	$storage_position 		= $f->input("storage_position");
	$softcopy 				= $f->input("softcopy","","type='file'");
?>

<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("WCC No",$wcc_no));?>
         <?=$t->row(array("PO No",$po_no));?>
         <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
         <?=$t->row(array("Softcopy File",$softcopy));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>