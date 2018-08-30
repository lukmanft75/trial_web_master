<?php
	include_once "win_head.php";
	
	$db->addtable("bpjs");$db->where("id",$_GET["id"]);$db->limit(1);$databpjs = $db->fetch_data();
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$databpjs["candidate_id"]));
	$status_id = $db->fetch_single_data("statuses","name",array("id"=>$databpjs["status_id"]));
	if($_GET["bpjs_type"] == '1') $_title = "BPJS Kesehatan";
	if($_GET["bpjs_type"] == '2') $_title = "BPJS Ketenagakerjaan";
?>
<h3><b><?=$_title;?></b></h3>
<?=$t->start("","editor_content");?>
		<?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Code",$databpjs["code"]));?>
         <?=$t->row(array("Name",$databpjs["name"]));?>
         <?=$t->row(array("Birthdate",$databpjs["birthdate"]));?>
         <?=$t->row(array("Sex",$databpjs["sex"]));?>
         <?=$t->row(array("Status",$status_id));?>
         <?=$t->row(array("Pisa",$databpjs["pisa"]));?>
         <?=$t->row(array("PWKT From",$databpjs["pkwt_from"]));?>
         <?=$t->row(array("Basic Salary",$databpjs["basic_salary"]));?>
         <?=$t->row(array("NIK",$databpjs["ktp"]));?>
         <?=$t->row(array("No BPJS",$databpjs["bpjs_id"]));?>
         <?=$t->row(array("Email",$databpjs["email"]));?>
         <?=$t->row(array("Remarks",$databpjs["remarks"]));?>
         <?=$t->row(array("Info To Employer At",$databpjs["info_to_empl_at"]));?>
	<?=$t->end();?>
	<?php $_SERVER["QUERY_STRING"] = str_replace("id=".$_GET["id"]."&","",$_SERVER["QUERY_STRING"]); ?>
	<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."';\"");?>

<?php include_once "../footer.php";?>