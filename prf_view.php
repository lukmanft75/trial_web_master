<?php 
	if($_GET["print"] == 1) $__print = true;
	if($__print) include_once "common.php";
	else  include_once "head.php";
	
	if($_POST["upload"] && $_GET["id"] > 0){
		if($_FILES["proof_of_payment"]["tmp_name"]){
			$prf_id = $_GET["id"];
			$_ext = strtolower(pathinfo($_FILES['proof_of_payment']['name'],PATHINFO_EXTENSION));
			$attachment_name = "proof_of_payment_".$prf_id."_".rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).".".$_ext;
			move_uploaded_file($_FILES['proof_of_payment']['tmp_name'],"../indottech/prf_attachments/".$attachment_name);
			$db->addtable("prf");			$db->where("id",$prf_id);
			$db->addfield("proof_of_payment");	$db->addvalue($attachment_name);
			$db->update();
		}
	}
	
	if($_GET["journaling"] == "1"){
		if($_GET["coa"] != ""){
			$db->addtable("prf");		$db->where("id",$_GET["id"]);
			$db->addfield("coa"); 		$db->addvalue($_GET["coa"]);
			$db->addfield("jurnal_at"); $db->addvalue($__now);
			$db->addfield("jurnal_by"); $db->addvalue($__username);
			$updating = $db->update();
			$paid_by = $db->fetch_single_data("prf","paid_by",["id" => $_GET["id"]]);
			$prf_code = $db->fetch_single_data("prf","code",["id" => $_GET["id"]]);
			if($paid_by != ""){
				$paid_at = $db->fetch_single_data("prf","paid_at",["id" => $_GET["id"]]);
				$db->addtable("transactions");	$db->where("description","%{prf_id:".$_GET["id"]."%","s","LIKE");$db->delete_();
				$db->addtable("jurnals");		$db->where("description","%{prf_id:".$_GET["id"]."%","s","LIKE");$db->delete_();
				$db->addtable("jurnal_details");$db->where("description","%{prf_id:".$_GET["id"]."%","s","LIKE");$db->delete_();
				$coaDebit = $_GET["coa"];
				$db->addPrfJournal($_GET["id"],$paid_at,$prf_code);
			}
		}
	}
	
	if($_GET["approving"] != ""){
		$db->addtable("prf");
		if($_GET["prfcode"] && $_GET["approving"] == "paid"){
			$db->addfield("code"); 			$db->addvalue($_GET["prfcode"]);
			$db->addfield("paid_status_at");$db->addvalue($__now);
			$__now = $_GET["prfdate"];
		}
		$db->addfield($_GET["approving"]."_at"); $db->addvalue($__now);
		$db->addfield($_GET["approving"]."_by"); $db->addvalue($__username);
		$db->where("id",$_GET["id"]);
		$updating = $db->update();
		if($updating["affected_rows"] > 0 && $_GET["approving"] == "paid"){
			if($db->addPrfJournal($_GET["id"],$_GET["prfdate"],$_GET["prfcode"]) == 1){
				javascript("alert('PRF Paid are success');");
				javascript("window.location='prf_list.php';");
			} else {
				javascript("alert('This PRF has no COA Setting, please Journaling this PRF first');");
			}
		}
		
		if($updating["affected_rows"] > 0){
			$checker_at = $db->fetch_single_data("prf","checker_at",["id" => $_GET["id"]]);
			$signer_at = $db->fetch_single_data("prf","signer_at",["id" => $_GET["id"]]);
			if($checker_at != "0000-00-00" && $signer_at != "0000-00-00"){
				$purpose = $db->fetch_single_data("prf","purpose",["id" => $_GET["id"]]);
				$nominal = format_amount($db->fetch_single_data("prf","nominal",["id" => $_GET["id"]]));
				$maker_by = $db->fetch_single_data("prf","maker_by",["id" => $_GET["id"]]);
				$maker_id = $db->fetch_single_data("users","id",["email" => $maker_by]);
				$maker_name = $db->fetch_single_data("users","name",["id" => $maker_id]);
				$prf_id = $_GET["id"];
				$message = "<a href=\"?sender_id=$maker_id\">$maker_name</a> telah membuat PRF untuk <b>$purpose</b> sebesar <b>Rp. $nominal</b> dan sudah di `checker` dan `signer` harap Tim Finance Follow Up PRF ini. Silakan klik <a href=\"prf_view.php?id=$prf_id\" target=\"_BLANK\">link ini</a> untuk melihat PRF tersebut.";
				sendMessage("0","5",$message);
				sendMessage("0","7",$message);
				sendMessage("0","8",$message);
			}
		}
		echo "<font color='blue'>".strtoupper($_GET["approving"])." Success!</font>";
	}
	
	$db->addtable("prf");$db->where("id",$_GET["id"]);$db->limit(1);$prf = $db->fetch_data();
	$prf_mode[1] = "Normal";
	$prf_mode[2] = "Reimburse";
	$prf_mode[3] = "Advance";
	$prf_mode[$prf["prf_mode"]] = "<b>".$prf_mode[$prf["prf_mode"]]."</b>";
	$maker_by_name = $db->fetch_single_data("users","name",array("email"=>$prf["maker_by"]));
	$checker_by_name = $db->fetch_single_data("users","name",array("email"=>$prf["checker_by"]));
	$signer_by_name = $db->fetch_single_data("users","name",array("email"=>$prf["signer_by"]));
	$finance_by_name = $db->fetch_single_data("users","name",array("email"=>$prf["finance_by"]));
	$accounting_by_name = $db->fetch_single_data("users","name",array("email"=>$prf["accounting_by"]));
	$approve_by_name = $db->fetch_single_data("users","name",array("email"=>$prf["approve_by"]));
	
	$maker_by_position = $db->fetch_single_data("users","job_division",array("email"=>$prf["maker_by"]));
	$checker_by_position = $db->fetch_single_data("users","job_division",array("email"=>$prf["checker_by"]));
	$signer_by_position = $db->fetch_single_data("users","job_division",array("email"=>$prf["signer_by"]));
	$finance_by_position = $db->fetch_single_data("users","job_division",array("email"=>$prf["finance_by"]));
	$accounting_by_position = $db->fetch_single_data("users","job_division",array("email"=>$prf["accounting_by"]));
	$approve_by_position = $db->fetch_single_data("users","job_division",array("email"=>$prf["approve_by"]));
	
	$departement = $maker_by_position;
	if($prf["payment_method"] == "1"){ $cheque_payment_to = "<b>".$prf["payment_to"]."</b>"; }
	if($prf["payment_method"] == "2"){ $bilyet_payment_to = "<b>".$prf["payment_to"]."</b>"; }
	if($prf["payment_method"] == "3"){ 
		$transfer_payment_to = "<b>".$prf["payment_to"]."</b>";
		$detail_bank = "<b>".$prf["bank_name"]." - ".$prf["bank_account"]."</b>";
	}
	if($prf["payment_method"] == "4"){ $cash_payment = " <font size='3'>&#10004;</font> "; }
	
	if($prf["deduct_type"] == "1") $deduct_type = "PPh 21";
	if($prf["deduct_type"] == "2") $deduct_type = "PPh 23";
	if($prf["deduct_type"] == "3") $deduct_type = "Other";
