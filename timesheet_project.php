<?php
	if($_GET["export"]){
		$_exportname = "TimeSheet.xls";
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$_exportname);
		header("Pragma: no-cache");
		header("Expires: 0");
		$_GET["do_filter"]="Load";
		$_isexport = true;
	}
	include_once "head.php";
?>
<div class="bo_title">Time Sheet</div>
<?=$error_messages;?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
				<?php
					if(@$_GET["txt_tap_time1"] == "") $_GET["txt_tap_time1"] = date("Y-m-d",mktime(0,0,0,date("m")-1,10,date("Y")));
					if(@$_GET["txt_tap_time2"] == "") $_GET["txt_tap_time2"] = date("Y-m-d",mktime(0,0,0,date("m"),10,date("Y")));
					$txt_tap_time1 = $f->input("txt_tap_time1",@$_GET["txt_tap_time1"],"type='date'");
					$txt_tap_time2 = $f->input("txt_tap_time2",@$_GET["txt_tap_time2"],"type='date'");
					$sel_projects = $f->select("sel_projects",$db->fetch_select_data("projects","id","name",[],["name"],"",true),$_GET["sel_projects"],"style='height:25px;'");
				?>
				<?=$t->row(["Periode",$txt_tap_time1." - ".$txt_tap_time2]);?>
				<?=$t->row(["Project",$sel_projects]);?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
		<?=$f->end();?>
	</div>
</div>

<?php
	function timeDiff($time1,$time2){
		$jam1 = explode(":",$time1);
		$jam2 = explode(":",$time2);
		return (($jam1[0]*3600) + ($jam1[1]*60) + $jam1[2]) - (($jam2[0]*3600) + ($jam2[1]*60) + $jam2[2]);
	}
	
	function secToTime($second){
		$hour = floor($second / 3600);
		$minute = floor($second / 60 % 60);
		$second = floor($second % 60);
		return substr("00",0,2-strlen($hour)).$hour.":".substr("00",0,2-strlen($minute)).$minute.":".substr("00",0,2-strlen($second)).$second;
	}
	
	function hari($day){
		$arr[1] = "Senin";
		$arr[2] = "Selasa";
		$arr[3] = "Rabu";
		$arr[4] = "Kamis";
		$arr[5] = "Jumat";
		$arr[6] = "Sabtu";
		$arr[7] = "Minggu";
		return $arr[$day];
	}
	
	$txt_tap_time2 = $_GET["txt_tap_time2"];
	$txt_tap_time1 = $_GET["txt_tap_time1"];
	$sel_projects = $_GET["sel_projects"];
	
	$numdate = $db->fetch_single_data("coa","concat(DATEDIFF('$txt_tap_time2','$txt_tap_time1'))",[]) + 1;
	$errmessage="";
	if($numdate > 32 || $numdate <= 0){ $errmessage = "<font color='red'>Periode harus < dari 33 hari</font>"; }
	if(!$sel_projects) $errmessage = "<font color='red'>Silakan Pilih Project</font>"; 
	echo $errmessage;
	if($errmessage == ""){
		$whereclause = "attendance_id > 0 AND id IN (SELECT candidate_id FROM all_data_update WHERE project_ids LIKE '%|".$sel_projects."|%') AND ";
		if(@$_GET["sel_user_id"] != "") $whereclause .= "id = '".$_GET["sel_user_id"]."' AND ";
		$db->addtable("candidates");
		if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
		$db->order("name");
		$users = $db->fetch_data(true);
		foreach($users as $user){			
			$attendance_id = $user["attendance_id"];
			if($attendance_id > 0){
				echo $t->start();
					echo $t->row(["<h2><b>".$user["name"]."</b></h2>"],["colspan='9'"]);
					echo $t->row(["(".$user["email"].")"],["colspan='9'"]);
				echo $t->end();
				
				echo $t->start("","data_content");
					echo $t->header(["Hari","Tanggal","Awal Tugas","Akhir Tugas","Masuk","Keluar","Terlambat","Pulang Awal","Status"]);
					for($xx = 0 ; $xx < $numdate ; $xx++){
						$m = substr($txt_tap_time1,5,2);
						$d = substr($txt_tap_time1,8,2) + $xx;
						$y = substr($txt_tap_time1,0,4);
						$currentDate = date("Y-m-d",mktime(0,0,0,$m,$d,$y));
						$day = date("N",mktime(0,0,0,$m,$d,$y));
						$rowAttr = "";
						
						$masuk = substr($db->fetch_single_data("attendance","tap_time",["project_id" => $sel_projects, "attendance_id" => $attendance_id,"tap_time" => $currentDate."%:LIKE"],["tap_time"]),11,8);
						$keluar = substr($db->fetch_single_data("attendance","tap_time",["project_id" => $sel_projects, "attendance_id" => $attendance_id,"tap_time" => $currentDate."%:LIKE"],["tap_time DESC"]),11,8);
						if($masuk >= "12:00:00") $masuk = "";
						if($keluar < "12:00:00") $keluar = "";
						
						$telat = "";
						if($masuk > "09:00:00" && $day != 6 && $day !=7){
							$telat = "<font color='red'>".secToTime(timeDiff($masuk,"09:00:00"))."</font>";
						}
						
						$pulangAwal = "";
						if($masuk < $keluar && $masuk != "" && $day != 6 && $day !=7){
							if(secToTime(timeDiff($keluar,$masuk)) < "09:00:00"){
								$pulangAwal = secToTime(timeDiff("09:00:00",secToTime(timeDiff($keluar,$masuk))));
							}
						}
						
						if($day == 6 || $day ==7) $rowAttr = "style='color:red;'";
						
						echo $t->row([hari($day),format_tanggal($currentDate),"8:00","17:00",$masuk,$keluar,$telat,$pulangAwal,""],[],$rowAttr);
					}
				echo $t->end()."<br>";
				
				if(count($users) > 1) echo "<hr style='border: 1px solid #000;'>";
			}
		}
	}
	include_once "footer.php";
?>