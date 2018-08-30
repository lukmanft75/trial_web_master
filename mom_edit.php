<?php include_once "head.php";?>
<div class="bo_title">Edit MOM Document</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("mom_documents");			$db->where("id",@$_GET["id"]);
		$db->addfield("name");					$db->addvalue($_POST["name"]);
		$db->addfield("storage_position");		$db->addvalue($_POST["storage_position"]);
		$db->addfield("received_by_indohr_at");	$db->addvalue($_POST["received_by_indohr_at"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			$mom_document_id = $_GET["id"];
			if($_FILES["softcopy"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
				$softcopy_name = $mom_document_id.".".$_ext;
				move_uploaded_file($_FILES['softcopy']['tmp_name'],"files_mom_indotech/".$softcopy_name);
				$db->addtable("mom_documents");	$db->where("id",$mom_document_id);
				$db->addfield("softcopy");		$db->addvalue($softcopy_name);
				$db->update();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("mom_documents");$db->where("id",$_GET["id"]);$db->limit(1);$mom_document = $db->fetch_data();
	
	$name 					= $f->input("name",$mom_document["name"],"size='50'");
	$storage_position 		= $f->input("storage_position",$mom_document["storage_position"]);
	$softcopy 				= $mom_document["softcopy"]."<br>".$f->input("softcopy","","type='file'");
	$received_by_indohr_at 	= $f->input("received_by_indohr_at",$mom_document["received_by_indohr_at"],"type='date'");
?>

<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Name",$name));?>
         <?=$t->row(array("Document Storage Position/Code",$storage_position));?>
         <?=$t->row(array("Received At",$received_by_indohr_at));?>
         <?=$t->row(array("Softcopy File",$softcopy));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>