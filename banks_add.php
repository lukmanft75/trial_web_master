<?php include_once "head.php";?>
<div class="bo_title">Add Banks</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("banks");
		$db->addfield("code");			$db->addvalue($_POST["code"]);
		$db->addfield("coa");			$db->addvalue($_POST["coa"]);
		$db->addfield("name");			$db->addvalue($_POST["name"]);
        $db->addfield("no_rek");		$db->addvalue($_POST["no_rek"]);
        $db->addfield("currency_id");	$db->addvalue($_POST["currency_id"]);
        $db->addfield("kurs");			$db->addvalue($_POST["kurs"]);
        $db->addfield("is_debt");		$db->addvalue($_POST["is_debt"]);
        $db->addfield("description");	$db->addvalue($_POST["description"]);
        $db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));	
        $db->addfield("created_by");	$db->addvalue($__username);	
        $db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
        $db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));	
        $db->addfield("updated_by");	$db->addvalue($__username);	
        $db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);	
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$txt_code = $f->input("code",$_POST["code"]);
	$sel_coa = $f->select("coa",$db->fetch_select_data("coa","coa","concat(coa,' - ',description) as coa_desc"),@$_POST["coa"]);
	$txt_name = $f->input("name",$_POST["name"]);
    $txt_no_rek = $f->input("no_rek",$_POST["no_rek"]);
    $sel_currency_id = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as id2"));
    $txt_kurs = $f->input("kurs",$_POST["kurs"]);
    $sel_is_debt = $f->select("is_debt",array("0" => "No","1" => "Yes"));
    $txt_description = $f->input("description",$_POST["description"]);
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Code",$txt_code));?>
        <?=$t->row(array("COA",$sel_coa));?>
        <?=$t->row(array("Name",$txt_name));?>
        <?=$t->row(array("No Rek",$txt_no_rek));?>
        <?=$t->row(array("Currency ID",$sel_currency_id));?>
        <?=$t->row(array("Is Debt",$sel_is_debt));?>
        <?=$t->row(array("Description",$txt_description));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>