?>
<style>
	.sign_area{
		border-spacing :0;
		border-collapse :collapse;
		letter-spacing:2px;
		width:800px;
	}
	
	.sign_area td{
		border: 1px solid black;
		vertical-align:top;
		width:33.3%
	}
</style>
<table style="border-bottom:5px solid black;width:800px;">
	<tr>
		<td width="20%"><img src="images/corphr.png"></td>
		<td width="60%" align="center" valign="middle">
			<span style="font-size:28px;font-weight:bolder;">PT. Indo Human Resource</span><br>
			Epicentrum Walk Office Lt.7 Unit 0709A Jl. HR Rasuna Said Kuningan, Jakarta
		</td>
		<td width="20%" align="right"><div style="border:2px solid black;width:100px;height:40px;text-align:center;font-size:20px;font-weight:bold;"><div style="margin-top:7px;"><?=$prf["code"];?></div></div></td>
	</tr>
</table>
<table width="800">
	<tr>
		<td width="70%" style="font-size:16px;font-weight:bolder;letter-spacing: 2px;">PAYMENT REQUEST FORM</td>
		<td width="30%" style="font-size:10px;">
			<table>
				<tr><td>Code Number</td><td>: <?=$prf["code_number"];?></td></tr>
				<tr><td>Departement</td><td>: <?=$prf["departement"];?></td></tr>
				<tr style="font-size:12px;font-weight:bolder;"><td>Request Date</td><td>: <?=format_tanggal($prf["maker_at"],"d/m/Y");?></td></tr>
			</table>
		</td>
	</tr>
