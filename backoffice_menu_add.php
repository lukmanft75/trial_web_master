<?php include_once "head.php";?>
<div class="bo_title">Add Back Office Menu</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("backoffice_menu");
		$db->addfield("name");$db->addvalue($_POST["name"]);
        $db->addfield("parent_id");$db->addvalue($_POST["parent_id"]);
        $db->addfield("url");$db->addvalue($_POST["url"]);
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
    $txt_parent_id = $f->input("parent_id",$_POST["parent_id"]);
    $txt_url = $f->input("url",$_POST["url"]);
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Parent ID",$txt_parent_id));?>
        <?=$t->row(array("Backoffice Menu",$txt_name));?>
        <?=$t->row(array("URL",$txt_url));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>