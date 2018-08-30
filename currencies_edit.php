<?php include_once "head.php";?>
<div class="bo_title">Edit Currencies</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("currencies");$db->where("id",$_GET["id"]);
		$db->addfield("id");$db->addvalue($_POST["id"]);
		$db->addfield("name");$db->addvalue($_POST["name"]);
        $db->addfield("kurs");$db->addvalue($_POST["kurs"]);
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
	
	$db->addtable("currencies");$db->where("id",$_GET["id"]);$db->limit(1);$currency = $db->fetch_data();
	$txt_id = $f->input("id",$currency["id"]);
	$txt_name = $f->input("name",$currency["name"]);
    $txt_kurs = $f->input("kurs",$currency["kurs"]);
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Currency ID",$txt_id));?>
        <?=$t->row(array("Name",$txt_name));?>
		<?=$t->row(array("Kurs",$txt_kurs));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>