</table>
<table style="letter-spacing:2px;width:800px;">
	<tr>
		<td nowrap width="150" valign="bottom"><b>Nominal Amount</b></td><td nowrap width="1" valign="bottom"><b> : </b></td><td style="font-size:16px;font-weight:bolder;">Rp. <?=format_amount($prf["nominal"]);?></td>
	</tr>
	<?php if($prf["deduct_nominal"] > 0){ ?>
	<tr>
		<td align="right">Deduct [<?=$deduct_type;?>]</td>
		<td> : </td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rp. <?=format_amount($prf["deduct_nominal"]);?></td>
	</tr>
	<tr>
		<td nowrap width="150" valign="bottom" align="right"><b>Total</b></td><td nowrap width="1" valign="bottom"><b> : </b></td><td style="font-size:16px;font-weight:bolder;">Rp. <?=format_amount($prf["nominal"] - $prf["deduct_nominal"]);?></td>
	</tr>
	<?php } ?>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td nowrap valign="top"><b>Payment's Method</b></td>
		<td nowrap width="1" valign="top"><b> : </b></td>
		<td>
			<?php if($prf["payment_method"] == "1"){ ?> <b>Cheque</b> Payment to: <?=$cheque_payment_to;?> <?php } ?>
			<?php if($prf["payment_method"] == "2"){ ?> <b>Bilyet Giro</b> Payment to: <?=$bilyet_payment_to;?> <?php } ?>
			<?php if($prf["payment_method"] == "3"){ ?> <b>Transfer </b> Payment to: <?=$transfer_payment_to;?> <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Detail Bank A/C <?=$detail_bank;?><br>
			<?php } ?>
			<?php if($prf["payment_method"] == "4"){ ?> <b>Cash Payment <?php } ?>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td nowrap valign="top"><b>Payment's Purpose</b></td><td nowrap width="1" valign="top"><b> : </b></td><td style="font-weight:bolder;"><?=str_replace(chr(10),"<br>",$prf["purpose"]);?></td>
	</tr>
	<tr><td colspan="3" align="right">(<?=$prf_mode[3];?> / <?=$prf_mode[2];?> / <?=$prf_mode[1];?>)</td></tr>
