<?php include_once "head.php";?>
<?php include_once "prf_js.php";?>
<div class="bo_title">Add PRF</div>
<?php
	$departement = $db->fetch_single_data("users","job_division",array("id"=>$__user_id));
	if(isset($_POST["save"])){
		$code_number = "%/".date("m/Y");
		$code_number = $db->fetch_single_data("prf","code_number",array("departement"=>$departement,"code_number" => $code_number.":LIKE"),array("code_number DESC"));
		if($code_number == ""){
			$code_number = "001/".date("m/Y");
		} else {
			$code_number = (str_replace(date("m/Y"),"",$code_number) * 1) + 1;
			$code_number = substr("000",0,3 - strlen($code_number)).$code_number."/".date("m/Y");
		}
		
		if($_POST["cost_center_code"] != ""){
			$departement = $db->fetch_single_data("cost_centers","departement",["code"=>$_POST["cost_center_code"]]);
		}
		
		$db->addtable("prf");
		$db->addfield("code");			$db->addvalue($_POST["code"]);
		$db->addfield("code_number");	$db->addvalue($code_number);
        $db->addfield("departement");	$db->addvalue($departement);
		$db->addfield("cost_center_code");$db->addvalue($_POST["cost_center_code"]);
        $db->addfield("nominal");		$db->addvalue($_POST["nominal"]);
        $db->addfield("payment_method");$db->addvalue($_POST["payment_method"]);
        $db->addfield("payment_to");	$db->addvalue($_POST["payment_to"]);
        $db->addfield("bank_name");		$db->addvalue($_POST["bank_name"]);
        $db->addfield("bank_account");	$db->addvalue($_POST["bank_account"]);
        $db->addfield("purpose");		$db->addvalue($_POST["purpose"]);
        $db->addfield("description");	$db->addvalue($_POST["description"]);
        $db->addfield("prf_mode");		$db->addvalue($_POST["prf_mode"]);
        $db->addfield("maker_at");		$db->addvalue($_POST["maker_at"]);
        $db->addfield("maker_by");		$db->addvalue($__username);
        $db->addfield("checker_by");	$db->addvalue($_POST["checker_by"]);
        $db->addfield("checker_at");	$db->addvalue("0000-00-00");
        $db->addfield("signer_by");		$db->addvalue($_POST["signer_by"]);
        $db->addfield("signer_at");		$db->addvalue("0000-00-00");
		if($_POST["approve_by"] != ""){
			$db->addfield("approve_by");		$db->addvalue($_POST["approve_by"]);
			$db->addfield("approve_at");		$db->addvalue("0000-00-00");
		}
		$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");	$db->addvalue($__username);
		$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$prf_id = $inserting["insert_id"];
			if($_FILES["attachment"]["tmp_name"]){
				$_ext = strtolower(pathinfo($_FILES['attachment']['name'],PATHINFO_EXTENSION));
				$attachment_name = "attachment_".$prf_id."_".rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).".".$_ext;
				move_uploaded_file($_FILES['attachment']['tmp_name'],"../indottech/prf_attachments/".$attachment_name);
				$db->addtable("prf");			$db->where("id",$prf_id);
				$db->addfield("attachment");	$db->addvalue($attachment_name);
				$db->update();
			}
			if($_POST["checker_by"] != ""){
				$checker_id = $db->fetch_single_data("users","id",["email" => $_POST["checker_by"]]);
				$checker_name = $db->fetch_single_data("users","name",["id" => $checker_id]);
				$maker_name = $db->fetch_single_data("users","name",["id" => $__user_id]);
				$purpose = $_POST["purpose"];
				$nominal = format_amount($_POST["nominal"]);
				$message = "<a href=\"?sender_id=$__user_id\">$maker_name</a> telah membuat PRF untuk <b>$purpose</b> sebesar <b>Rp. $nominal</b> dan menunggu Anda untuk melakukan `chekcer`. Silakan klik <a href=\"prf_view.php?id=$prf_id\" target=\"_BLANK\">link ini</a> untuk melihat PRF tersebut.";
				sendMessage("0",$checker_id,$message);
			}
			
			if($_POST["signer_by"] != ""){
				$signer_id = $db->fetch_single_data("users","id",["email" => $_POST["signer_by"]]);
				$signer_name = $db->fetch_single_data("users","name",["id" => $signer_id]);
				$maker_name = $db->fetch_single_data("users","name",["id" => $__user_id]);
				$message = "<a href=\"?sender_id=$__user_id\">$maker_name</a> telah membuat PRF untuk <b>$purpose</b> sebesar <b>Rp. $nominal</b> dan menunggu Anda untuk melakukan `signer`. Silakan klik <a href=\"prf_view.php?id=$prf_id\" target=\"_BLANK\">link ini</a> untuk melihat PRF tersebut.";
				sendMessage("0",$signer_id,$message);
			}
			
			javascript("alert('Data Saved');");
			javascript("window.location='prf_view.php?id=".$prf_id."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
    $txt_code = $f->input("code",$_POST["code"],"");
	
	$sel_cost_center = $f->select("cost_center_code",$db->fetch_select_data("cost_centers","code","concat('[',code,'] ',name)",[],[],"",true),$_POST["cost_center_code"],"onchange='load_checker(this.value);'");
    $txt_nominal = $f->input("nominal",$_POST["nominal"],"type='number' onblur='load_checker(cost_center_code.value,this.value);'");
	$sel_payment_method = $f->select("payment_method",array(""=>"","1"=>"Cheque","2"=>"Bilyet Giro","3"=>"Transfer","4"=>"Cash"),$_POST["payment_method"]);
	$txt_payment_to = $f->input("payment_to",$_POST["payment_to"]);
	$txt_bank_name = $f->input("bank_name",$_POST["bank_name"]);
	$txt_bank_account = $f->input("bank_account",$_POST["bank_account"]);
	$txt_purpose = $f->textarea("purpose",$_POST["purpose"],"style='width:400px;height:30px;'");
	$txt_description = $f->textarea("description",$_POST["description"],"style='width:400px;height:100px;'");
	$sel_prf_mode = $f->select("prf_mode",array("1"=>"Normal","2"=>"Reimburse","3"=>"Advance"),$_POST["prf_mode"]);
	
	$txt_attachment = $f->input("attachment","","type='file'");
	if($_POST["maker_at"] == "") $_POST["maker_at"] = date("Y-m-d");
	$txt_maker_at = $f->input("maker_at",$_POST["maker_at"],"type='date' readonly");
	$sel_checker = $f->select("checker_by",$db->fetch_select_data("users","email","name",[],["name"],"",true),$_POST["checker_by"]);
	$sel_signer = $f->select("signer_by",$db->fetch_select_data("users","email","name",[],["name"],"",true),$_POST["signer_by"]);
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<input type="hidden" name="approve_by" id="approve_by">
        <?=$t->row(array("PRF Code",$txt_code));?>
        <?=$t->row(array("Code Number","<i>Auto generate</i>"));?>
        <?=$t->row(array("Cost Center",$sel_cost_center));?>
        <?=$t->row(array("Nominal Amount",$txt_nominal));?>
        <?=$t->row(array("Payment's Method",$sel_payment_method));?>
        <?=$t->row(array("Payment To",$txt_payment_to));?>
        <?=$t->row(array("Bank Name",$txt_bank_name));?>
        <?=$t->row(array("Bank Account Number (No.Rekening)",$txt_bank_account));?>
        <?=$t->row(array("Payment's Purpose",$txt_purpose));?>
        <?=$t->row(array("Note",$txt_description));?>
        <?=$t->row(array("PRF Mode",$sel_prf_mode));?>
        <?=$t->row(array("Attachment",$txt_attachment));?>
        <?=$t->row(array("Request Date",$txt_maker_at));?>
        <?=$t->row(array("Checker",$sel_checker));?>
        <?=$t->row(array("Signer",$sel_signer));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>