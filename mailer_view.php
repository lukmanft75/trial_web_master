<?php include_once "head.php";?>
<div class="bo_title">View Mailer</div>
<?php	
	$db->addtable("mailer");$db->where("id",$_GET["id"]);$db->limit(1);$mailer = $db->fetch_data();
	if($mailer["isdebug"]==0) $isdebug = "Ya";
	if($mailer["isdebug"]==1) $isdebug = "Tidak";
	
	if($mailer["status"]==0) $status = "Unsend";
	if($mailer["status"]==1) $status = "Progress";
	if($mailer["status"]==2) $status = "Sent";
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Debug Receiver",$mailer["debug_receiver"]));?>
		<?=$t->row(array("Subject",$mailer["subject"]));?>
		<?=$t->row(array("Body",$mailer["body"]));?>
		<?=$t->row(array("Is Debuging",$isdebug));?>
		<?=$t->row(array("Execute Time",format_tanggal($mailer["exec_time"],"dmY",true)));?>
		<?=$t->row(array("Status","<span id='status'></span>"));?>
		<?=$t->row(array("On Progress","<span id='onprogress'></span>"));?>
		<?=$t->row(array("Started Time",format_tanggal($mailer["started_time"],"dmY",true)));?>
		<?=$t->row(array("Finished Time",format_tanggal($mailer["finished_time"],"dmY",true)));?>
	<?=$t->end();?>
	<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
	<?=$f->input("edit","Edit","type='button' onclick=\"window.location='mailer_edit.php?id=".$_GET["id"]."';\"");?>
<?=$f->end();?>
<script>
	function reload_progress(){
		get_ajax("../ajax/get_mailer_status_ajax.php?id=<?=$_GET["id"];?>&mode=onprogress","onprogress");
		get_ajax("../ajax/get_mailer_status_ajax.php?id=<?=$_GET["id"];?>","status");
		setTimeout(function(){ reload_progress(); }, 3000);
	}
	
	reload_progress();
</script>
<?php include_once "footer.php";?>