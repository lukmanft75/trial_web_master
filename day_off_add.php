<?php include_once "head.php";?>
<div class="bo_title">Add Hari Libur dan Cuti Bersama</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("day_off");
        $db->addfield("tanggal");		$db->addvalue($_POST["tanggal"]);
        $db->addfield("keterangan");	$db->addvalue($_POST["keterangan"]);
        $db->addfield("is_leave");		$db->addvalue($_POST["is_leave"]);
		$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");	$db->addvalue($__username);
		$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$txt_tanggal = $f->input("tanggal",date("Y-m-d"),"type='date'");
    $txt_keterangan = $f->input("keterangan","");
    $sel_is_leave = $f->select("is_leave",["0" => "Bukan","1" => "Ya"],"");
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Tanggal",$txt_tanggal));?>
        <?=$t->row(array("Keterangan",$txt_keterangan));?>
        <?=$t->row(array("Cuti Bersama",$sel_is_leave));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>