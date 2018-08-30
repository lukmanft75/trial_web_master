<?php include_once "head.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php include_once "invoice_script.php";?>
<div class="bo_title">Add Invoice</div>
<?php
	$copy_id = $_GET["copy_id"];
	if(isset($_POST["save"])){
		$_existed_num = $db->fetch_single_data("invoice","id",array("num" => $_POST["num"]));
		if($_existed_num <= 0){
			$tot_reimbursement = 0;
			$tot_fee = 0;
			foreach($_POST["reimbursement"] as $key => $reimbursement){
				if(!$_POST["after_tax_rate"][$key]){
					$tot_reimbursement += $reimbursement;
					$tot_fee += $_POST["fee"][$key];
				}
			}
			
			if($_POST["immediate"]) $_POST["due_date"] = -1;
			
			$db->addtable("invoice");
			$db->addfield("num");				$db->addvalue($_POST["num"]);
			$db->addfield("issue_at");			$db->addvalue($_POST["issue_at"]);
			$db->addfield("due_date");			$db->addvalue($_POST["due_date"]);
			$db->addfield("division_id");		$db->addvalue($_POST["division_id"]);
			$db->addfield("client_id");		    $db->addvalue($_POST["client_id"]);
			$db->addfield("currency_id");		$db->addvalue($_POST["currency_id"]);
			$db->addfield("description");		$db->addvalue($_POST["description"]);
			$db->addfield("additional_detail");	$db->addvalue($_POST["additional_detail"]);
			$db->addfield("billing_periode");	$db->addvalue($_POST["billing_periode"]);
			$db->addfield("po_no");			    $db->addvalue($_POST["po_no"]);
			
			if($_POST["is_vat_1"] && $_POST["is_vat_2"]){ $vat = ($tot_reimbursement + $tot_fee) * 0.1; }
			else if($_POST["is_vat_1"]){ $vat = $tot_reimbursement * 0.1; }
			else if($_POST["is_vat_2"]){ $vat = $tot_fee * 0.1; }
			else { $vat = 0; }
			$db->addfield("vat");				$db->addvalue($vat);			
			
			if($_POST["is_tax23_1"] && $_POST["is_tax23_2"]){ $tax23 = ($tot_reimbursement + $tot_fee) * -0.02; }
			else if($_POST["is_tax23_1"]){ $tax23 = $tot_reimbursement * -0.02; }
			else if($_POST["is_tax23_2"]){ $tax23 = $tot_fee * -0.02; }
			else { $tax23 = 0; }
			$db->addfield("tax23");				$db->addvalue($tax23);
			
			$total = $tot_reimbursement + $tot_fee + $vat + $tax23;
				
			foreach($_POST["reimbursement"] as $key => $reimbursement){
				if($_POST["after_tax_rate"][$key]){
					$total += $reimbursement + $_POST["fee"][$key];
				}
			}
			
			$db->addfield("total_po");			$db->addvalue($tot_reimbursement + $tot_fee);
			$db->addfield("total");				$db->addvalue($total);
			$db->addfield("inwords");			$db->addvalue(convert_number_to_words($total));
			
			$print_config = "|".$_POST["show_attn"]."|".$_POST["digit_year_4"]."|".$_POST["due_date_format"]."|".$_POST["show_detail_attached"]."|".$_POST["attn"]."|";
			$vat_mode = bindec(($_POST["is_vat_1"] * 1).($_POST["is_vat_2"] * 1));
			$tax23_mode = bindec(($_POST["is_tax23_1"] * 1).($_POST["is_tax23_2"] * 1));
			
			$db->addfield("print_config");		$db->addvalue($print_config);
			$db->addfield("vat_mode");			$db->addvalue($vat_mode);
			$db->addfield("tax23_mode");		$db->addvalue($tax23_mode);
			$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("created_by");		$db->addvalue($__username);
			$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");		$db->addvalue($__username);
			$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$inserting = $db->insert();
			if($inserting["affected_rows"] >= 0){
				$invoice_id = $inserting["insert_id"];
				
				$id_wcc = $db->fetch_single_data("wcc","id",array("po_no" => $_POST["po_no"].":LIKE"));
				$db->addtable("wcc");
				$db->addfield("wcc_no");		$db->addvalue($_POST["wcc_no"]);
				$db->addfield("po_no");			$db->addvalue($_POST["po_no"]);
				$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");	$db->addvalue($__username);
				$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				if($id_wcc > 0){
					$db->where("id",$id_wcc);
					$inserting = $db->update();					
				} else {
					$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("created_by");	$db->addvalue($__username);
					$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
					$inserting = $db->insert();					
				}
				
				//insert invoice_detail
				foreach($_POST["invoice_description"] as $key => $description){
					$db->addtable("invoice_detail");
					$db->addfield("invoice_id");		$db->addvalue($invoice_id);
					$db->addfield("invoice_num");		$db->addvalue($_POST["num"]);
					$db->addfield("po_id");				$db->addvalue($po_id);
					$db->addfield("po_num");			$db->addvalue($_POST["po_no"]);
					$db->addfield("description");		$db->addvalue($description);
					$db->addfield("currency_id");		$db->addvalue($_POST["currency_id"]);
					$db->addfield("reimbursement");		$db->addvalue($_POST["reimbursement"][$key]);
					$db->addfield("fee");				$db->addvalue($_POST["fee"][$key]);
					$db->addfield("after_tax_rate");	$db->addvalue($_POST["after_tax_rate"][$key]);
					$db->insert();					
				}
				
				//insert jurnal
				$db->addtable("jurnals");
				$db->addfield("tanggal");		$db->addvalue($_POST["issue_at"]);
				$db->addfield("invoice_id");	$db->addvalue($invoice_id);
				$db->addfield("invoice_num");	$db->addvalue($_POST["num"]);
				$db->addfield("description");	$db->addvalue("Account Receivable Invoice No: ".$_POST["num"]." -- ".$_POST["description"]);
				$db->addfield("status");		$db->addvalue("1");
				$db->addfield("isapproved");	$db->addvalue("1");
				$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("created_by");	$db->addvalue($__username);
				$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");	$db->addvalue($__username);
				$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$jurnal_insert = $db->insert();
				if($jurnal_insert["affected_rows"] >= 0){
					$jurnal_id = $jurnal_insert["insert_id"];
					foreach($_POST["coa"] as $key => $coa){
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue($coa);
						$db->addfield("description");	$db->addvalue($_POST["jurnal_description"][$key]);
						$db->addfield("debit");			$db->addvalue($_POST["debit"][$key]);
						$db->addfield("kredit");		$db->addvalue($_POST["credit"][$key]);
						$db->insert();
					}
				} else {
					javascript("alert('Saving Journal failed');");
					echo $jurnal_insert["error"];
				}				
				javascript("alert('Data Saved');");
				javascript("window.location='invoice_view.php?id=".$invoice_id."';");
			} else {
				javascript("alert('Saving data failed');");
				echo $inserting["error"];
			}
		} else {
			javascript("alert('Saving data failed, `Invoice No` already used!');");
		}
	}
	
	if($copy_id > 0) {$db->addtable("invoice");$db->where("id",$copy_id);$db->limit(1);$data = $db->fetch_data();}
	$num_month = "0".substr("00",0,2-strlen(date("m"))).date("m");
	$num_like = "%CHR-".$num_month."/%/".date("y")."%";
	$num = $db->fetch_single_data("invoice","num",array("num" => $num_like.":LIKE"),array("num DESC"));
	if(!$num) $num = "CHR-".$num_month."/001/".date("y"); else {
		$num = substr(str_ireplace("CHR-".$num_month."/","",$num),0,3) * 1;
		$num ++;
		$num = substr("000",0,3-strlen($num)).$num;
		$num = "CHR-".$num_month."/".$num."/".date("y");
	}
	
	$_POST["issue_at"] = date("Y-m-d");
	if($copy_id > 0 && !isset($_POST["save"])){
		$_POST["client_id"] = $data["client_id"];
		$_POST["division_id"] = $data["division_id"];
		$_POST["issue_at"] = substr($data["issue_at"],0,10);
		$_POST["due_date"] = $data["due_date"];
		$_POST["description"] = $data["description"];
		$_POST["additional_detail"] = $data["additional_detail"];
		$_POST["billing_periode"] = $data["billing_periode"];
		$_POST["currency_id"] = $data["currency_id"];
		$_POST["total"] = format_amount($data["total"]);
		
		$checked_is_vat_1 = ($data["vat_mode"] == 2 || $data["vat_mode"] == 3) ? "checked" : "";
		$checked_is_vat_2 = ($data["vat_mode"] == 1 || $data["vat_mode"] == 3) ? "checked" : "";
		$checked_is_tax23_1 = ($data["tax23_mode"] == 2 || $data["tax23_mode"] == 3) ? "checked" : "";
		$checked_is_tax23_2 = ($data["tax23_mode"] == 1 || $data["tax23_mode"] == 3) ? "checked" : "";
	}
	
	$sel_client = $f->select_window("client_id","Clients",$_POST["client_id"],"clients","id","name","win_clients.php");
    $sel_division = $f->select("division_id",$db->fetch_select_data("divisions","id","name",null,array("name")),$_POST["division_id"]);
	$txt_num = $f->input("num",$num);
    $sel_po_no = $f->select_window("po_no","Purchase Order",$_POST["po_no"],"po","num","concat(num) as num","win_po.php");
	$txt_wcc_no = $f->input("wcc_no","","style='width:250px;'");
	$cal_issue_at = $f->input("issue_at",$_POST["issue_at"],"type='date'");
	if($_POST["due_date"] == -1){
		$_chk_immediate = "checked";
		$_duedate_readonly = "readonly";
	}
    $cal_due_date = $f->input("due_date",$_POST["due_date"],"$_duedate_readonly style='width:50px;' type='number' value='30'")." Day(s)";
	$chk_immediate = $f->input("immediate","1","type='checkbox' $_chk_immediate onclick='immediate_duedate();'")." Immediate";
	
    $txt_desc = $f->textarea("description",$_POST["description"]);
    $txt_billing = $f->input("billing_periode",$_POST["billing_periode"]);
	
    $chk_is_vat = $f->input("is_vat_1","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_vat_1)." Reimbursement &nbsp;&nbsp;&nbsp;";
    $chk_is_vat .= $f->input("is_vat_2","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_vat_2)." Fee";
	
    $chk_is_tax23 = $f->input("is_tax23_1","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_tax23_1)." Reimbursement &nbsp;&nbsp;&nbsp;";
    $chk_is_tax23 .= $f->input("is_tax23_2","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_tax23_2)." Fee";
	
	$sel_currency_id = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as id2"),$_POST["currency_id"]);
    $txt_total = $f->input("total",$_POST["total"],"size='12' readonly");
	
    if($data["print_config"] != ""){
		$print_config = explode("|",$data["print_config"]);
		$checked_show_attn = ($print_config[1] == "1") ? "checked":"";
		$checked_digit_4_year = ($print_config[2] == "1") ? "checked":"";
		$checked_show_detail_attached = ($print_config[4] == "1") ? "checked":"";
		$checked_due_date_format_1 = ($print_config[3] == "1") ? "checked":"";
		$checked_due_date_format_2 = ($print_config[3] == "2") ? "checked":"";
		$checked_due_date_format_3 = ($print_config[3] == "3") ? "checked":"";
	}
	
    $chk_show_attn = $f->input("show_attn","1","type='checkbox' ".$checked_show_attn)." Show Attn : ";
	$chk_show_attn .= $f->input("attn");
    $chk_digit_year_4 = $f->input("digit_year_4","1","type='checkbox' ".$checked_digit_4_year)." 4 digit year in Invoice No";
    $chk_show_detail_attached = $f->input("show_detail_attached","1","type='checkbox' ".$checked_show_detail_attached)." Show \"Detail Explanations as Attached\"";
    $chk_due_date_format = $f->input("due_date_format","1","type='radio' ".$checked_due_date_format_1)." xx Day(s) &nbsp;&nbsp;&nbsp;";
    $chk_due_date_format .= $f->input("due_date_format","2","type='radio' ".$checked_due_date_format_2)." Nett xx Day(s) &nbsp;&nbsp;&nbsp;";
    $chk_due_date_format .= $f->input("due_date_format","3","type='radio' ".$checked_due_date_format_3)." DD MMM YYYY";
	
	$plusminbutton1 = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('detail_area1','row_detail1_');\"")."&nbsp;";
	$plusminbutton1 .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('detail_area1','row_detail1_');\"");
	$chkAfterTaxRate = $f->input("after_tax_rate[0]","1","type='checkbox' onchange='hitung_total(); load_jurnal_details();'");
	$txt_invoice_description = $f->input("invoice_description[0]","","style='width:300px'");
    $txt_reimbursement = $f->input("reimbursement[0]","","type='number' step='0.01' onkeyup='hitung_total();' onblur='load_jurnal_details();'");
    $txt_fee = $f->input("fee[0]","","type='number' step='0.01' onkeyup='hitung_total();' onblur='load_jurnal_details();'");
	
	$plusminbutton = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('detail_area','row_detail_');\"")."&nbsp;";
	$plusminbutton .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('detail_area','row_detail_');\"");
	$sel_coa = $f->select("coa[0]",$db->fetch_select_data("coa","coa","concat(coa,' -- ',description) as description",array(),array("coa"),"",true));
	$txt_jurnal_description = $f->input("jurnal_description[0]","","style='width:300px'");
	$txt_debit = $f->input("debit[0]","","type='number' step='0.01'");
	$txt_credit = $f->input("credit[0]","","type='number' step='0.01'");
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<table>
		<tr>
			<td valign="top">
				<?=$t->start("","editor_content");?>
					<?=$t->row(array("Client",$sel_client));?>
					<?=$t->row(array("Division",$sel_division));?>
					<?=$t->row(array("No. Invoice",$txt_num));?>
					<?=$t->row(array("PO No.",$sel_po_no));?>
					<?=$t->row(array("WCC No.",$txt_wcc_no));?>
					<?=$t->row(array("Date (Issue At)",$cal_issue_at));?>
					<?=$t->row(array("Payment Due Date",$cal_due_date.$chk_immediate));?>
					<?=$t->row(array("Description",$txt_desc));?>
					<?=$t->row(array("Billing Period",$txt_billing));?>
					<?=$t->row(array("Currency",$sel_currency_id));?>
					<?=$t->row(array("VAT",$chk_is_vat));?>
					<?=$t->row(array("Tax 23",$chk_is_tax23));?>
					<?=$t->row(array("Total",$txt_total));?>
				<?=$t->end();?>
				<br>
				<h3><b>Print Config : </b></h3>
				<?=$t->start("","editor_content");?>
				<?=$t->row(array($chk_show_attn));?>
				<?=$t->row(array($chk_digit_year_4));?>
				<?=$t->row(array($chk_show_detail_attached));?>
				<?=$t->row(array("Due Date Format : ".$chk_due_date_format));?>
				<?=$t->end();?>
			</td>
			<td valign="top">
				<h3><b>Invoice Detail</b></h3>
				<?=$t->start("width='100%'","detail_area1","editor_content_2");?>
					<?=$t->row(array($plusminbutton1."<br>No.","After<br>Tax Rate","Description","Reimbursement","Fee"),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"));?>
					<?=$t->row(array("<div id=\"firstno\">1</div>",$chkAfterTaxRate,$txt_invoice_description,$txt_reimbursement,$txt_fee),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"),"id=\"row_detail1_0\"");?>
				<?=$t->end();?>
				<br>
				<h3><b>Additional Detail</b></h3>
				<?=$f->textarea("additional_detail",$_POST["additional_detail"],"style='width:350px;height:150px;'");?>
			</td>
		</tr>
	</table>
	<br>
	<h3><b>Journal Detail</b></h3>
	<?=$t->start("width='100%'","detail_area","editor_content_2");?>
        <?=$t->row(array($plusminbutton."<br>No.","COA","Description","Debit","Credit"),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"));?>
		<?=$t->row(array("<div id=\"firstno\">1</div>",$sel_coa,$txt_jurnal_description,$txt_debit,$txt_credit),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"),"id=\"row_detail_0\"");?>
	<?=$t->end();?>
	<br>
	
	
	<?php
		if($copy_id > 0){
			$db->addtable("invoice_detail"); $db->where("invoice_id",$copy_id);
			foreach($db->fetch_data(true) as $key => $invoice_detail){
				$is_checked_after_tax_rate = "false";
				if($invoice_detail["after_tax_rate"] == "1") $is_checked_after_tax_rate = "true";
				?> <script>
					adding_row('detail_area1','row_detail1_');
					document.getElementById("after_tax_rate[<?=$key;?>]").checked = <?=$is_checked_after_tax_rate;?>;
					document.getElementById("invoice_description[<?=$key;?>]").value = "<?=$invoice_detail["description"];?>";
					document.getElementById("reimbursement[<?=$key;?>]").value = "<?=$invoice_detail["reimbursement"];?>";
					document.getElementById("fee[<?=$key;?>]").value = "<?=$invoice_detail["fee"];?>";
				</script> <?php
			}
			?> <script> substract_row('detail_area1','row_detail1_'); </script> <?php
		
			$db->addtable("jurnal_details"); $db->awhere("jurnal_id IN (SELECT id FROM jurnals WHERE invoice_id = '".$copy_id."')");
			foreach($db->fetch_data(true) as $key => $jurnal){
				?> <script>
					adding_row('detail_area','row_detail_');
					document.getElementById("coa[<?=$key;?>]").value = "<?=$jurnal["coa"];?>";
					document.getElementById("jurnal_description[<?=$key;?>]").value = "<?=$jurnal["description"];?>";
					document.getElementById("debit[<?=$key;?>]").value = "<?=$jurnal["debit"];?>";
					document.getElementById("credit[<?=$key;?>]").value = "<?=$jurnal["kredit"];?>";
				</script> <?php
			}
			?> <script> substract_row('detail_area','row_detail_'); </script> <?php
		}
	?>
	
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>