<?php
	include_once "win_head.php";
	echo $data["id"];
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_work_experiences");$db->where("id",$_GET["id"]);
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("company_name");		$db->addvalue($_POST["company_name"]);
		$db->addfield("location");			$db->addvalue($_POST["location"]);
		$db->addfield("tasks");			$db->addvalue($_POST["tasks"]);
		$db->addfield("phone");			$db->addvalue($_POST["phone"]);
		$db->addfield("join_at");		$db->addvalue($_POST["join_at"]);
		$db->addfield("last_at");		$db->addvalue($_POST["last_at"]);
		$db->addfield("leaving_reason");		$db->addvalue($_POST["leaving_reason"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] > 0) echo "<font color='green'><b>Data saved</b></font><br><br>";
		
	}
	
	$db->addtable("candidate_work_experiences");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$data["candidate_id"]));
	$company_name = $f->input("company_name",$data["company_name"]);
	$location = $f->input("location",$data["location"]);
	$tasks = $f->textarea("tasks",$data["tasks"]);
	$phone = $f->input("phone",$data["phone"]);
	$join_at = $f->input("join_at",$data["join_at"],"type='date'");
	$last_at = $f->input("last_at",$data["last_at"],"type='date'");
	$leaving_reason = $f->textarea("leaving_reason",$data["leaving_reason"]);
	
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]));?>
	<?=$t->start("","editor_content");?>
		 <?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Company Name",$company_name));?>
         <?=$t->row(array("Location",$location));?>
         <?=$t->row(array("Tasks",$tasks));?>
         <?=$t->row(array("Phone",$phone));?>
         <?=$t->row(array("Join Date",$join_at));?>
         <?=$t->row(array("Last Date",$last_at));?>
         <?=$t->row(array("Reason for Leaving",$leaving_reason));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?php $_SERVER["QUERY_STRING"] = str_replace("id=".$_GET["id"]."&","",$_SERVER["QUERY_STRING"]); ?>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."';\"");?>
<?=$f->end();?>