<?php include_once "head.php";?>
<div class="bo_title">Edit Banks</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("banks");			$db->where("id",$_GET["id"]);
		$db->addfield("code");			$db->addvalue($_POST["code"]);
		$db->addfield("coa");			$db->addvalue($_POST["coa"]);
		$db->addfield("name");			$db->addvalue($_POST["name"]);
        $db->addfield("no_rek");		$db->addvalue($_POST["no_rek"]);
        $db->addfield("currency_id");	$db->addvalue($_POST["currency_id"]);
        $db->addfield("kurs");			$db->addvalue($_POST["kurs"]);
        $db->addfield("is_debt");		$db->addvalue($_POST["is_debt"]);
        $db->addfield("description");	$db->addvalue($_POST["description"]);
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
	
	$db->addtable("banks");$db->where("id",$_GET["id"]);$db->limit(1);$bank = $db->fetch_data();
	$txt_code = $f->input("code",$bank["code"]);
	$sel_coa = $f->select("coa",$db->fetch_select_data("coa","coa","concat(coa,' - ',description) as coa_desc"),$bank["coa"]);
	$txt_name = $f->input("name",$bank["name"]);
    $txt_no_rek = $f->input("no_rek",$bank["no_rek"]);
    $sel_currency_id = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as id2"),$bank["currency_id"]);
    $txt_kurs = $f->input("kurs",$bank["kurs"]);
    $sel_is_debt = $f->select("is_debt",array("0" => "No","1" => "Yes"));
    $txt_description = $f->input("description",$bank["description"]);
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Code",$txt_code));?>
        <?=$t->row(array("COA",$sel_coa));?>
        <?=$t->row(array("Name",$txt_name));?>
        <?=$t->row(array("No Rek",$txt_no_rek));?>
        <?=$t->row(array("Currency ID",$sel_currency_id));?>
        <?=$t->row(array("Kurs",$txt_kurs));?>
        <?=$t->row(array("Is Debt",$sel_is_debt));?>
        <?=$t->row(array("Description",$txt_description));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>