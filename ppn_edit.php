<?php include_once "head.php";?>
<div class="bo_title">Edit PPn</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("ppn");					$db->where("id",@$_GET["id"]);
		$db->addfield("client_id");				$db->addvalue($_POST["client_id"]);
		$db->addfield("no_invoices");			$db->addvalue($_POST["no_invoices"]);
		$db->addfield("storage_position");		$db->addvalue($_POST["storage_position"]);
		$db->addfield("vat_no");				$db->addvalue($_POST["vat_no"]);
		$db->addfield("nominal");				$db->addvalue($_POST["nominal"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			$ppn_id = $_GET["id"];
			if($_FILES["softcopy"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
				$softcopy_name = $ppn_id.".".$_ext;
				move_uploaded_file($_FILES['softcopy']['tmp_name'],"files_ppn/".$softcopy_name);
				$db->addtable("ppn");			$db->where("id",$ppn_id);
				$db->addfield("softcopy");		$db->addvalue($softcopy_name);
				$db->update();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("ppn");$db->where("id",$_GET["id"]);$db->limit(1);$ppn = $db->fetch_data();
	
	$client_id 				= $f->input("wcc_no",$wcc["wcc_no"],"size='25'");
	$po_no 					= $f->input("po_no",$wcc["po_no"],"size='25'");
	$storage_position 		= $f->input("storage_position",$wcc["storage_position"]);
	$softcopy 				= $wcc["softcopy"]."<br>".$f->input("softcopy","","type='file'");
	
	$client_id 				= $f->select("client_id",$db->fetch_select_data("clients","id","name",array(),array("name")),$ppn["client_id"],"style='height:25px'");
	$no_invoices			= $f->input("no_invoices",$ppn["no_invoices"],"size='50'");
	$storage_position		= $f->input("storage_position",$ppn["storage_position"],"size='50'");
	$vat_no					= $f->input("vat_no",$ppn["vat_no"],"size='50'");
	$nominal				= $f->input("nominal",$ppn["nominal"],"type='number'");
	$softcopy 				= $f->input("softcopy","","type='file'");
?>

<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Client",$client_id));?>
         <?=$t->row(array("Invoices No",$no_invoices));?>
         <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
         <?=$t->row(array("VAT No",$vat_no));?>
         <?=$t->row(array("Nominal",$nominal));?>
         <?=$t->row(array("Softcopy File",$softcopy));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>