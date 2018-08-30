<?php
	include_once "win_head.php";
	
	$db->addtable($_tablename);
	if($_POST["keyword"] != "") $db->awhere(
															$_id_field." = '".$_POST["keyword"]."' 
															OR ".$_caption_field." LIKE '%".$_POST["keyword"]."%' 
															OR ktp LIKE '%".$_POST["keyword"]."%' 
															OR phone LIKE '%".$_POST["keyword"]."%' 
															OR code LIKE '%".$_POST["keyword"]."%' 
															OR sex LIKE '%".$_POST["keyword"]."%'"
													);
	$db->limit(1000);
	$db->order($_caption_field);
	$_data = $db->fetch_data(true);
?>
<h3><b><?=$_title;?></b></h3>
<?php
	if($_GET["addnew"]==1 || isset($_POST["saving_new"])){
		include_once "win_candidate_add.php";
	} else {
		echo $f->input("addnew","Add New Candidate","type='button' onclick=\"window.location='?addnew=1&".$_SERVER["QUERY_STRING"]."';\"");
	}
?>
<br><br>
<?=$f->start("","POST","?".$_SERVER["QUERY_STRING"]);?>
Search : <?=$f->input("keyword",$_POST["keyword"],"size='50'");?>&nbsp;<?=$f->input("search","Load","type='submit'");?>
<?=$f->end();?>
<br>
<?=$t->start("","data_content");?>
<?=$t->header(array("No","Code","Candidate Name","KTP","Sex"));?>
<?php 
	foreach($_data as $no => $data){
		$actions = "onclick=\"parent_load('".$_name."','".$data[$_id_field]."','".$data[$_caption_field]."');\"";
		echo $t->row(array($no+1,$data["code"],$data[$_caption_field],$data["ktp"],$data["sex"]),array("align='right' valign='top' ".$actions,"align='right' valign='top' ".$actions,$actions,$actions,$actions));
	} 
?>
<?=$t->end();?>