<?php include_once "head.php";?>
<div class="bo_title">Add Indottech Role</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("indottech_roles");
		$db->addfield("user_id");			$db->addvalue($_POST["user_id"]);
		$db->addfield("module");			$db->addvalue($_POST["module"]);
		$db->addfield("role");				$db->addvalue($_POST["role"]);
		$db->addfield("approve_min");		$db->addvalue($_POST["approve_min"]);
		$db->addfield("approve_max");		$db->addvalue($_POST["approve_max"]);
		$db->addfield("project_id");		$db->addvalue($_POST["project_id"]);
		$db->addfield("scope_id");			$db->addvalue($_POST["scope_id"]);
		$db->addfield("region_id");			$db->addvalue($_POST["region_id"]);
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
	
	$sel_user = $f->select("user_id",$db->fetch_select_data("users","id","email",["forbidden_chr_dashboards"=>"6"],["email"],"",true),$_POST["user_id"],"style='height:20px;'");
	$txt_module = $f->input("module",$_POST["module"]);
	$sel_role = $f->select("role",[""=>"","maker"=>"Maker","checker"=>"Checker","signer"=>"Signer","approver"=>"Approver"],@$_POST["role"],"style='height:20px;'");
	$sel_projects = $f->select("project_id",$db->fetch_select_data("indottech_projects","id","name",[],["id"],"",true),@$_POST["project_id"],"style='height:20px;'");
	$sel_scopes = $f->select("scope_id",$db->fetch_select_data("indottech_scopes","id","name",[],["id"],"",true),@$_POST["scope_id"],"style='height:20px;'");
	$sel_regions = $f->select("region_id",$db->fetch_select_data("indottech_regions","id","name",[],["id"],"",true),@$_POST["region_id"],"style='height:20px;'");
	$txt_approve_min = $f->input("approve_min",$_POST["approve_min"]);
	$txt_approve_max = $f->input("approve_max",$_POST["approve_max"]);
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Project",$sel_projects));?>
        <?=$t->row(array("Scope",$sel_scopes));?>
        <?=$t->row(array("Region",$sel_regions));?>
        <?=$t->row(array("User",$sel_user));?>
        <?=$t->row(array("Module",$txt_module));?>
        <?=$t->row(array("Role",$sel_role));?>
		<?=$t->row(array("Approve Minimal",$txt_approve_min));?>
		<?=$t->row(array("Approve Maximal",$txt_approve_max));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>