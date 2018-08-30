<?php include_once "head.php";?>
<div class="bo_title">Edit Indottech Project</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("cost_centers");$db->where("id",$_GET["id"]);
		$db->addfield("code");				$db->addvalue($_POST["code"]);
		$db->addfield("departement");		$db->addvalue($_POST["departement"]);
		$db->addfield("name");				$db->addvalue($_POST["name"]);
		$db->addfield("project_id");		$db->addvalue($_POST["project_id"]);
		$db->addfield("scope_id");			$db->addvalue($_POST["scope_id"]);
		$db->addfield("region_ids");		$db->addvalue(sel_to_pipe($_POST["region_ids"]));
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
	
	$db->addtable("cost_centers");$db->where("id",$_GET["id"]);$db->limit(1);$cost_center = $db->fetch_data();
	$txt_code = $f->input("code",$cost_center["code"]);
	$txt_departement = $f->input("departement",$cost_center["departement"]);
	$txt_name = $f->input("name",$cost_center["name"]);
	$sel_projects = $f->select("project_id",$db->fetch_select_data("indottech_projects","id","name",[],["id"],"",true),@$cost_center["project_id"],"style='height:20px;'");
	$sel_scopes = $f->select("scope_id",$db->fetch_select_data("indottech_scopes","id","name",[],["id"],"",true),@$cost_center["scope_id"],"style='height:20px;'");
	$sel_regions = $f->select_multiple("region_ids",$db->fetch_select_data("indottech_regions","id","name",[],["id"],"",true),pipetoarray(@$cost_center["region_ids"]),"style='height:160px;'");
	
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
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>