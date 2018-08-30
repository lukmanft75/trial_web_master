<?php include_once "head.php";?>
<div class="bo_title">Add Insurance Plan</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("insurance_plan");
		$db->addfield("insurance_corp");				$db->addvalue($_POST["corp"]);
		$db->addfield("plan");				$db->addvalue($_POST["plan"]);
		$db->addfield("price");				$db->addvalue($_POST["price"]);
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
	
	$corp = $f->input("corp",$_POST["corp"]);
	$plan = $f->input("plan",$_POST["plan"]);
	$price = $f->input("price",$_POST["price"],"type='number'");
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Insurance Coorporation",$corp));?>
        <?=$t->row(array("Plan",$plan));?>
        <?=$t->row(array("Price",$price));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>