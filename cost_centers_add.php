<?php include_once "head.php";?>
<div class="bo_title">Add Indottech Project</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("cost_centers");
		$db->addfield("code");				$db->addvalue($_POST["code"]);
		$db->addfield("departement");		$db->addvalue($_POST["departement"]);
		$db->addfield("name");				$db->addvalue($_POST["name"]);
		$db->addfield("project_id");		$db->addvalue($_POST["project_id"]);
		$db->addfield("scope_id");			$db->addvalue($_POST["scope_id"]);
		$db->addfield("region_ids");		$db->addvalue(sel_to_pipe($_POST["region_ids"]));
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
	
	$txt_code = $f->input("code",$_POST["code"]);
	$txt_departement = $f->input("departement",$_POST["departement"]);
	$txt_name = $f->input("name",$_POST["name"]);
	$sel_projects = $f->select("project_id",$db->fetch_select_data("indottech_projects","id","name",[],["id"],"",true),@$_POST["project_id"],"style='height:20px;'");
	$sel_scopes = $f->select("scope_id",$db->fetch_select_data("indottech_scopes","id","name",[],["id"],"",true),@$_POST["scope_id"],"style='height:20px;'");
	$sel_regions = $f->select_multiple("region_ids",$db->fetch_select_data("indottech_regions","id","name",[],["id"],"",true),@$_POST["region_ids"],"style='height:160px;'");
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Code",$txt_code));?>
        <?=$t->row(array("Departement",$txt_departement));?>
        <?=$t->row(array("Name",$txt_name));?>
        <?=$t->row(array("Project",$sel_projects));?>
        <?=$t->row(array("Scope",$sel_scopes));?>
        <?=$t->row(array("Region",$sel_regions));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>