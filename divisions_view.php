<?php include_once "head.php";?>
<div class="bo_title">View Divisions</div>

<?php
	$db->addtable("divisions");$db->where("id",$_GET["id"]);$db->limit(1);$division = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Divisions Name",$division["name"]));?>
		<?=$t->row(array("Description",$division["description"]));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>