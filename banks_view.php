<?php include_once "head.php";?>
<div class="bo_title">View Banks</div>

<?php
	$db->addtable("banks");$db->where("id",$_GET["id"]);$db->limit(1);$bank = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
        <?=$t->row(array("Code",$bank["code"]));?>
        <?=$t->row(array("COA",$bank["coa"]));?>
        <?=$t->row(array("Name",$bank["name"]));?>
        <?=$t->row(array("No Rek",$bank["no_rek"]));?>
        <?=$t->row(array("Currency ID",$bank["currency_id"]));?>
        <?=$t->row(array("Kurs",format_amount($bank["kurs"])),array("","align='right'"));?>
        <?=$t->row(array("Saldo",format_amount($bank["saldo"])),array("","align='right'"));?>
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>