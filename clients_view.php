<?php include_once "head.php";?>
<div class="bo_title">View Clients</div>

<?php
	$db->addtable("clients");$db->where("id",$_GET["id"]);$db->limit(1);$client = $db->fetch_data();
	
?>
<?=$t->start("","editor_content");?>
		
		<?=$t->row(array("PIC",					$client["pic"]));?>
		<?=$t->row(array("Company Name",		$client["name"]));?>
		<?=$t->row(array("Company Description",	$client["description"]));?>
		<?=$t->row(array("Address",				$client["address"]));?>
		<?=$t->row(array("Email",				$client["email"]));?>
		<?=$t->row(array("Website",				$client["website"]));?>
		<?=$t->row(array("Phone",				$client["phone"]));?>
		<?=$t->row(array("Fax",					$client["fax"]));?>
		<?=$t->row(array("Zipcode",				$client["zipcode"]));?>
		<?=$t->row(array("Tax Address",			$client["tax_address"]));?>
		<?=$t->row(array("Tax Number",			$client["tax_number"]));?>
		<?=$t->row(array("Tax Zipcode",			$client["tax_zipcode"]));?>
		
	<?=$t->end();?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>