<?php include_once "head.php";?>
<div class="bo_title">View Insurance Plan</div>

<?php
	$db->addtable("insurance_plan");$db->where("id",$_GET["id"]);$db->limit(1);$insurance_plan = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Insurance Coorporation",$insurance_plan["insurance_corp"]));?>
        <?=$t->row(array("Plan",$insurance_plan["plan"]));?>
        <?=$t->row(array("Price",$insurance_plan["price"]));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>