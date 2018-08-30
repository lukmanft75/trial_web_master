<?php
	include_once "win_head.php";
	echo $data["id"];
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_course");$db->where("id",$_GET["id"]);
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("degree_id");			$db->addvalue($_POST["degree_id"]);
		$db->addfield("course_name");		$db->addvalue($_POST["course_name"]);
		$db->addfield("course_year");		$db->addvalue($_POST["course_year"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] > 0) echo "<font color='green'><b>Data saved</b></font><br><br>";
		
	}
	
	$db->addtable("candidate_course");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$data["candidate_id"]));
	$degree_id = $f->select("degree_id",$db->fetch_select_data("degrees","id","name"),$data["degree_id"]);
	$course_name = $f->input("course_name",$data["course_name"]);
	$course_year = $f->input("course_year",$data["course_year"]);
	
	
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]));?>
	<?=$t->start("","editor_content");?>
		 <?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Degree",$degree_id));?>
         <?=$t->row(array("Course Name",$course_name));?>
         <?=$t->row(array("Course Year",$course_year));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?php $_SERVER["QUERY_STRING"] = str_replace("id=".$_GET["id"]."&","",$_SERVER["QUERY_STRING"]); ?>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."';\"");?>
<?=$f->end();?>