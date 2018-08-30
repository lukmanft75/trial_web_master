<?php
	
	
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_work_experiences");
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("company_name");		$db->addvalue($_POST["company_name"]);
		$db->addfield("location");			$db->addvalue($_POST["location"]);
		$db->addfield("tasks");			$db->addvalue($_POST["tasks"]);
		$db->addfield("phone");			$db->addvalue($_POST["phone"]);
		$db->addfield("join_at");		$db->addvalue($_POST["join_at"]);
		$db->addfield("last_at");		$db->addvalue($_POST["last_at"]);
		$db->addfield("leaving_reason");		$db->addvalue($_POST["leaving_reason"]);
		$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");		$db->addvalue($__username);
		$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		print_r($inserting);
		?><script> window.location="?<?=str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]);?>"; </script><?php
		
	}
	
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$_GET["candidate_id"]));
	$company_name = $f->input("company_name",@$_POST["company_name"]);
	$location = $f->input("location",@$_POST["location"]);
	$tasks = $f->textarea("tasks",@$_POST["tasks"]);
	$phone = $f->input("phone",@$_POST["phone"]);
	$join_at = $f->input("join_at",@$_POST["join_at"],"type='date'");
	$last_at = $f->input("last_at",@$_POST["last_at"],"type='date'");
	$leaving_reason = $f->textarea("leaving_reason",@$_POST["leaving_reason"]);
		
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
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';\"");?>
<?=$f->end();?>