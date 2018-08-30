<?php include_once "head.php";?>
<?php include_once "trainings_script.php";?>
<div class="bo_title">Edit Training Schedule</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("trainings");				$db->where("id",$_GET["id"]);
		$db->addfield("start_date");			$db->addvalue(@$_POST["start_date"]);
		$db->addfield("end_date");				$db->addvalue(@$_POST["end_date"]);
		$db->addfield("start_time");			$db->addvalue(@$_POST["start_time"]);
		$db->addfield("end_time");				$db->addvalue(@$_POST["end_time"]);
		$db->addfield("time_caption");			$db->addvalue(@$_POST["time_caption"]);
		$db->addfield("location");				$db->addvalue(str_replace("\n\r","<br>",@$_POST["location"]));
		$db->addfield("topic");					$db->addvalue(@$_POST["topic"]);
		$db->addfield("description");			$db->addvalue(@$_POST["description"]);
		$db->addfield("trainer");				$db->addvalue(@$_POST["trainer"]);
		$db->addfield("earlybird");				$db->addvalue(@$_POST["earlybird"]);
		$db->addfield("quota");					$db->addvalue(@$_POST["quota"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Berhasil tersimpan');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			echo $updating["error"];
			javascript("alert('Data gagal tersimpan');");
		}
	}
	
	$db->addtable("trainings"); $db->where("id",$_GET["id"]); $db->limit(1); $training = $db->fetch_data();
	
	$txt_start_date 			= $f->input("start_date",$training["start_date"],"type='date' style='widht:150px;' onblur='time_caption.value = load_time_caption();'");
	$txt_end_date	 			= $f->input("end_date",$training["end_date"],"type='date' style='widht:150px;' onblur='time_caption.value = load_time_caption();'");
	$txt_start_time 			= $f->input("start_time",$training["start_time"],"type='time' style='widht:100px;' onblur='time_caption.value = load_time_caption();'");
	$txt_end_time 				= $f->input("end_time",$training["end_time"],"type='time' style='widht:100px;' onblur='time_caption.value = load_time_caption();'");
	$txt_time_caption			= $f->input("time_caption",$training["time_caption"],"style='width:400px;' onfocus='this.value = load_time_caption();'");
	$txt_location				= $f->textarea("location",$training["location"]);
	$txt_topic				 	= $f->input("topic",$training["topic"]);
	$txt_description 			= $f->textarea("description",$training["description"]);
	$txt_trainer			 	= $f->input("trainer",$training["trainer"]);
	$txt_earlybird			 	= $f->input("earlybird",$training["earlybird"],"type='number'");
	$txt_quota			 		= $f->input("quota",$training["quota"],"type='number'");
?>
<?=$f->start();?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Event Date",$txt_start_date." - ".$txt_end_date));?>
		<?=$t->row(array("Time",$txt_start_time." - ".$txt_end_time));?>
		<?=$t->row(array("Time Caption",$txt_time_caption));?>
		<?=$t->row(array("Location",$txt_location));?>
		<?=$t->row(array("Topic",$txt_topic));?>
		<?=$t->row(array("Description",$txt_description));?>
		<?=$t->row(array("Trainer",$txt_trainer));?>
		<?=$t->row(array("Early Bird","Rp. ".$txt_earlybird));?>
		<?=$t->row(array("Quota",$txt_quota));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>