<?php include_once "head.php";?>
<div class="bo_title">Edit Indottech Scope</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("indottech_scopes");$db->where("id",$_GET["id"]);
		$db->addfield("initial");			$db->addvalue($_POST["initial"]);
		$db->addfield("name");				$db->addvalue($_POST["name"]);
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
	
	$db->addtable("indottech_scopes");$db->where("id",$_GET["id"]);$db->limit(1);$indottech_scope = $db->fetch_data();
	$txt_initial = $f->input("initial",$indottech_scope["initial"]);
	$txt_name = $f->input("name",$indottech_scope["name"]);
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Scope Initial",$txt_initial));?>
		<?=$t->row(array("Scope Name",$txt_name));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>