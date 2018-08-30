<?php
	
	
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_relations");
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("relation_id");		$db->addvalue($_POST["relation_id"]);
		$db->addfield("fullname");			$db->addvalue($_POST["fullname"]);
		$db->addfield("division");			$db->addvalue($_POST["division"]);
		$db->addfield("location");			$db->addvalue($_POST["location"]);
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
	$relation_id = $f->select("relation_id",$db->fetch_select_data("relations","id","name",array(),array(),"",true));
	$fullname = $f->input("fullname",@$_POST["fullname"]);
	$division = $f->input("division",@$_POST["division"]);
	$location = $f->input("location",@$_POST["location"]);
		
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]));?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Relation",$relation_id));?>
         <?=$t->row(array("Fullname",$fullname));?>
         <?=$t->row(array("Division",$division));?>
         <?=$t->row(array("Location",$location));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';\"");?>
<?=$f->end();?>