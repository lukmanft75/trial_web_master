<?php include_once "head.php";?>
<div class="bo_title">Edit Back Office Menu</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("backoffice_menu");$db->where("id",$_GET["id"]);
		$db->addfield("name");$db->addvalue($_POST["name"]);
        $db->addfield("parent_id");$db->addvalue($_POST["parent_id"]);
        $db->addfield("url");$db->addvalue($_POST["url"]);
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
	
	$db->addtable("backoffice_menu");$db->where("id",$_GET["id"]);$db->limit(1);$backoffice_men = $db->fetch_data();
	$txt_name = $f->input("name",$backoffice_men["name"]);
    $txt_parent_id = $f->input("parent_id",$backoffice_men["parent_id"]);
    $txt_url = $f->input("url",$backoffice_men["url"]);
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Parent ID",$txt_parent_id));?>
		<?=$t->row(array("Backoffice Menu",$txt_name));?>
        <?=$t->row(array("URL",$txt_url));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>