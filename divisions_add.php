<?php include_once "head.php";?>
<div class="bo_title">Add Division</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("divisions");
		$db->addfield("name");$db->addvalue($_POST["name"]);
        $db->addfield("description");$db->addvalue($_POST["description"]);
		$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");		$db->addvalue($__username);
		$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$txt_name = $f->input("name",$_POST["name"]);
    $txt_description = $f->textarea("description",$_POST["description"]);
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Division Name",$txt_name));?>
        <?=$t->row(array("Description",$txt_description));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>