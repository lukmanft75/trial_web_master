<?php include_once "head.php";?>
<div class="bo_title">Edit Mailer</div>
<script src="./tinymce/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "#body",
	menubar : false,
	images_upload_base_path: 'attachments'
});
</script>
<?php
	if(isset($_POST["save"])){
		$db->addtable("mailer");$db->where("id",$_GET["id"]);
		$db->addfield("debug_receiver");	$db->addvalue($_POST["debug_receiver"]);
		$db->addfield("subject");			$db->addvalue($_POST["subject"]);
		$db->addfield("body");				$db->addvalue($_POST["body"]);
		$db->addfield("isdebug");			$db->addvalue($_POST["isdebug"]);
		$db->addfield("exec_time");			$db->addvalue($_POST["exec_time"]." ".$_POST["exec_time_hours"]);
		$db->addfield("status");			$db->addvalue(0);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			if($_FILES['recepients']['tmp_name']) {
				move_uploaded_file($_FILES['recepients']['tmp_name'], "../mailer_recepients/".$_GET["id"].".txt");
			}
			javascript("alert('Data Berhasil tersimpan');");
		} else {
			javascript("alert('Data gagal tersimpan');");
		}
	}
	
	$db->addtable("mailer");$db->where("id",$_GET["id"]);$db->limit(1);$mailer = $db->fetch_data();
	
	$txt_debug_receiver = $f->textarea("debug_receiver",$mailer["debug_receiver"]);
	$txt_subject = $f->input("subject",$_POST["subject"],"style='width:600px'");
	$txt_body = $f->textarea("body",$mailer["body"],"style='width:800px;height:500px;'");
	$sel_isdebug = $f->select("isdebug",array("0"=>"Ya","1"=>"Tidak"),$mailer["isdebug"]);
	$exec_time = explode(" ",$mailer["exec_time"]);
	$date_exec_time = $f->input_tanggal("exec_time",$exec_time[0])." ".$f->input_time("exec_time_hours",$exec_time[1]);
	$txt_recepients = $f->input("recepients","","type='file'");
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Debug Receiver",$txt_debug_receiver));?>
		<?=$t->row(array("Subject",$txt_subject));?>
		<?=$t->row(array("Body",$txt_body));?>
		<?=$t->row(array("Is Debuging",$sel_isdebug));?>
		<?=$t->row(array("Execute Time",$date_exec_time));?>
		<?=$t->row(array("Recepients",$txt_recepients));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> 
	<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
	<?=$f->input("view","View","type='button' onclick=\"window.location='mailer_view.php?id=".$_GET["id"]."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>