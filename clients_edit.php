<?php include_once "head.php";?>
<div class="bo_title">Edit Clients</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("clients");$db->where("id",$_GET["id"]);
		$db->addfield("pic");			$db->addvalue($_POST["pic"]);
		$db->addfield("name");			$db->addvalue($_POST["name"]);
		$db->addfield("description");	$db->addvalue($_POST["description"]);
		$db->addfield("address");		$db->addvalue($_POST["address"]);
		$db->addfield("email");			$db->addvalue($_POST["email"]);
		$db->addfield("website");		$db->addvalue($_POST["website"]);
		$db->addfield("phone");			$db->addvalue($_POST["phone"]);
		$db->addfield("fax");			$db->addvalue($_POST["fax"]);
		$db->addfield("zipcode");		$db->addvalue($_POST["zipcode"]);
		$db->addfield("tax_address");	$db->addvalue($_POST["tax_address"]);
		$db->addfield("tax_no");		$db->addvalue($_POST["tax_no"]);
		$db->addfield("tax_zipcode");	$db->addvalue($_POST["tax_zipcode"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$updating = $db->update();
		if($updating["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$db->addtable("clients");$db->where("id",$_GET["id"]);$db->limit(1);$client = $db->fetch_data();
	$txt_pic = $f->input("pic",$client["pic"],"style='width:500px;'");
	$txt_name = $f->input("name",$client["name"],"style='width:500px;'");
	$txt_description = $f->input("description",$client["description"],"style='width:500px;'");
	$txt_address = $f->input("address",$client["address"],"style='width:500px;'");
	$txt_email = $f->input("email",$client["email"],"style='width:500px;'");
	$txt_website = $f->input("website",$client["website"],"style='width:500px;'");
	$txt_phone = $f->input("phone",$client["phone"],"style='width:500px;'");
	$txt_fax = $f->input("fax",$client["fax"],"style='width:500px;'");
	$txt_zipcode = $f->input("zipcode",$client["zipcode"],"style='width:500px;'");
	$txt_tax_address = $f->input("tax_address",$client["tax_address"],"style='width:500px;'");
	$txt_tax_no = $f->input("tax_no",$client["tax_no"],"style='width:500px;'");
	$txt_tax_zipcode = $f->input("tax_zipcode",$client["tax_zipcode"],"style='width:500px;'");
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("PIC",$txt_pic));?>
		<?=$t->row(array("Company Name",$txt_name));?>
		<?=$t->row(array("Company Description",$txt_description));?>
		<?=$t->row(array("Address",$txt_address));?>
		<?=$t->row(array("Email",$txt_email));?>
		<?=$t->row(array("Website",$txt_website));?>
		<?=$t->row(array("Phone",$txt_phone));?>
		<?=$t->row(array("Fax",$txt_fax));?>
		<?=$t->row(array("Zipcode",$txt_zipcode));?>
		<?=$t->row(array("Tax Address",$txt_tax_address));?>
		<?=$t->row(array("Tax Number",$txt_tax_no));?>
		<?=$t->row(array("Tax Zipcode",$txt_tax_zipcode));?>
		
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>