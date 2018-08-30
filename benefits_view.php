<?php include_once "head.php";?>
<div class="bo_title">View Benefit</div>

<?php
	$db->addtable("benefits");$db->where("id",$_GET["id"]);$db->limit(1);$benefits = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Benefit Name",$benefits["name"]));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>