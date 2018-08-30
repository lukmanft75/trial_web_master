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
<div class="bo_title">Add Journal</div>
<?php
	if(isset($_POST["save"])){
		$_debit = 0; $_kredit = 0;
		foreach($_POST["coa"] as $key => $arr_coa){
			$_debit += $_POST["debit"][$key];
			$_kredit += $_POST["kredit"][$key];
		}
		if($_debit == $_kredit && $_debit > 0){
			$db->addtable("jurnals");
			$db->addfield("tanggal");			$db->addvalue($_POST["tanggal"]);
			$db->addfield("invoice_id");		$db->addvalue($_POST["invoice_id"]);
			$db->addfield("invoice_num");		$db->addvalue($_POST["invoice_num"]);
			$db->addfield("description");		$db->addvalue($_POST["description"]);
			$db->addfield("currency_id");		$db->addvalue($_POST["currency_id"]);
			$db->addfield("bank_id");			$db->addvalue($_POST["bank_id"]);
			$db->addfield("status");		    $db->addvalue("1");
			$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("created_by");		$db->addvalue($__username);
			$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");		$db->addvalue($__username);
			$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$inserting = $db->insert();
			if($inserting["affected_rows"] >= 0){
				$jurnal_id = $inserting["insert_id"];
				foreach($_POST["coa"] as $key => $arr_coa){
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue($_POST["coa"][$key]);
					$db->addfield("description");	$db->addvalue($_POST["description_detail"][$key]);
					$db->addfield("debit");			$db->addvalue($_POST["debit"][$key]);
					$db->addfield("kredit");		$db->addvalue($_POST["kredit"][$key]);
					$inserting = $db->insert();
				}
				javascript("alert('Data Saved');");
				javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
			} else {
				javascript("alert('Saving data failed');");
			}
		} else {
			javascript("alert('Jumlah Debit dan Kredit harus balance dan harus lebih besar dari 0');");
		}
	}
	
    $txt_tanggal = $f->input("tanggal",$_POST["tanggal"],"type='date'");
	$txt_invoice_num = $f->input("invoice_num",$_POST["invoice_num"]);
	$txt_description = $f->input("description",$_POST["description"],"style='width:600px;'");
	$sel_currencies = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as name"),"");
	$sel_bank = $f->select("bank_id",$db->fetch_select_data("banks","id","concat(name,' (',no_rek,')') as bank",array(),array("name,no_rek"),"",true),$_POST["bank_id"]);
	
	$plusminbutton = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('detail_area','row_detail_');\"")."&nbsp;";
	$plusminbutton .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('detail_area','row_detail_');\"");
	
	$sel_coa = $f->select("coa[0]",$db->fetch_select_data("coa","coa","concat(coa,' - ',description) as coa_desc"),"");
	$txt_description_detail = $f->input("description_detail[0]","","style='width:300px;'");
	$txt_debit = $f->input("debit[0]","","type='number' step='0.01' onkeyup=\"loadTotal('detail_area');\"");
	$txt_credit = $f->input("kredit[0]","","type='number' step='0.01' onkeyup=\"loadTotal('detail_area');\"");
	
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
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php
	foreach($_POST["coa"] as $key => $coa){
		?><script>
			document.getElementById("coa[<?=$key;?>]").value = "<?=$coa;?>";
			document.getElementById("description_detail[<?=$key;?>]").value = "<?=str_replace(['"',chr(10),chr(13)],[""," "," "],$_POST["description_detail"][$key]);?>";
			document.getElementById("debit[<?=$key;?>]").value = "<?=$_POST["debit"][$key];?>";
			document.getElementById("kredit[<?=$key;?>]").value = "<?=$_POST["kredit"][$key];?>";
			adding_row('detail_area','row_detail_');
		</script><?php
	}
?>
<?php include_once "footer.php";?>