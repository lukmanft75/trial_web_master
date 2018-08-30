<?php include_once "head.php";?>
<div class="bo_title">Edit facilities</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("facilities");			$db->where("id",$_GET["id"]);
		$db->addfield("category");				$db->addvalue($_POST["category"]);
		$db->addfield("name");					$db->addvalue($_POST["name"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("facilities");$db->where("id",$_GET["id"]);$db->limit(1);$facilities = $db->fetch_data();
	$txt_name 			= $f->input("name",$facilities["name"]);
	$txt_category 		= $f->input("category",$facilities["category"]);
?>
<?=$f->start();?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Category",$txt_category));?>
		<?=$t->row(array("Facility Name",$txt_name));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>