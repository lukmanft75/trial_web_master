<?php include_once "head.php";?>
<div class="bo_title">Edit COA</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("coa");			$db->where("coa",$_GET["coa"]);
		$db->addfield("coa");			$db->addvalue($_POST["coa"]);
		$db->addfield("parent");		$db->addvalue($_POST["parent"]);
        $db->addfield("description");	$db->addvalue($_POST["description"]);
		$db->addfield("prf_code");		$db->addvalue($_POST["prf_code"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("coa");$db->where("coa",$_GET["coa"]);$db->limit(1);$coa = $db->fetch_data();
	$txt_coa = $f->input("coa",$coa["coa"]);
	$sel_parent = $f->select("parent",$db->fetch_select_data("coa","coa","concat('[',coa,'] ',description)",array(),array("coa"),"",true),$coa["parent"]);
    $txt_description = $f->input("description",$coa["description"]);
	$txt_prf_code = $f->input("prf_code",$coa["prf_code"]);
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("COA",$txt_coa));?>
        <?=$t->row(array("Parent",$sel_parent));?>
        <?=$t->row(array("Description",$txt_description));?>
        <?=$t->row(array("PRF CODE",$txt_prf_code));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>