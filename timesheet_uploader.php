<?php	
	set_time_limit(0);
	ini_set("memory_limit", "-1");
	include_once "head.php";
	if(!isset($_POST["upload"])){
		$projects = $db->fetch_select_data("projects","id","name",[],["name"],"",true);
?>
	<table width="100%"><tr><td align="center">
		<b>UPLOAD TIME ATTENDANCE</b><br><br>
		<?=$f->start("","POST","?step=1","enctype=\"multipart/form-data\"");?>
			<table>
				<tr><td>Choose File</td><td>:</td><td><?=$f->input("attlog","","type='file' accept='.dat'");?></td></tr>
				<tr><td>Project</td><td>:</td><td><?=$f->select("project_id",$projects,"","style='height:20px;'");?></td></tr>
				<tr><td colspan="3"><?=$f->input("upload","Upload","type='submit'","btn_sign");?></td></tr>
			</table>
		<?=$f->end();?>
	</td></tr></table>	
<?php 
	} else {
		$total = 0;
		$file_name = rand(0,9).rand(0,9).rand(0,9).date("YmdHis").".dat";
		move_uploaded_file($_FILES["attlog"]["tmp_name"],"upload_files/".$file_name);
		$contents = file("upload_files/".$file_name);
		foreach($contents as $content){
			$attendance = explode(chr(9),$content);
			if(is_numeric(trim($attendance[0]))){
				$project_id = $_POST["project_id"];
				$attendance_id = trim($attendance[0]);
				$tap_time = $attendance[1];
				if(substr($tap_time,0,10) > "2018-03-23"){
					$db->addtable("attendance");
					$db->addfield("id");
					$db->where("project_id",$project_id);
					$db->where("attendance_id",$attendance_id);
					$db->where("tap_time",$tap_time);
					$db->limit(1);
					$att_exist = $db->fetch_data();
					if($att_exist["id"] <= 0){
						$db->addtable("attendance");
						$db->addfield("project_id");	$db->addvalue($project_id);
						$db->addfield("attendance_id");	$db->addvalue($attendance_id);
						$db->addfield("tap_time");		$db->addvalue($tap_time);
						$inserting = $db->insert();
						$total += $inserting["affected_rows"];
					}
				}
			}
		}
		echo "<b>Total : $total</b>";
	}
	include_once "footer.php";
?>