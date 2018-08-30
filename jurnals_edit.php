<?php include_once "head.php";?>
<script>
	function loadTotal(detail_area){
		var elm_detail_area = document.getElementById(detail_area);
		var numrow = elm_detail_area.childElementCount;
		var Tdebit = 0;
		var Tcredit = 0;
		var Nbalance = 0;
		for (ii = 0; ii < numrow; ii++){
			Tdebit += (document.getElementById("debit[" + ii + "]").value * 1);
			Tcredit += (document.getElementById("kredit[" + ii + "]").value * 1);
		}
		Nbalance = Tdebit - Tcredit;
		document.getElementById("totDebit").innerHTML = Tdebit.formatMoney(2, '.', ',');
		document.getElementById("totCredit").innerHTML = Tcredit.formatMoney(2, '.', ',');
		document.getElementById("balance").innerHTML = Nbalance.formatMoney(2, '.', ',');
		return 1;
	}
</script>
<div class="bo_title">Edit Journal</div>
<?php
	$unbalanced = false;
	if(!$_GET["referrer"]){
		$_referrer = str_replace("_edit","_list",$_SERVER["PHP_SELF"]);
	} else {
		$_referrer = $_GET["referrer"];
	}
	if(isset($_POST["save"]) || isset($_POST["approving"])){
		$_debit = 0; $_kredit = 0;
		foreach($_POST["coa"] as $key => $arr_coa){
			$_debit += $_POST["debit"][$key];
			$_kredit += $_POST["kredit"][$key];
		}
		if($_debit == $_kredit && $_debit > 0){
			$db->addtable("jurnals");			$db->where("id",$_GET["id"]);
			$db->addfield("tanggal");			$db->addvalue($_POST["tanggal"]);
			$db->addfield("invoice_id");		$db->addvalue($_POST["invoice_id"]);
			$db->addfield("invoice_num");		$db->addvalue($_POST["invoice_num"]);
			$db->addfield("description");		$db->addvalue($_POST["description"]);
			$db->addfield("currency_id");		$db->addvalue($_POST["currency_id"]);
			$db->addfield("bank_id");			$db->addvalue($_POST["bank_id"]);
			$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");		$db->addvalue($__username);
			$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$updating = $db->update();
			if($updating["affected_rows"] >= 0){
				$transaction_id = $db->fetch_single_data("jurnals","transaction_id",array("id" => $_GET["id"]));
				if($transaction_id > 0){
					$db->addtable("transactions");$db->where("id",$transaction_id);
					$db->addfield("bank_id");	$db->addvalue($_POST["bank_id"]);
					$db->update();
				}
				
				$jurnal_id = $_GET["id"];
				$db->addtable("jurnal_details");
				$db->where("jurnal_id",$jurnal_id);
				$db->delete_();
				foreach($_POST["coa"] as $key => $arr_coa){
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue($_POST["coa"][$key]);
					$db->addfield("description");	$db->addvalue($_POST["description_detail"][$key]);
					$db->addfield("debit");			$db->addvalue($_POST["debit"][$key]);
					$db->addfield("kredit");		$db->addvalue($_POST["kredit"][$key]);
					$inserting = $db->insert();
				}
				
				if(isset($_POST["approving"]) && $_isapprovaler){
					$db->addtable("jurnals");		$db->where("id",$_GET["id"]);				
					$db->addfield("isapproved");	$db->addvalue(1);
					$db->addfield("approved_at");	$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("approved_by");	$db->addvalue($__username);
					$db->addfield("approved_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
					$updating = $db->update();
					
				}
				
				javascript("alert('Data Saved');");
				javascript("window.location='".$_referrer."';");
			} else {
				javascript("alert('Saving data failed');");
			}
		} else {
			$unbalanced = true;
			javascript("alert('Jumlah Debit dan Kredit harus balance dan harus lebih besar dari 0');");
		}
	}
	
	$db->addtable("jurnals");	$db->where("id",$_GET["id"]);	$db->limit(1);	$jurnal = $db->fetch_data();
	
    $txt_tanggal = $f->input("tanggal",$jurnal["tanggal"],"type='date'");
	$txt_invoice_num = $f->input("invoice_num",$jurnal["invoice_num"]);
	$txt_description = $f->input("description",htmlentities($jurnal["description"]),"style='width:600px;'");
	$sel_currencies = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as name"),$jurnal["currency_id"]);
	$sel_bank = $f->select("bank_id",$db->fetch_select_data("banks","id","concat(name,' (',no_rek,')') as bank",array(),array("name,no_rek"),"",true),$jurnal["bank_id"]);
	
	$plusminbutton = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('detail_area','row_detail_');\"")."&nbsp;";
	$plusminbutton .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('detail_area','row_detail_');\"");
	
	$sel_coa = $f->select("coa[0]",$db->fetch_select_data("coa","coa","concat(coa,' - ',description) as coa_desc"),"");
	$txt_description_detail = $f->input("description_detail[0]","","style='width:300px;'");
	$txt_debit = $f->input("debit[0]","","type='number' step='0.01' onkeyup=\"loadTotal('detail_area');\"");
	$txt_credit = $f->input("kredit[0]","","type='number' step='0.01' onkeyup=\"loadTotal('detail_area');\"");
	$_isapprovaler = true;
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Journal Date",$txt_tanggal));?>
        <?=$t->row(array("Invoice No",$txt_invoice_num));?>
        <?=$t->row(array("Description",$txt_description));?>
        <?=$t->row(array("Currency",$sel_currencies));?>
        <?=$t->row(array("Bank",$sel_bank));?>
	<?=$t->end();?>
	
	<?=$t->start("width='100%'","detail_area","editor_content_2");?>
        <?=$t->row(array($plusminbutton."<br>No.","COA","Description","Debit","Credit"),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"));?>
		<?=$t->row(array("<div id=\"firstno\">1</div>",$sel_coa,$txt_description_detail,$txt_debit,$txt_credit),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"),"id=\"row_detail_0\"");?>
	<?=$t->end();?>
	<br>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("<b>Total Debit</b>","<div id='totDebit'>0</div>"),["","align='right' style='font-weight:bolder;width:150px;'"]);?>
        <?=$t->row(array("<b>Total Credit</b>","<div id='totCredit'>0</div>"),["","align='right' style='font-weight:bolder;'"]);?>
        <?=$t->row(array("<b>Balance</b>","<div id='balance'>0</div>"),["","align='right' style='font-weight:bolder;'"]);?>
	<?=$t->end();?>
	<br>
	<!--
	<?php if($_isapprovaler && !$jurnal["isapproved"]){ ?>
		<?=$f->input("approving","Approving & Saving","type='submit'");?> 
	<?php } ?>
	<?php if($jurnal["isapproved"]){ echo "<b>Approved</b>";}?>
	-->
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".$_referrer."';\"");?>
<?=$f->end();?>
<?php
	if(!$unbalanced){
		$db->addtable("jurnal_details");	$db->where("jurnal_id",$_GET["id"]);
		$jurnal_details = $db->fetch_data(true);
		foreach($jurnal_details as $key => $jurnal_detail){
			?><script>
				document.getElementById("coa[<?=$key;?>]").value = "<?=$jurnal_detail["coa"];?>";
				document.getElementById("description_detail[<?=$key;?>]").value = "<?=str_replace(['"',chr(10),chr(13)],[""," "," "],$jurnal_detail["description"]);?>";
				document.getElementById("debit[<?=$key;?>]").value = "<?=$jurnal_detail["debit"];?>";
				document.getElementById("kredit[<?=$key;?>]").value = "<?=$jurnal_detail["kredit"];?>";
				adding_row('detail_area','row_detail_');
			</script><?php
		}
	} else {
		foreach($_POST["coa"] as $key => $coa){
			?><script>
				document.getElementById("coa[<?=$key;?>]").value = "<?=$coa;?>";
				document.getElementById("description_detail[<?=$key;?>]").value = "<?=str_replace(['"',chr(10),chr(13)],[""," "," "],$_POST["description_detail"][$key]);?>";
				document.getElementById("debit[<?=$key;?>]").value = "<?=$_POST["debit"][$key];?>";
				document.getElementById("kredit[<?=$key;?>]").value = "<?=$_POST["kredit"][$key];?>";
				adding_row('detail_area','row_detail_');
			</script><?php
		}
	}
?>
<script> loadTotal("detail_area"); </script>
<?php include_once "footer.php";?>