<?php
	include_once "win_head.php";
	echo $data["id"];
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_educations");$db->where("id",$_GET["id"]);
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("degree_id");		$db->addvalue($_POST["degree_id"]);
		$db->addfield("institution");			$db->addvalue($_POST["institution"]);
		$db->addfield("city");			$db->addvalue($_POST["city"]);
		$db->addfield("major");			$db->addvalue($_POST["major"]);
		$db->addfield("graduation_year");		$db->addvalue($_POST["graduation_year"]);
		$db->addfield("gpa");			$db->addvalue($_POST["gpa"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] > 0) echo "<font color='green'><b>Data saved</b></font><br><br>";
		
	}
	
	$db->addtable("candidate_educations");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$data["candidate_id"]));
	$degree_id = $f->select("degree_id",$db->fetch_select_data("degrees","id","name",array(),array(),"",true),$data["degree_id"]);
	$institution = $f->input("institution",@$data["institution"]);
	$city = $f->input("city",@$data["city"]);
	$major = $f->input("major",@$data["major"]);
	$graduation_year = $f->input("graduation_year",@$data["graduation_year"]);
	$gpa = $f->input("gpa",@$data["gpa"]);	
	
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
	<?php $_SERVER["QUERY_STRING"] = str_replace("id=".$_GET["id"]."&","",$_SERVER["QUERY_STRING"]); ?>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."';\"");?>
<?=$f->end();?>