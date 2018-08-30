<?php include_once "head.php";?>
<div class="bo_title">Add PPn</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("ppn");	
		$db->addfield("client_id");				$db->addvalue($_POST["client_id"]);
		$db->addfield("no_invoices");			$db->addvalue($_POST["no_invoices"]);
		$db->addfield("storage_position");		$db->addvalue($_POST["storage_position"]);
		$db->addfield("vat_no");				$db->addvalue($_POST["vat_no"]);
		$db->addfield("nominal");				$db->addvalue($_POST["nominal"]);
		$db->addfield("created_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");			$db->addvalue($__username);
		$db->addfield("created_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$ppn_id = $inserting["insert_id"];
			if($_FILES["softcopy"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
				$softcopy_name = $ppn_id.".".$_ext;
				move_uploaded_file($_FILES['softcopy']['tmp_name'],"files_ppn/".$softcopy_name);
				$db->addtable("ppn");			$db->where("id",$ppn_id);
				$db->addfield("softcopy");		$db->addvalue($softcopy_name);
				$db->update();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$client_id 				= $f->select("client_id",$db->fetch_select_data("clients","id","name",array(),array("name")),"","style='height:25px'");
	$no_invoices			= $f->input("no_invoices","","size='50'");
	$storage_position		= $f->input("storage_position","","size='50'");
	$vat_no					= $f->input("vat_no","","size='50'");
	$nominal				= $f->input("nominal","","type='number'");
	$softcopy 				= $f->input("softcopy","","type='file'");
?>

<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Client",$client_id));?>
         <?=$t->row(array("No Invoices",$no_invoices));?>
         <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
         <?=$t->row(array("VAT No",$vat_no));?>
         <?=$t->row(array("Nominal",$nominal));?>
         <?=$t->row(array("Softcopy File",$softcopy));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>