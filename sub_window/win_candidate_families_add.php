<?php
	
	
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_families");
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("relation_id");		$db->addvalue($_POST["relation_id"]);
		$db->addfield("fullname");			$db->addvalue($_POST["fullname"]);
		$db->addfield("birthdate");			$db->addvalue($_POST["birthdate"]);
		$db->addfield("degree_id");			$db->addvalue($_POST["degree_id"]);
		$db->addfield("occupation");		$db->addvalue($_POST["occupation"]);
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
	$birthdate = $f->input("birthdate",@$_POST["birthdate"],"type='date'");
	$degree_id = $f->select("degree_id",$db->fetch_select_data("degrees","id","name",array(),array(),"",true));
	$occupation = $f->input("occupation",@$_POST["occupation"]);
		
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]));?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Relation",$relation_id));?>
         <?=$t->row(array("Fullname",$fullname));?>
         <?=$t->row(array("Birthdate",$birthdate));?>
         <?=$t->row(array("Degree",$degree_id));?>
         <?=$t->row(array("Occupation",$occupation));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';\"");?>
<?=$f->end();?>