</table>
<table class="sign_area">
	<tr style="font-weight:bold;text-align:center;height:70px;">
		<td>Maker</td>
		<td>Checker</td>
		<td>Signer</td>
	</tr>
	<tr>
		<td><div style="width:70px;display:inline-block">Name</div> : <?=$maker_by_name;?></td>
		<td><div style="width:70px;display:inline-block">Name</div> : <?=$checker_by_name;?></td>
		<td><div style="width:70px;display:inline-block">Name</div> : <?=$signer_by_name;?></td>
	</tr>
	<tr>
		<td><div style="width:70px;display:inline-block">Position</div> : <?=$maker_by_position;?></td>
		<td><div style="width:70px;display:inline-block">Position</div> : <?=$checker_by_position;?></td>
		<td><div style="width:70px;display:inline-block">Position</div> : <?=$signer_by_position;?></td>
	</tr>
	<tr>
		<td><div style="width:70px;display:inline-block">Date</div> : <?=format_tanggal($prf["maker_at"],"dFY");?></td>
		<td><div style="width:70px;display:inline-block">Date</div> : <?=format_tanggal($prf["checker_at"],"dFY");?></td>
		<td><div style="width:70px;display:inline-block">Date</div> : <?=format_tanggal($prf["signer_at"],"dFY");?></td>
	</tr>
	<tr style="font-weight:bold;text-align:center;height:70px;">
		<td>Finance</td>
		<td>Accounting</td>
		<td>Approve</td>
	</tr>
	<tr>
		<td><div style="width:70px;display:inline-block">Name</div> : <?=$finance_by_name;?></td>
		<td><div style="width:70px;display:inline-block">Name</div> : <?=$accounting_by_name;?></td>
		<td><div style="width:70px;display:inline-block">Name</div> : <?=$approve_by_name;?></td>
	</tr>
	<tr>
		<td><div style="width:70px;display:inline-block">Position</div> : <?=$finance_by_position;?></td>
		<td><div style="width:70px;display:inline-block">Position</div> : <?=$accounting_by_position;?></td>
		<td><div style="width:70px;display:inline-block">Position</div> : <?=$approve_by_position;?></td>
	</tr>
	<tr>
		<td><div style="width:70px;display:inline-block">Date</div> : <?=format_tanggal($prf["finance_at"],"dFY");?></td>
		<td><div style="width:70px;display:inline-block">Date</div> : <?=format_tanggal($prf["accounting_at"],"dFY");?></td>
		<td><div style="width:70px;display:inline-block">Date</div> : <?=format_tanggal($prf["approve_at"],"dFY");?></td>
	</tr>
</table>
<br>
<table style="border:1px solid black;width:800px;">
<tr><td style="height:140px;vertical-align:top;">
	<b><u>Note :</u></b>
	<br><?=str_replace(chr(10),"<br>",$prf["description"]);?>
	<?php if($prf["cost_center_code"] != ""){ ?>
	<br><br><i>Cost Center : </i> <?=$db->fetch_single_data("cost_centers","concat('[',code,'] ',name)",["code" => $prf["cost_center_code"]]);?>
	<?php } ?>
</td></tr>
</table>
<br>
<script>
	function toogle_proof_of_payment_div(){
		var bo_filter_container = document.getElementById('bo_filter_container'),
		style = window.getComputedStyle(bo_filter_container),
		bo_filter_container_display = style.getPropertyValue('display');
		if(bo_filter_container_display == "none") {
			bo_filter_container.style.display="block";
			bo_expand.innerHTML="[-] Hide Upload Proof of Payment";
		}
		if(bo_filter_container_display == "block") {
			bo_filter_container.style.display="none";
			bo_expand.innerHTML="[+] View Upload Proof of Payment";
		}
	}
	
	function paid_click(){
		modalBody = "<table>";
		modalBody += "<tr>";
		modalBody += "<td><b>PRF Date : </b></td>";
		modalBody += "<td><input type='date' id='prfdate' value='<?=substr($__now,0,10);?>'></td>";
		modalBody += "</tr>";
		modalBody += "<tr>";
		modalBody += "<td><b>PRF CODE : </b></td>";
		modalBody += "<td>";
		modalBody += "<select id='prfcode'>";
		modalBody += "<option value=''>-pilih prf code-</option>";
		<?php
			$prf_codes = $db->fetch_all_data("coa",["prf_code"],"prf_code <> ''","prf_code");
			foreach($prf_codes as $prf_code){
		?>
		modalBody += "<option value='<?=$prf_code["prf_code"];?>'><?=$prf_code["prf_code"];?></option>";
		<?php } ?>
		modalBody += "</select>";
		modalBody += "</td>";
		modalBody += "</tr>";
		modalFooter = "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location='?id=<?=$_GET["id"];?>&approving=paid&prfcode='+prfcode.value+'&prfdate='+prfdate.value;\">OK</button>";
		modalFooter += "<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>";
		$('#modalTitle').html("PRF's Paying");
		$('#modalBody').html(modalBody);
		$('#modalFooter').html(modalFooter);
		$('#myModal').modal('show');
	}
	
	function jurnal_click(){
		modalBody = "<table>";
		modalBody += "<tr>";
		modalBody += "<td><b>COA: </b></td>";
		modalBody += "<td>";
		modalBody += "<select id='coa'>";
		modalBody += "<option value=''>-pilih coa-</option>";
		<?php
			$coas = $db->fetch_all_data("coa",[],"","coa");
			foreach($coas as $coa){
		?>
		modalBody += "<option value='<?=$coa["coa"];?>'><?=$coa["coa"];?> -- <?=$coa["description"];?></option>";
		<?php } ?>
		modalBody += "</select>";
		modalBody += "</td>";
		modalBody += "</tr>";
		modalFooter = "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location='?id=<?=$_GET["id"];?>&journaling=1&coa='+coa.value;\">OK</button>";
		modalFooter += "<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>";
		$('#modalTitle').html("PRF's Journaling");
		$('#modalBody').html(modalBody);
		$('#modalFooter').html(modalFooter);
		$('#myModal').modal('show');
	}
