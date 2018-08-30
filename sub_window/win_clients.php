<?php
	include_once "win_head.php";
	
	$db->addtable($_tablename);
	if($_POST["keyword"] != "") $db->awhere(
										$_id_field ." = '".$_POST["keyword"]."'
										OR name LIKE '%".$_POST["keyword"]."%'"
									);
	$db->limit(1000);
	$db->order("created_at DESC");
	$_data = $db->fetch_data(true);
?>
<script>
	function parent_load(client_id,client_name,attn){
		parent.document.getElementById("<?=$_GET["name"];?>").value = client_id;
		parent.document.getElementById("sw_caption_<?=$_GET["name"];?>").innerHTML = client_name;
		try{
			if(attn != ""){ parent.document.getElementById("attn").value = attn; }
		}catch(e){}
		parent.$.fancybox.close();
	}
</script>
<h3><b><?=$_title;?></b></h3>
<br><br>
<?=$f->start("","POST","?".$_SERVER["QUERY_STRING"]);?>
Search : <?=$f->input("keyword",$_POST["keyword"],"size='50'");?>&nbsp;<?=$f->input("search","Load","type='submit'");?>
<?=$f->end();?>
<br>
<?=$t->start("","data_content");?>
<?=$t->header(array("No","Client ID","Company Name","NPWP"));?>
<?php 
	foreach($_data as $no => $data){
		$actions = "onclick=\"parent_load('".$data["id"]."','".$data["name"]."','".$data["pic"]."');\"";
		
		echo $t->row(array($no+1,$data["id"],$data["name"],$data["tax_no"]),array("align='right' valign='top' ".$actions,"valign='top' ".$actions,"valign='top' ".$actions,"valign='top' ".$actions));
	} 
?>
<?=$t->end();?>