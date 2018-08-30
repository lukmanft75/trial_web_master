<?php
	include_once "win_head.php";
	$_title = "Positions";
	$_tablename = "positions";
	$_id_field = "id";
	$_caption_field = "name";
	
	$candidate_id = $db->fetch_single_data("all_data_update","candidate_id",array("id" => $_GET["all_data_update_id"]));
	
	if($_GET["remove"]){
		$position_ids = $db->fetch_single_data("all_data_update","position_ids",array("id" => $_GET["all_data_update_id"]));
		$position_ids = str_replace ("|".$_GET["remove"]."|","",$position_ids);
		$db->addtable("all_data_update"); 	$db->where("id",$_GET["all_data_update_id"]);
		$db->addfield("position_ids");		$db->addvalue($position_ids);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->update();
		
	}
	
	if($_GET["add"]){
		$position_ids = $db->fetch_single_data("all_data_update","position_ids",array("id" => $_GET["all_data_update_id"]))."|".$_GET["add"]."|";
		$db->addtable("all_data_update"); 	$db->where("id",$_GET["all_data_update_id"]);
		$db->addfield("position_ids");		$db->addvalue($position_ids);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->update();
		
	}
?>
<h3><b><?=$_title;?></b></h3>
<br><br>
<h3><b>Positions History</b></h3>
<?=$t->start("","data_content");?>
	<?=$t->header(array("No","Positions Name","Action"));?>
	<?php
		$position_ids = pipetoarray($db->fetch_single_data("all_data_update","position_ids",array("id" => $_GET["all_data_update_id"])));
		foreach($position_ids as $no => $position_id){
			$positions = $db->fetch_single_data("positions","name",array("id" => $position_id));
			$action = $f->input("remove","Remove","type='button' onclick=\"window.location='?remove=".$position_id."&all_data_update_id=".$_GET["all_data_update_id"]."';\"");
			echo $t->row(array($no+1,$positions,$action),array("align='right' valign='top'","valign='top'"));
		}
	?>
<?=$t->end();?>
<?=$f->input("close","Close","type='button' onclick=\"parent.window.location = parent.window.location;\"");?>

<br><br>
<?php
	$db->addtable($_tablename);
	if($_POST["keyword"] != "") $db->awhere(
										$_id_field." = '".$_POST["keyword"]."' 
										OR ".$_caption_field." LIKE '%".$_POST["keyword"]."%'"
								);
	$db->limit(1000);
	$db->order($_caption_field);
	$_data = $db->fetch_data(true);
?>
<?=$f->start("","POST","?".$_SERVER["QUERY_STRING"]);?>
	Search : <?=$f->input("keyword",$_POST["keyword"],"size='50'");?>&nbsp;<?=$f->input("search","Load","type='submit'");?>
	<br>
	<?=$t->start("","data_content");?>
		<?=$t->header(array("No","Positions Name","Action"));?>
		<?php 
			foreach($_data as $no => $data){
				$action = $f->input("add","Add","type='button' onclick=\"window.location='?add=".$data[$_id_field]."&all_data_update_id=".$_GET["all_data_update_id"]."';\"");
				echo $t->row(array($no+1,$data[$_caption_field],$action),array("align='right' valign='top'","valign='top'","valign='top'"));
			} 
		?>
	<?=$t->end();?>
<?=$f->end();?>