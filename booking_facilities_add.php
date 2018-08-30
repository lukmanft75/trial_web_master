<?php include_once "head.php";?>
<div class="bo_title">Add Booking Facility</div>
<?php	
	if(isset($_POST["save"])){
		$_start_at_1 = adding_second($_POST["start_at"],1);
		$_end_at_1 = adding_second($_POST["end_at"],-1);
		$whereclause  = "facility_id = '".$_POST["facility_id"]."' ";
		$whereclause .= "AND (";
		$whereclause .= "	('".$_start_at_1."' BETWEEN start_at AND end_at OR '".$_end_at_1."' BETWEEN start_at AND end_at) ";
		$whereclause .= "	OR('".$_start_at_1."' < end_at AND '".$_end_at_1."' > start_at) ";
		$whereclause .= ")";
		
		$db->addtable("booking_facilities");
		$db->addfield("user_id");
		$db->limit(1);
		$db->awhere($whereclause);
		$arr = $db->fetch_data();
		if($arr["user_id"] <= 0){
			if($__group_id > 0){ $_POST["user_id"] = $__user_id; }
			$db->addtable("booking_facilities");
			$db->addfield("user_id");				$db->addvalue(@$_POST["user_id"]);
			$db->addfield("facility_id");			$db->addvalue(@$_POST["facility_id"]);
			$db->addfield("start_at");				$db->addvalue(@$_POST["start_at"]);
			$db->addfield("end_at");				$db->addvalue(@$_POST["end_at"]);
			$db->addfield("description");			$db->addvalue(@$_POST["description"]);
			$db->addfield("created_at");			$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("created_by");			$db->addvalue($__username);
			$db->addfield("created_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");			$db->addvalue($__username);
			$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$inserting = $db->insert();
			if($inserting["affected_rows"] >= 0){
				$insert_id = $inserting["insert_id"];
				javascript("alert('Data Saved');");
				if($_GET["facility_id"] != "" && $_GET["date"] != ""){
					javascript("window.location='booking_matrix_list.php?tanggal=".$_GET["date"]."&page=1&sort=&do_filter=Load';");
				} else {
					javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
				}
			} else {
				echo $inserting["error"];
				javascript("alert('Saving data failed');");
			}
		} else {
			javascript("alert('The Facility is already booked, please choose at different time!');");
		}
	}
	
	$_POST["start_at"] = $_GET["date"]."T".$_GET["start_at"];
	$_POST["end_at"] = $_GET["date"]."T".$_GET["end_at"];
	$_POST["facility_id"] = $_GET["facility_id"];
	
	if(!isset($_POST["start_at"])) $_POST["start_at"] = date("Y-m-d H:i");
	if(!isset($_POST["end_at"])) $_POST["end_at"] = date("Y-m-d H:i");
	
	if($__group_id < 1){
		$available_user		= array();
	} else {
		$available_user		= array("id" => $__user_id);
	}
	$sel_user 			= $f->select("user_id",$db->fetch_select_data("users","id","name",$available_user,array("name")),$_POST["user_id"]);
	$sel_facility 		= $f->select("facility_id",$db->fetch_select_data("facilities","id","name",null,array("name")),$_POST["facility_id"]);
	$cal_start_at 		= $f->input("start_at",str_replace(" ","T",$_POST["start_at"]),"type='datetime-local'");
	$cal_end_at 		= $f->input("end_at",str_replace(" ","T",$_POST["end_at"]),"type='datetime-local'");
	$txt_description 	= $f->input("description",@$_POST["description"]);
?>
<?=$f->start("","POST","?facility_id=".$_GET["facility_id"]."&date=".$_GET["date"]."&start_at=".$_GET["start_at"]."&end_at=".$_GET["end_at"]);?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("User Name",$sel_user));?>
        <?=$t->row(array("Facility Name",$sel_facility));?>
		<?=$t->row(array("Start",$cal_start_at));?>
		<?=$t->row(array("End",$cal_end_at));?>
		<?=$t->row(array("Description",$txt_description));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>