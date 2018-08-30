<?php
	
	
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_educations");
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("degree_id");		$db->addvalue($_POST["degree_id"]);
		$db->addfield("institution");			$db->addvalue($_POST["institution"]);
		$db->addfield("city");			$db->addvalue($_POST["city"]);
		$db->addfield("major");			$db->addvalue($_POST["major"]);
		$db->addfield("graduation_year");		$db->addvalue($_POST["graduation_year"]);
		$db->addfield("gpa");		$db->addvalue($_POST["gpa"]);
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
	$degree_id = $f->select("degree_id",$db->fetch_select_data("degrees","id","name",array(),array(),"",true));
	$institution = $f->input("institution",@$_POST["institution"]);
	$city = $f->input("city",@$_POST["city"]);
	$major = $f->input("major",@$_POST["major"]);
	$graduation_year = $f->input("graduation_year",@$_POST["graduation_year"]);
	$gpa = $f->input("gpa",@$_POST["gpa"]);
		
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]));?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Degree",$degree_id));?>
         <?=$t->row(array("Institution",$institution));?>
         <?=$t->row(array("City",$city));?>
         <?=$t->row(array("Major",$major));?>
         <?=$t->row(array("Graduation Year",$graduation_year));?>
         <?=$t->row(array("GPA",$gpa));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';\"");?>
<?=$f->end();?>