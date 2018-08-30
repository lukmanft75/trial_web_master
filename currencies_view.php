<?php include_once "head.php";?>
<div class="bo_title">View Currencies</div>

<?php
	$db->addtable("currencies");$db->where("id",$_GET["id"]);$db->limit(1);$currency = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Currency ID",$currency["id"]));?>
        <?=$t->row(array("Name",$currency["name"]));?>
		<?=$t->row(array("Kurs",format_amount($currency["kurs"])));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>