<?php include_once "head.php";?>
<?php include_once "prf_js.php";?>
<?php
	if($db->fetch_single_data("prf","paid_by",array("id" => $_GET["id"])) != ""){
		if($__group_id > 4){
			javascript("alert('This PRF has Paid, You`re not allow to edit this PRF');");
			javascript("window.location='prf_list.php';");
		} else {
			$onlyFinance = "readonly";
			$onlyFinance2 = "disabled";
		}
	}
	if($__group_id > 4 && $__username != $db->fetch_single_data("prf","created_by",array("id"=>$_GET["id"]))){
		javascript("alert('You`re not allow to update this document');");
		javascript("window.location='prf_list.php';");
		exit();
	}
	if($__group_id > 4 
		&& ($db->fetch_single_data("prf","checker_at",array("id" => $_GET["id"])) != "0000-00-00" || $db->fetch_single_data("prf","signer_at",array("id" => $_GET["id"])) != "0000-00-00" )
		&& $__username != $db->fetch_single_data("prf","created_by",array("id"=>$_GET["id"]))
	){
		javascript("alert('This PRF has Checked or Signed, You`re not allow to edit this PRF');");
		javascript("window.location='prf_list.php';");
		exit();
	}
?>
<div class="bo_title">Edit PRF</div>
<?php
	if(isset($_POST["save"])){
		$prf_created_by = $db->fetch_single_data("prf","created_by",array("id"=>$_GET["id"]));
		$db->addtable("prf");			$db->where("id",$_GET["id"]);
		$db->addfield("code");			$db->addvalue($_POST["code"]);
		$db->addfield("cost_center_code");$db->addvalue($_POST["cost_center_code"]);
        $db->addfield("nominal");		$db->addvalue($_POST["nominal"]);
        $db->addfield("deduct_type");	$db->addvalue($_POST["deduct_type"]);
        $db->addfield("deduct_nominal");$db->addvalue($_POST["deduct_nominal"]);
        $db->addfield("payment_method");$db->addvalue($_POST["payment_method"]);
        $db->addfield("payment_to");	$db->addvalue($_POST["payment_to"]);
        $db->addfield("bank_name");		$db->addvalue($_POST["bank_name"]);
        $db->addfield("bank_account");	$db->addvalue($_POST["bank_account"]);
        $db->addfield("purpose");		$db->addvalue($_POST["purpose"]);
        $db->addfield("description");	$db->addvalue($_POST["description"]);
        $db->addfield("prf_mode");		$db->addvalue($_POST["prf_mode"]);
        $db->addfield("maker_at");		$db->addvalue($_POST["maker_at"]);
		if($__group_id > 4 || $__username == $prf_created_by){
			$db->addfield("checker_by");	$db->addvalue($_POST["checker_by"]);
			$db->addfield("checker_at");	$db->addvalue("0000-00-00");
			$db->addfield("signer_by");		$db->addvalue($_POST["signer_by"]);
			$db->addfield("signer_at");		$db->addvalue("0000-00-00");
			if($_POST["approve_by"] != ""){
				$db->addfield("approve_by");		$db->addvalue($_POST["approve_by"]);
				$db->addfield("approve_at");		$db->addvalue("0000-00-00");
			}
		}
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->update();
		if($inserting["affected_rows"] >= 0){		
			$prf_id = $_GET["id"];		
			if($_FILES["attachment"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['attachment']['name'],PATHINFO_EXTENSION));
				$attachment_name = "attachment_".$prf_id."_".rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).".".$_ext;
				move_uploaded_file($_FILES['attachment']['tmp_name'],"../indottech/prf_attachments/".$attachment_name);
				$db->addtable("prf");			$db->where("id",$prf_id);
				$db->addfield("attachment");	$db->addvalue($attachment_name);
				$db->update();
			}
			
			javascript("alert('Data Saved');");
			javascript("window.location='prf_view.php?id=".$_GET["id"]."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$notallowchange = "";
	if($__group_id <= 4 && $__username != $db->fetch_single_data("prf","created_by",array("id"=>$_GET["id"]))){
		$notallowchange = " readonly";
	}
	
	$db->addtable("prf");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
    // $txt_code = $f->input("code",$data["code"],"");
    $txt_code = $f->select("code",$db->fetch_select_data("coa","prf_code","concat(prf_code) as name",["prf_code"=>":<>"],["prf_code"],"",true),$data["code"]);

	$sel_cost_center = $f->select("cost_center_code",$db->fetch_select_data("cost_centers","code","concat('[',code,'] ',name)",[],[],"",true),$data["cost_center_code"],"onchange='load_checker(this.value);'");
    $txt_nominal = $f->input("nominal",$data["nominal"],"$onlyFinance type='number' onblur='load_checker(cost_center_code.value,this.value);' ".$notallowchange);
	$sel_deduct_type = $f->select("deduct_type",array(""=>"","1"=>"PPh 21","2"=>"PPh 23","3"=>"Other"),$data["deduct_type"],$onlyFinance2);
    $txt_deduct_nominal = $f->input("deduct_nominal",$data["deduct_nominal"],"$onlyFinance type='number'");
	$sel_payment_method = $f->select("payment_method",array(""=>"","1"=>"Cheque","2"=>"Bilyet Giro","3"=>"Transfer","4"=>"Cash"),$data["payment_method"],$onlyFinance2);
	$txt_payment_to = $f->input("payment_to",$data["payment_to"],$onlyFinance);
	$txt_bank_name = $f->input("bank_name",$data["bank_name"],$onlyFinance);
	$txt_bank_account = $f->input("bank_account",$data["bank_account"],$onlyFinance);
	$txt_purpose = $f->textarea("purpose",$data["purpose"],"style='width:400px;height:30px;'".$notallowchange);
	$txt_description = $f->textarea("description",$data["description"],"style='width:400px;height:100px;'");
	$sel_prf_mode = $f->select("prf_mode",array("1"=>"Normal","2"=>"Reimburse","3"=>"Advance"),$data["prf_mode"],$onlyFinance2);
	$txt_attachment = "";
	if($data["attachment"] != ""){ $txt_attachment = "<i>".$data["attachment"]."</i><br>"; }
	$txt_attachment .= $f->input("attachment","","$onlyFinance2 type='file'");
	$txt_maker_at = $f->input("maker_at",$data["maker_at"],"type='date' readonly");
	$sel_checker = $f->select("checker_by",$db->fetch_select_data("users","email","name",[],["name"],"",true),$data["checker_by"],$onlyFinance2);
	$sel_signer = $f->select("signer_by",$db->fetch_select_data("users","email","name",[],["name"],"",true),$data["signer_by"],$onlyFinance2);
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<input type="hidden" name="approve_by" id="approve_by">
        <?=$t->row(array("PRF Code",$txt_code));?>
        <?=$t->row(array("Code Number","<b>".$data["code_number"]."</b>"));?>
        <?=$t->row(array("Cost Center",$sel_cost_center));?>
        <?=$t->row(array("Nominal Amount",$txt_nominal));?>
        <?=$t->row(array("Deduct",$sel_deduct_type." ".$txt_deduct_nominal));?>
        <?=$t->row(array("Payment's Method",$sel_payment_method));?>
        <?=$t->row(array("Payment To",$txt_payment_to));?>
        <?=$t->row(array("Bank Name",$txt_bank_name));?>
        <?=$t->row(array("Bank Account Number (No.Rekening)",$txt_bank_account));?>
        <?=$t->row(array("Payment's Purpose",$txt_purpose));?>
        <?=$t->row(array("Note",$txt_description));?>
        <?=$t->row(array("PRF Mode",$sel_prf_mode));?>
        <?=$t->row(array("Attachment",$txt_attachment));?>
        <?=$t->row(array("Request Date",$txt_maker_at));?>
        <?=$t->row(array("Maker By",$data["maker_by"]));?>
        <?=$t->row(array("Checker",$sel_checker));?>
        <?=$t->row(array("Signer",$sel_signer));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> 
	<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
	<?=$f->input("view","View","type='button' onclick=\"window.location='prf_view.php?id=".$_GET["id"]."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>