<?php include_once "head.php";?>
<div class="bo_title">Add Mailer</div>
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
		$db->addtable("mailer");
		$db->addfield("debug_receiver");	$db->addvalue($_POST["debug_receiver"]);
		$db->addfield("subject");			$db->addvalue($_POST["subject"]);
		$db->addfield("body");				$db->addvalue($_POST["body"]);
		$db->addfield("isdebug");			$db->addvalue($_POST["isdebug"]);
		$db->addfield("exec_time");			$db->addvalue($_POST["exec_time"]." ".$_POST["exec_time_hours"]);
		$db->addfield("status");			$db->addvalue(0);
		$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");		$db->addvalue($__username);
		$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		echo $inserting["error"];
		if($inserting["affected_rows"] >= 0){
			$insert_id = $inserting["insert_id"];
			if($_FILES['recepients']['tmp_name']) {
				move_uploaded_file($_FILES['recepients']['tmp_name'], "../mailer_recepients/".$insert_id.".txt");
			}
			javascript("alert('Data Berhasil tersimpan');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Data gagal tersimpan');");
		}
	}
	
	$txt_debug_receiver = $f->textarea("debug_receiver",$mailer["debug_receiver"]);
	$txt_subject = $f->input("subject",$_POST["subject"],"style='width:600px'");
	$txt_body = $f->textarea("body",$_POST["body"],"style='width:800px;height:500px;'");
	$sel_isdebug = $f->select("isdebug",array("0"=>"Ya","1"=>"Tidak"),$_POST["isdebug"]);
	$date_exec_time = $f->input_tanggal("exec_time",$_POST["exec_time"])." ".$f->input_time("exec_time_hours",$_POST["exec_time_hours"]);
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
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>