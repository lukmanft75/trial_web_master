<?php include_once "head.php";?>
<div class="bo_title">Edit Catatan Kehadiran</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("attendance_notes");	$db->where("id",$_GET["id"]);
		$db->addfield("attendance_id");		$db->addvalue($_POST["attendance_id"]);
        $db->addfield("tanggal");			$db->addvalue($_POST["tanggal"]);
        $db->addfield("notes");				$db->addvalue($_POST["notes"]);
        $db->addfield("attended");			$db->addvalue($_POST["attended"]);
        $db->addfield("surat_dokter");		$db->addvalue($_POST["surat_dokter"]);
        $db->addfield("cuti");				$db->addvalue($_POST["cuti"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("attendance_notes");$db->where("id",$_GET["id"]);$db->limit(1);$attendance_note = $db->fetch_data();
	$sel_attendance_id = $f->select("attendance_id",$db->fetch_select_data("users","attendance_id","name",["group_id" => "1,2,3,4,5,6,7,8,9,10,19:IN"],["name"],"",true),$attendance_note["attendance_id"],"style='height:25px'");
	$txt_tanggal = $f->input("tanggal",$attendance_note["tanggal"],"type='date'");
    $txt_notes = $f->input("notes",$attendance_note["notes"]);
	$sel_attended = $f->select("attended",["0" => "Tidak","1" => "Ya"],$attendance_note["attended"],"style='height:25px'");
	$sel_surat_dokter = $f->select("surat_dokter",["0" => "Tidak","1" => "Ada"],$attendance_note["surat_dokter"],"style='height:25px'");
	$sel_cuti = $f->select("cuti",["0" => "Tidak","1" => "Ada"],$attendance_note["cuti"],"style='height:25px'");
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Tanggal",$txt_tanggal));?>
        <?=$t->row(array("Nama",$sel_attendance_id));?>
        <?=$t->row(array("Catatan",$txt_notes));?>
        <?=$t->row(array("Dianggap Hadir",$sel_attended));?>
        <?=$t->row(array("Ada Surat Dokter",$sel_surat_dokter));?>
        <?=$t->row(array("Cuti",$sel_cuti));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>