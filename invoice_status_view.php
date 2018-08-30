<?php include_once "head.php";?>
<div class="bo_title">View Invoice Status</div>

<?php
	$db->addtable("invoice_status");$db->where("id",$_GET["id"]);$db->limit(1);$invoice_status = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Invoice Status Name",$invoice_status["name"]));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>