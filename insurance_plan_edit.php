<?php include_once "head.php";?>
<div class="bo_title">Edit Insurance Plan</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("insurance_plan");$db->where("id",$_GET["id"]);
		$db->addfield("insurance_corp");$db->addvalue($_POST["corp"]);
		$db->addfield("plan");$db->addvalue($_POST["plan"]);
		$db->addfield("price");$db->addvalue($_POST["price"]);
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
	
	$db->addtable("insurance_plan");$db->where("id",$_GET["id"]);$db->limit(1);$insurance_plan = $db->fetch_data();
	$corp = $f->input("corp",$insurance_plan["insurance_corp"]);
	$plan = $f->input("plan",$insurance_plan["plan"]);
	$price = $f->input("price",$insurance_plan["price"],"type='number'");
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Insurance Coorporation",$corp));?>
		<?=$t->row(array("Plan",$plan));?>
		<?=$t->row(array("Price",$price));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>