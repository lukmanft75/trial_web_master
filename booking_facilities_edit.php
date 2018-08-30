<?php include_once "head.php";?>
<div class="bo_title">Edit Booking Facility</div>
<?php
	if(isset($_POST["save"])){
		$_start_at_1 = adding_second($_POST["start_at"],1);
		$_end_at_1 = adding_second($_POST["end_at"],-1);
		$whereclause  = "id <> '".$_GET["id"]."' AND facility_id = '".$_POST["facility_id"]."' ";
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
			$_created_by = $db->fetch_single_data("booking_facilities","created_by",array("id" => $_GET["id"]));
			if($_created_by == $__username || $__user_id == 1){
				if($__group_id > 0){ $_POST["user_id"] = $__user_id; }
				$db->addtable("booking_facilities");	$db->where("id",$_GET["id"]);
				$db->addfield("user_id");				$db->addvalue(@$_POST["user_id"]);
				$db->addfield("facility_id");			$db->addvalue(@$_POST["facility_id"]);
				$db->addfield("start_at");				$db->addvalue(@$_POST["start_at"]);
				$db->addfield("end_at");				$db->addvalue(@$_POST["end_at"]);
				$db->addfield("description");			$db->addvalue(@$_POST["description"]);
				$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");			$db->addvalue($__username);
				$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$updating = $db->update();
				if($updating["affected_rows"] >= 0){
					javascript("alert('Data Saved');");
					javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
				} else {
					javascript("alert('Saving data failed');");
				}
			} else {
					javascript("alert('You cannot update this data, please ask $_created_by!');");
			}
		}else {
			javascript("alert('The Facility is already booked, please choose at different time!');");
		}
	}
	
	$db->addtable("booking_facilities");$db->where("id",$_GET["id"]);$db->limit(1);$booking_facility = $db->fetch_data();
	if($__group_id < 1){
		$available_user		= array();
	} else {
		$available_user		= array("id" => $__user_id);
	}
	$sel_user 			= $f->select("user_id",$db->fetch_select_data("users","id","name",$available_user,array("name")),$booking_facility["user_id"]);
	$sel_facility 		= $f->select("facility_id",$db->fetch_select_data("facilities","id","name",null,array("name")),$booking_facility["facility_id"]);
	$cal_start			= $f->input("start_at",str_replace(" ","T",$booking_facility["start_at"]),"type='datetime-local'");
	$cal_end 			= $f->input("end_at",str_replace(" ","T",$booking_facility["end_at"]),"type='datetime-local'");
	$txt_description 	= $f->input("description",$booking_facility["description"]);
?>
<?=$f->start();?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("User Names",$sel_user));?>
        <?=$t->row(array("facilities Names",$sel_facility));?>
		<?=$t->row(array("Start",$cal_start));?>
		<?=$t->row(array("End",$cal_end));?>
		<?=$t->row(array("Description",$txt_description));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>