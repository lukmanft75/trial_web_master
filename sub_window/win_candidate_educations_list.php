<?php
	include_once "win_head.php";
	
	if($_GET["deleting"]){
		$db->addtable("candidate_educations");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
	
	$db->addtable("candidate_educations");
	$db->awhere("candidate_id = '".$_GET["candidate_id"]."'");
	$education = $db->fetch_data(true);
	
	$_title = "Candidate Education";  
 
?>
<h3><b><?=$_title;?></b></h3>
<?php
	if($_GET["addnew"]==1 || isset($_POST["saving_new"])){
		include_once "win_candidate_educations_add.php";
	} else {
		echo $f->input("addnew","Add New Education","type='button' onclick=\"window.location='?addnew=1&".$_SERVER["QUERY_STRING"]."';\"");
	}
	
?>
<br><br>
<?=$t->start("","data_content");?>
<?=$t->header(array("No","Candidate","Degree","Institution","City","Major","Graduation Year","GPA",""));?>
<?php 
	foreach($education as $no => $data){
		$actions = "<a href=\"win_candidate_educations_edit.php?id=".$data["id"]."&".$_SERVER["QUERY_STRING"]."\">Edit</a> |
					<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$data["id"]."';}\">Delete</a>
					";
		$candidate = $db->fetch_single_data("candidates","name",array("id" => $data["candidate_id"]));
		$relation = $db->fetch_single_data("relations","name",array("id" => $data["relation_id"]));
		$degree = $db->fetch_single_data("degrees","name",array("id" => $data["degree_id"]));
	
		echo $t->row(array($no+1,$candidate,$degree,$data["institution"],$data["city"],$data["major"],$data["graduation_year"],$data["gpa"],$actions),array("align='right' valign='top' ","align='right' valign='top' "));
	} 
?>
<?=$t->end();?>