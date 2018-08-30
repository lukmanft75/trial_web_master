<?php include_once "head.php";?>
<div class="bo_title">View Backoffice Menu</div>

<?php
	$db->addtable("backoffice_menu");$db->where("id",$_GET["id"]);$db->limit(1);$backoffice_men = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Parent ID",$backoffice_men["parent_id"]));?>
		<?=$t->row(array("Backoffice Menu",$backoffice_men["name"]));?>
        <?=$t->row(array("URL",$backoffice_men["url"]));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>