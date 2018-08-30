<?php include_once "head.php";?>
<div class="bo_title">Time Sheet</div>
<?=$error_messages;?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
			$loop = true;
			$_d1 = 29;
			$workday = 0;
			while($loop){
				$_d1--;
				$_day1 = date("N",mktime(0,0,0,date("m")-1,$_d1,date("Y")));
				if($_day1 != 6 && $_day1 != 7) $workday++;
				if($workday > 3) $loop = false;
			}
			
			$loop = true;
			$_d2 = 29;
			$workday = 0;
			while($loop){
				$_d2--;
				$_day2 = date("N",mktime(0,0,0,date("m"),$_d2,date("Y")));
				if($_day2 != 6 && $_day2 != 7) $workday++;
				if($workday > 3) $loop = false;
			}
			
			if(@$_GET["txt_tap_time1"] == "") $_GET["txt_tap_time1"] = date("Y-m-d",mktime(0,0,0,date("m")-1,$_d1,date("Y")));
			if(@$_GET["txt_tap_time2"] == "") $_GET["txt_tap_time2"] = date("Y-m-d",mktime(0,0,0,date("m"),$_d2,date("Y")));
                $txt_tap_time1 = $f->input("txt_tap_time1",@$_GET["txt_tap_time1"],"type='date'");
                $txt_tap_time2 = $f->input("txt_tap_time2",@$_GET["txt_tap_time2"],"type='date'");
				$sel_user_id = $f->select("sel_user_id",$db->fetch_select_data("users","id","name",["group_id" => "1,2,3,4,5,6,7,8,9,10,19:IN"],["name"],"",true),@$_GET["sel_user_id"],"style='height:25px'");
			?>
			<?=$t->row(array("Periode",$txt_tap_time1." - ".$txt_tap_time2));?>
			<?php if($__group_id <= 2){ echo $t->row(array("Nama",$sel_user_id)); } ?>
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
	
	$numdate = $db->fetch_single_data("coa","concat(DATEDIFF('$txt_tap_time2','$txt_tap_time1'))",[]) + 1;
	$errmessage="";
	if($numdate > 34 || $numdate <= 0){ $errmessage="<font color='red'>Periode harus < dari 35 hari</font>"; }
	echo $errmessage;
	if($errmessage == ""){
		$whereclause = "attendance_id > 0 AND ";
		if($__group_id > 2) $whereclause .= "id = '".$__user_id."' AND ";
		if(@$_GET["sel_user_id"] != "") $whereclause .= "id = '".$_GET["sel_user_id"]."' AND ";
		$db->addtable("users");
		if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
		$db->order("name");
		$users = $db->fetch_data(true);
		foreach($users as $user){
			$totTelat = 0;
			$totPulangAwal = 0;
			$TOTAL = 0;
			$_totTelat = 0;
			$_totPulangAwal = 0;
			$_TOTAL = 0;
			
			$attendance_id = $db->fetch_single_data("users","attendance_id",["id" => $user["id"]]);
			if($attendance_id > 0){
				echo $t->start();
					echo $t->row(["<h2><b>".$user["name"]."</b></h2>"],["colspan='8'"]);
					echo $t->row(["Jam Kerja : Senin - Jumat (8:00 - 17:00)"],["colspan='8'"]);
				echo $t->end();
				
				echo $t->start("","data_content");
					echo $t->header(["Hari","Tanggal","Masuk","Keluar","Terlambat","Pulang Awal","Jumlah Jam Kerja","Keterangan"]);
					for($xx = 0 ; $xx < $numdate ; $xx++){
						$m = substr($txt_tap_time1,5,2);
						$d = substr($txt_tap_time1,8,2) + $xx;
						$y = substr($txt_tap_time1,0,4);
						$currentDate = date("Y-m-d",mktime(0,0,0,$m,$d,$y));
						$day = date("N",mktime(0,0,0,$m,$d,$y));
						$rowAttr = "";
						$keterangan = "";
						$day_off = $db->fetch_single_data("day_off","keterangan",["tanggal" => $currentDate]);
						$attended = $db->fetch_single_data("attendance_notes","attended",["attendance_id" => $attendance_id,"tanggal" => $currentDate]);
						$attendance_notes = $db->fetch_single_data("attendance_notes","notes",["attendance_id" => $attendance_id,"tanggal" => $currentDate]);
						$keterangan = $attendance_notes;
						
						$masuk = substr($db->fetch_single_data("attendance","tap_time",["project_id" => "0", "attendance_id" => $attendance_id,"tap_time" => $currentDate."%:LIKE"],["tap_time"]),11,8);
						$keluar = substr($db->fetch_single_data("attendance","tap_time",["project_id" => "0", "attendance_id" => $attendance_id,"tap_time" => $currentDate."%:LIKE"],["tap_time DESC"]),11,8);
						if($masuk >= "12:00:00") $masuk = "";
						if($keluar < "12:00:00") $keluar = "";
						
						$telat = "";
						if($masuk > "09:00:00" && $day != 6 && $day !=7){
							$telat = "<font color='red'>".secToTime(timeDiff($masuk,"09:00:00"))."</font>";
							$totTelat += timeDiff($masuk,"09:00:00");
							$_totTelat++;
						}
						
						$pulangAwal = "";
						if($masuk < $keluar && $masuk != "" && $day != 6 && $day !=7){
							if(secToTime(timeDiff($keluar,$masuk)) < "09:00:00"){
								$pulangAwal = secToTime(timeDiff("09:00:00",secToTime(timeDiff($keluar,$masuk))));
								$totPulangAwal += timeDiff("09:00:00",secToTime(timeDiff($keluar,$masuk)));
								$_totPulangAwal++;
							}
						}
						
						$totalJam = "";
						if($masuk != "" && $keluar != ""){
							$totalJam = secToTime(timeDiff($keluar,$masuk));
							$TOTAL += timeDiff($keluar,$masuk);
						}
						
						if($masuk != "" || $keluar != "" || $attended) $_TOTAL++;
						
						if($masuk == "" && $keluar == "" && $day != 6 && $day !=7){
							if($day_off){
								$keterangan = $day_off;
							} else if(!$attended){
								$surat_dokter = $db->fetch_single_data("attendance_notes","surat_dokter",["attendance_id" => $attendance_id,"tanggal" => $currentDate]);
								$cuti = $db->fetch_single_data("attendance_notes","cuti",["attendance_id" => $attendance_id,"tanggal" => $currentDate]);
								if($surat_dokter) $keterangan .= " -- Ada surat dokter";
								else if($cuti) $keterangan .= "";
								else if($keterangan == "") $keterangan = "<font color='red'>Tidak Masuk</font>";
							} else if($keterangan == ""){
								$keterangan = "Dianggap masuk tanpa keterangan!";
							}
						}
						///////if($keterangan == "") $keterangan = $day_off;///KHUSUS RAMADHAN
						
						if($day == 6 || $day ==7) $rowAttr = "style='color:red;'";
						
						echo $t->row([hari($day),format_tanggal($currentDate),$masuk,$keluar,$telat,$pulangAwal,$totalJam,$keterangan],[],$rowAttr);
					}
					$totTelat = "<b>".secToTime($totTelat)." (".$_totTelat."x)</b>";
					$totPulangAwal = "<b>".secToTime($totPulangAwal)." (".$_totPulangAwal."x)</b>";
					$TOTAL = "<b>".secToTime($TOTAL)." (".$_TOTAL." hari)</b>";
					echo $t->row(["","","","",$totTelat,$totPulangAwal,$TOTAL,""]);
				echo $t->end()."<br>";
				
				$leaves = array();
				$db->addtable("day_off"); $db->where("tanggal",date("Y")."-%","s","LIKE"); $db->where("is_leave","1");
				$arrays = $db->fetch_data(true);
				foreach($arrays as $leave){ $leaves[$leave["tanggal"]] = $leave["keterangan"]; }
				$db->addtable("attendance_notes"); $db->where("tanggal",date("Y")."-%","s","LIKE"); $db->where("cuti","1"); $db->where("attendance_id",$attendance_id);
				$arrays = $db->fetch_data(true);
				foreach($arrays as $leave){ $leaves[$leave["tanggal"]] = $leave["notes"]; }
				ksort($leaves);
				$jatahCuti = $db->fetch_single_data("users","leave_num",["attendance_id" => $attendance_id]);
				$jmlCuti = 0;
				
				echo "<b>DAFTAR CUTI</b><br>";
				echo "<table><tr><td width='500'>";
					echo $t->start("","data_content");
						echo $t->header(["Tanggal","Keterangan Cuti"]);
						foreach($leaves as $tanggalCuti => $LeaveNotes){
							$jmlCuti++;
							echo $t->row([format_tanggal($tanggalCuti),$LeaveNotes]);
						}
						$sisaCuti = $jatahCuti - $jmlCuti;
						echo $t->header(["Sisa Cuti",$jatahCuti." - ".$jmlCuti." = ".$sisaCuti]);
					echo $t->end();
					
				echo "</td></tr></table>";
				echo "<br>";
				
				if(count($users) > 1) echo "<hr style='border: 1px solid #000;>";
			}
		}
	}
	include_once "footer.php";
?>