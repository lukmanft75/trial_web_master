<?php
	include_once "win_head.php";
	echo $data["id"];
	if(isset($_POST["saving_new"])){
		$db->addtable("candidate_relations");$db->where("id",$_GET["id"]);
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("relation_id");		$db->addvalue($_POST["relation_id"]);
		$db->addfield("fullname");			$db->addvalue($_POST["fullname"]);
		$db->addfield("division");			$db->addvalue($_POST["division"]);
		$db->addfield("location");			$db->addvalue($_POST["location"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] > 0) echo "<font color='green'><b>Data saved</b></font><br><br>";
		
	}
	
	$db->addtable("candidate_relations");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$data["candidate_id"]));
	$relation_id = $f->select("relation_id",$db->fetch_select_data("relations","id","name"),$data["relation_id"]);
	$fullname = $f->input("fullname",$data["fullname"]);
	$division = $f->input("division",$data["division"]);
	$location = $f->input("location",$data["location"]);
	
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
	<?php $_SERVER["QUERY_STRING"] = str_replace("id=".$_GET["id"]."&","",$_SERVER["QUERY_STRING"]); ?>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."?".$_SERVER["QUERY_STRING"]."';\"");?>
<?=$f->end();?>