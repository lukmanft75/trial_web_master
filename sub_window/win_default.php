<?php
	include_once "win_head.php";
	
	if($_POST["add"] && $_POST["newvalue"] != ""){
		$db->addtable($_tablename);
		$db->addfield($_caption_field);$db->addvalue($_POST["newvalue"]);
		$db->addfield("created_at");$db->addvalue(date("Y-m-d H:i:S"));
		$db->addfield("created_by");$db->addvalue($__username);
		$db->addfield("created_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");$db->addvalue(date("Y-m-d H:i:S"));
		$db->addfield("updated_by");$db->addvalue($__username);
		$db->addfield("updated_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] > 0){
			?> <script> parent_load('<?=$_name;?>','<?=$inserting["insert_id"];?>','<?=$_POST["newvalue"];?>'); </script> <?php
		}
	}
	
	$db->addtable($_tablename);
	$db->addfield($_id_field);
	$db->addfield($_caption_field);
	if($_POST["keyword"] != "") $db->awhere($_id_field." = '".$_POST["keyword"]."' OR ".$_caption_field." LIKE '%".$_POST["keyword"]."%'");
	$db->limit(1000);
	$db->order($_caption_field);
	$_data = $db->fetch_data(true);
?>
<h3><b><?=$_title;?></b></h3>
<?=$f->start("","POST","?".$_SERVER["QUERY_STRING"]);?>
Search : <?=$f->input("keyword",$_POST["keyword"],"size='50'");?>&nbsp;<?=$f->input("search","Load","type='submit'");?>
<?=$f->end();?>
<br>
<?=$t->start("","data_content");?>
<?=$t->header(array("No","ID","Value"));?>
<?php 
	$btnadd = $f->input("add","Add New","type='submit'");
	$newvalue = $f->input("newvalue",$_POST["newvalue"],"size='50'");
	echo $f->start("","POST","?".$_SERVER["QUERY_STRING"]);
	echo $t->row(array($btnadd,$newvalue),array("colspan='2' align='center'",""));
	echo $f->end();
	foreach($_data as $no => $data){
		$actions = "onclick=\"parent_load('".$_name."','".$data[$_id_field]."','".$data[$_caption_field]."');\"";
		echo $t->row(array($no+1,$data[$_id_field],$data[$_caption_field]),array("align='right' valign='top' ".$actions,"align='right' valign='top' ".$actions,$actions));
	} 
?>
<?=$t->end();?>