</script>
<?php
	if(!$__print){
		if($prf["checker_at"] == "0000-00-00") echo $f->input("checker","Checked","type='button' onclick=\"window.location='?id=".$_GET["id"]."&approving=checker';\"")."&nbsp;";
		if($prf["signer_at"] == "0000-00-00") echo $f->input("signer","Signed","type='button' onclick=\"window.location='?id=".$_GET["id"]."&approving=signer';\"")."&nbsp;";
		if($__group_id <= 4) {
			if($prf["finance_by"] == "") echo $f->input("finance_ok","Finance OK","type='button' onclick=\"window.location='?id=".$_GET["id"]."&approving=finance';\"")."&nbsp;";
			if($prf["accounting_by"] == "") echo $f->input("accounting_ok","Accounting OK","type='button' onclick=\"window.location='?id=".$_GET["id"]."&approving=accounting';\"")."&nbsp;";
			if($prf["approve_by"] == "" || $prf["approve_at"] == "0000-00-00") echo $f->input("approved","Approved","type='button' onclick=\"window.location='?id=".$_GET["id"]."&approving=approve';\"")."&nbsp;";
			// if($prf["paid_by"] == "" && $prf["jurnal_by"] != "" && $prf["coa"]!="") 
			if($prf["paid_by"] == "") 
				echo $f->input("paid","Paid","type='button' onclick=\"paid_click();\"")."&nbsp;";
			if($prf["jurnal_by"] == "" || $prf["coa"]=="") 
				echo $f->input("jurnal","Journaling","type='button' onclick=\"jurnal_click();\"")."&nbsp;";
		}
		if($prf["paid_by"] != "") {
			?>
			<div id="bo_expand" onclick="toogle_proof_of_payment_div();">[+] View Upload Proof of Payment</div>
			<div id="bo_filter">
				<div id="bo_filter_container">
					<form method="POST" action="?id=<?=$_GET["id"];?>" enctype="multipart/form-data">
						Choose Proof of Payment File: <?=$f->input("proof_of_payment","","type='file'");?><br><br>
						<?=$f->input("upload","Upload","type='submit'");?>
					</form>
				</div>
			</div>
			<?php
		}
		echo "<br><br>";
		echo $f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"")."&nbsp;";
		echo $f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"")."&nbsp;";
		echo $f->input("print","Print","type='button' onclick=\"window.open('prf_view.php?print=1&id=".$_GET["id"]."','_blank');\"");
		if($prf["attachment"] != ""){
			echo $f->input("attachment","View Attachment","type='button' onclick=\"window.open('../indottech/prf_attachments/".$prf["attachment"]."');\"")."&nbsp;";
		}
		if($prf["proof_of_payment"] != ""){
			echo $f->input("proof_of_payment","View Proof of Payment","type='button' onclick=\"window.open('../indottech/prf_attachments/".$prf["proof_of_payment"]."');\"")."&nbsp;";
		}
		if($prf["settlement"] != ""){
			echo $f->input("settlement","View Settlement","type='button' onclick=\"window.open('../indottech/prf_attachments/".$prf["settlement"]."');\"")."&nbsp;";
		}
		echo "<br><br>";
		include_once "footer.php";
	} else {
		?>		
			<script>
				window.print();
				setTimeout(window.close, 0);
			</script>
		<?php
	}
?>