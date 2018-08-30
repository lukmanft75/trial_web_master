<?php
	include_once "win_head.php";
	
	if($_GET["deleting"]){
		$db->addtable("candidate_families");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
	
	$db->addtable("candidate_families");
	$db->where("candidate_id",$_GET["candidate_id"]);
	$_data = $db->fetch_data(true);
	$_title = "Candidate Families";
?>
<h3><b><?=$_title;?></b></h3>
<?php
	if($_GET["addnew"]==1 || isset($_POST["saving_new"])){
		include_once "win_candidate_families_add.php";
	} else {
		echo $f->input("addnew","Add New Family","type='button' onclick=\"window.location='?addnew=1&".$_SERVER["QUERY_STRING"]."';\"");
	}
?>
<br><br>
<?=$t->start("","data_content");?>
<?=$t->header(array("No","Relation","Full Name","Date of Birth","Last Education","Occupation",""));?>
<?php 
	foreach($_data as $no => $data){
		$actions = "<a href=\"win_candidate_families_edit.php?id=".$data["id"]."&".$_SERVER["QUERY_STRING"]."\">Edit</a> |
					<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$data["id"]."';}\">Delete</a>
					";
		$relation = $db->fetch_single_data("relations","name",array("id" => $data["relation_id"]));
		$birthdate = format_tanggal($data["birthdate"]);
		$last_education = $db->fetch_single_data("degrees","name",array("id" => $data["degree_id"]));
	
		echo $t->row(array($no+1,$relation,$data["fullname"],$birthdate,$last_education,$data["occupation"],$actions),array("align='right' valign='top' ","align='right' valign='top' "));
	} 
?>
<?=$t->end();?>