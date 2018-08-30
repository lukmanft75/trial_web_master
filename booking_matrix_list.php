<?php include_once "head.php";?>
<div class="bo_title">Booking Matrix</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$tanggal = $f->input("tanggal",@$_GET["tanggal"],"type='date'");
			?>
			<?=$t->row(array("Start",$tanggal));?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit' style='width:100px;'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\" style='width:100px;'");?>
		<?=$f->end();?>
	</div>
</div>

<?php
	$whereclause = "";
	    if(@$_GET["tanggal"]!="") $whereclause .= "tanggal = '".$_GET["tanggal"]."' AND ";
			
	$db->addtable("booking_facilities");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$booking_facilities = $db->fetch_data(true);
	
	if(@$_GET["tanggal"] != ""){
?>
		<h3><b> Tanggal : <?=format_tanggal($_GET["tanggal"],"dMY");?> </b></h3>
		<?php
		$db->addtable("facilities");
		$facilities = $db->fetch_data(true);
		echo $t->start("","data_content");
			$headers[] = "Jam";
			foreach($facilities as $facility){ $headers[] = $facility["name"]; }
			echo $t->header($headers);
			$rows = array();
			for($i=0; $i<930;$i+=30){
				$curr_hours = date("H:i",mktime(6,$i));
				$curr_hours2 = date("H:i",mktime(6,$i+30));
				$rows[0] = $curr_hours." - ".$curr_hours2;
				$curr_datetime = $_GET["tanggal"]." ".$curr_hours;
				$curr_datetime2 = $_GET["tanggal"]." ".$curr_hours2;
				$cols = 0;
				foreach($facilities as $facility){
					$cols++;
					$rows_attr[$cols] = "align='center'";
					$db->addtable("booking_facilities");
					$db->addfield("user_id");
					$db->addfield("start_at");
					$db->addfield("end_at");
					$db->limit(1);
					$db->awhere("facility_id = '".$facility["id"]."' AND start_at < '$curr_datetime2' AND end_at > '$curr_datetime'");
					$arr = $db->fetch_data();
					if($arr["user_id"] > 0){
						$title = "";
						$db->addtable("booking_facilities");
						$db->addfield("user_id");
						$db->addfield("start_at");
						$db->addfield("end_at");
						$db->addfield("description");
						$db->awhere("facility_id = '".$facility["id"]."' AND start_at < '$curr_datetime2' AND end_at > '$curr_datetime'");
						foreach($db->fetch_data(true) as $arr){
							$title .= $db->fetch_single_data("users","name",array("id" => $arr["user_id"]));
							$title .= chr(13).chr(10).$arr["description"];
							$title .= chr(13).chr(10).substr($arr["start_at"],11,5)." - ".substr($arr["end_at"],11,5).chr(13).chr(10).chr(13).chr(10);
							
						}
						$rows[$cols] = "<img title='$title' src='icons/icon_booking.png' height='20'>";
					} else {
						$rows[$cols] = "<a href=\"booking_facilities_add.php?facility_id=".$facility["id"]."&date=".$_GET["tanggal"]."&start_at=".$curr_hours."&end_at=".$curr_hours2."\"><img src='icons/icon_sebelum_booking.png' height='20'></a>";
					}
				}
				echo $t->row($rows,$rows_attr);
			}
		echo $t->end();
	}
?>
<script> toogle_bo_filter(); </script>
<?php include_once "footer.php";?>