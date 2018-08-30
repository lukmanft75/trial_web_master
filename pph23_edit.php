<?php include_once "head.php";?>
<div class="bo_title">Edit PPh 23</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("pph23");					$db->where("id",@$_GET["id"]);
		$db->addfield("client_id");				$db->addvalue($_POST["client_id"]);
		$db->addfield("no_invoices");			$db->addvalue($_POST["no_invoices"]);
		$db->addfield("tgl_potong");			$db->addvalue($_POST["tgl_potong"]);
		$db->addfield("no_potong");				$db->addvalue($_POST["no_potong"]);
		$db->addfield("nominal");				$db->addvalue($_POST["nominal"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			$pph23_id = $_GET["id"];
			if($_FILES["softcopy"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
				$softcopy_name = $pph23_id.".".$_ext;
				move_uploaded_file($_FILES['softcopy']['tmp_name'],"files_pph23/".$softcopy_name);
				$db->addtable("pph23");			$db->where("id",$pph23_id);
				$db->addfield("softcopy");		$db->addvalue($softcopy_name);
				$db->update();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("pph23");$db->where("id",$_GET["id"]);$db->limit(1);$pph23 = $db->fetch_data();
	
	$client_id 				= $f->input("wcc_no",$wcc["wcc_no"],"size='25'");
	$po_no 					= $f->input("po_no",$wcc["po_no"],"size='25'");
	$storage_position 		= $f->input("storage_position",$wcc["storage_position"]);
	$softcopy 				= $wcc["softcopy"]."<br>".$f->input("softcopy","","type='file'");
	
	$client_id 				= $f->select("client_id",$db->fetch_select_data("clients","id","name",array(),array("name")),$pph23["client_id"],"style='height:25px'");
	$no_invoices			= $f->input("no_invoices",$pph23["no_invoices"],"size='50'");
	$tgl_potong				= $f->input("tgl_potong",$pph23["tgl_potong"],"type='date'");
	$no_potong				= $f->input("no_potong",$pph23["no_potong"],"size='50'");
	$nominal				= $f->input("nominal",$pph23["nominal"],"type='number'");
	$softcopy 				= $f->input("softcopy","","type='file'");
?>

<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Client",$client_id));?>
         <?=$t->row(array("Invoices No",$no_invoices));?>
         <?=$t->row(array("Tgl Bukti Potong",$tgl_potong));?>
         <?=$t->row(array("No Bukti Potong",$no_potong));?>
         <?=$t->row(array("Nominal",$nominal));?>
         <?=$t->row(array("Softcopy File",$softcopy));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>