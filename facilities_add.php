<?php include_once "head.php";?>
<div class="bo_title">Add Facilities</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("facilities");
		$db->addfield("category");				$db->addvalue(@$_POST["category"]);
		$db->addfield("name");					$db->addvalue(@$_POST["name"]);
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
	
	
	$txt_category 		= $f->input("category",@$_POST["category"]);
	$txt_name 			= $f->input("name",@$_POST["name"]);
?>
<?=$f->start();?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Category",$txt_category));?>
		<?=$t->row(array("Facility Name",$txt_name));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>