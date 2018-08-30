<?php include_once "head.php";?>
<div class="bo_title">Edit Divisions</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("divisions");$db->where("id",$_GET["id"]);
		$db->addfield("name");$db->addvalue($_POST["name"]);
        $db->addfield("description");$db->addvalue($_POST["description"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("divisions");$db->where("id",$_GET["id"]);$db->limit(1);$division = $db->fetch_data();
	$txt_name = $f->input("name",$division["name"]);
    $txt_description = $f->textarea("description",$division["description"]);
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Division Name",$txt_name));?>
		<?=$t->row(array("Description",$txt_description));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>