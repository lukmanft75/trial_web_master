<?php include_once "head.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php include_once "invoice_script.php";?>
<?php
	$invoice_status_id = $db->fetch_single_data("invoice","invoice_status_id",["id" => $_GET["id"]]);
	if($invoice_status_id > 0){
		$saving_alert = "Invoice already paid, changes related to numbers are not allowed!";
	}
	if(isset($_GET["payment"])){
		if($invoice_status_id == 0){
			$db->addtable("invoice");	$db->where("id",$_GET["id"]);
			$db->addfield("invoice_status_id");	$db->addvalue("1");
			$db->addfield("paid_at");			$db->addvalue($_GET["paid_at"]);
			$db->addfield("paid_bank_coa");		$db->addvalue($_GET["paid_bank_coa"]);
			$db->update();
			//insert jurnal
			$nominal = 0;
			$invoce_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$_GET["id"]."'");
			foreach($invoce_details as $invoce_detail){
				$nominal += $invoce_detail["reimbursement"] + $invoce_detail["fee"];
			}
			$vat = $db->fetch_single_data("invoice","vat",["id" => $_GET["id"]]);
			$tax23 = $db->fetch_single_data("invoice","tax23",["id" => $_GET["id"]]);
			$invoice_num = $db->fetch_single_data("invoice","num",["id" => $_GET["id"]]);
			$invoiceDescription = $db->fetch_single_data("invoice","description",["id" => $_GET["id"]]);
			$nominal += $vat + $tax23;
			$paymentDescription = "Payment Invoice No: ".$invoice_num." -- ".$invoiceDescription;
			$ar_jurnal_id = $db->fetch_single_data("jurnals","id",["invoice_id" => $_GET["id"],"description" => "Account Receivable Invoice No%:LIKE"]);
			$coaKredit = $db->fetch_single_data("jurnal_details","coa",["jurnal_id"=>$ar_jurnal_id,"debit"=>"0:<>","coa"=>"(SELECT coa FROM coa WHERE description LIKE 'piutang%'):IN"]);
			$db->addtable("jurnals");
			$db->addfield("tanggal");		$db->addvalue($_GET["paid_at"]);
			$db->addfield("invoice_id");	$db->addvalue($_GET["id"]);
			$db->addfield("invoice_num");	$db->addvalue($invoice_num);
			$db->addfield("description");	$db->addvalue($paymentDescription);
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
				$db->addtable("jurnal_details");
				$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
				$db->addfield("coa");			$db->addvalue($_GET["paid_bank_coa"]);
				$db->addfield("description");	$db->addvalue($paymentDescription);
				$db->addfield("debit");			$db->addvalue($nominal - $_GET["payment_pph23"] - $_GET["payment_titipan"]);
				$db->insert();
				
				$db->addtable("jurnal_details");
				$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
				$db->addfield("coa");			$db->addvalue($coaKredit);
				$db->addfield("description");	$db->addvalue($paymentDescription);
				$db->addfield("kredit");		$db->addvalue($nominal);
				$db->insert();
				
				if($_GET["payment_pph23"] <> 0){
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue($__coa["BDD PPh23"]);
					$db->addfield("description");	$db->addvalue($paymentDescription);
					$db->addfield("debit");			$db->addvalue($_GET["payment_pph23"]);
					$db->insert();
				}	

				if($_GET["payment_titipan"] <> 0){
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue("");
					$db->addfield("description");	$db->addvalue("Titipan - ".$paymentDescription);
					$db->addfield("debit");			$db->addvalue($_GET["payment_titipan"]);
					$db->insert();
				}
			} else {
				javascript("alert('Saving Journal failed');");
				echo $jurnal_insert["error"];
			}
			$message = "<font color='blue'>Invoice Paid at ".format_tanggal($_GET["paid"],"dMY")."</font>";
		} else {
			$message = "<font color='red'>Invoice Already Paid</font>";
		}
	}
?>
<div class="bo_title">Edit Invoice</div>
<?php
	if($message != "") echo $message;
	if(isset($_POST["save"])){
		$_existed_num = $db->fetch_single_data("invoice","id",array("num" => $_POST["num"],"id" => $_GET["id"].":!="));
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
			
			$db->addtable("invoice");			$db->where("id",$_GET["id"]);
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
			
			if($invoice_status_id == 0){
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
			}
			
			$print_config = "|".$_POST["show_attn"]."|".$_POST["digit_year_4"]."|".$_POST["due_date_format"]."|".$_POST["show_detail_attached"]."|".$_POST["attn"]."|";
			$vat_mode = bindec(($_POST["is_vat_1"] * 1).($_POST["is_vat_2"] * 1));
			$tax23_mode = bindec(($_POST["is_tax23_1"] * 1).($_POST["is_tax23_2"] * 1));
			
			$db->addfield("print_config");		$db->addvalue($print_config);
			$db->addfield("vat_mode");			$db->addvalue($vat_mode);
			$db->addfield("tax23_mode");		$db->addvalue($tax23_mode);
			$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");		$db->addvalue($__username);
			$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$updating = $db->update();
			if($updating["affected_rows"] >= 0){
				$invoice_id = $_GET["id"];
				
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
				
				if($invoice_status_id == 0){
					$old_jurnal_id = $db->fetch_single_data("jurnals","id",array("invoice_id" => $invoice_id));
					$db->addtable("invoice_detail");$db->where("invoice_id",$invoice_id);$db->delete_();
					$db->addtable("jurnals"); $db->where("id",$old_jurnal_id); $db->delete_();
					$db->addtable("jurnal_details"); $db->where("jurnal_id",$old_jurnal_id); $db->delete_();
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
				}
				javascript("alert('Data Saved, ".$saving_alert."');");
				javascript("window.location='invoice_view.php?id=".$_GET["id"]."';");
			} else {
				javascript("alert('Saving data failed');");
				echo $inserting["error"];
			}
		} else {
			javascript("alert('Saving data failed, `Invoice No` already used!');");
		}
	}
	
	$db->addtable("invoice");$db->where("id",$_GET["id"]);$db->limit(1);$data = $db->fetch_data();
	
	$sel_client = $f->select_window("client_id","Clients",$data["client_id"],"clients","id","name","win_clients.php");
    $sel_division = $f->select("division_id",$db->fetch_select_data("divisions","id","name",null,array("name")),$data["division_id"]);
	$txt_num = $f->input("num",$data["num"]);
    $sel_po_no = $f->select_window("po_no","Purchase Order",$data["po_no"],"po","num","concat(num) as num","win_po.php");
	$txt_wcc_no = $f->input("wcc_no",$db->fetch_single_data("wcc","wcc_no",array("po_no" => $data["po_no"])),"style='width:250px;'");
	$cal_issue_at = $f->input("issue_at",substr($data["issue_at"],0,10),"type='date'");
	if($data["due_date"] == -1){
		$_chk_immediate = "checked";
		$_duedate_readonly = "readonly";
	}
    $cal_due_date = $f->input("due_date",$data["due_date"],"$_duedate_readonly style='width:50px;' type='number' value='30'")." Day(s)";
	$chk_immediate = $f->input("immediate","1","type='checkbox' $_chk_immediate onclick='immediate_duedate();'")." Immediate";
	
    $txt_desc = $f->textarea("description",$data["description"]);
    $txt_billing = $f->input("billing_periode",$data["billing_periode"]);
	
	$checked_is_vat_1 = ($data["vat_mode"] == 2 || $data["vat_mode"] == 3) ? "checked" : "";
	$checked_is_vat_2 = ($data["vat_mode"] == 1 || $data["vat_mode"] == 3) ? "checked" : "";
    $chk_is_vat = $f->input("is_vat_1","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_vat_1)." Reimbursement &nbsp;&nbsp;&nbsp;";
    $chk_is_vat .= $f->input("is_vat_2","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_vat_2)." Fee";
	
	$checked_is_tax23_1 = ($data["tax23_mode"] == 2 || $data["tax23_mode"] == 3) ? "checked" : "";
	$checked_is_tax23_2 = ($data["tax23_mode"] == 1 || $data["tax23_mode"] == 3) ? "checked" : "";
    $chk_is_tax23 = $f->input("is_tax23_1","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_tax23_1)." Reimbursement &nbsp;&nbsp;&nbsp;";
    $chk_is_tax23 .= $f->input("is_tax23_2","1","type='checkbox' onclick='hitung_total();load_jurnal_details();' ".$checked_is_tax23_2)." Fee";
	
	$sel_currency_id = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as id2"),$data["currency_id"]);
    $txt_total = $f->input("total",format_amount($data["total"]),"readonly size='12'");
	
	$print_config = explode("|",$data["print_config"]);
	$checked_show_attn = ($print_config[1] == "1") ? "checked":"";
	$checked_digit_4_year = ($print_config[2] == "1") ? "checked":"";
	$checked_show_detail_attached = ($print_config[4] == "1") ? "checked":"";
	$checked_due_date_format_1 = ($print_config[3] == "1") ? "checked":"";
	$checked_due_date_format_2 = ($print_config[3] == "2") ? "checked":"";
	$checked_due_date_format_3 = ($print_config[3] == "3") ? "checked":"";
	$attn = ($print_config[5] != "") ? $print_config[5]:$db->fetch_single_data("clients","pic",["id" => $data["client_id"]]);
	
    $chk_show_attn = $f->input("show_attn","1","type='checkbox' ".$checked_show_attn)." Show Attn : ";
	$chk_show_attn .= $f->input("attn",$attn);
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
<?=$f->start("","POST","?".$_SERVER["QUERY_STRING"],"enctype='multipart/form-data'");?>
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
				<?=$f->textarea("additional_detail",$data["additional_detail"],"style='width:350px;height:150px;'");?>
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
		$db->addtable("invoice_detail"); $db->where("invoice_id",$_GET["id"]);
		foreach($db->fetch_data(true) as $key => $invoice_detail){			
			$is_checked_after_tax_rate = "false";
			if($invoice_detail["after_tax_rate"] == "1") $is_checked_after_tax_rate = "true";
			?> <script>
				adding_row('detail_area1','row_detail1_');
				document.getElementById("after_tax_rate[<?=$key;?>]").checked = <?=$is_checked_after_tax_rate;?>;
				document.getElementById("invoice_description[<?=$key;?>]").value = "<?=str_replace([chr(13).chr(10),'"',"'"],[" ","",""],$invoice_detail["description"]);?>";
				document.getElementById("reimbursement[<?=$key;?>]").value = "<?=$invoice_detail["reimbursement"];?>";
				document.getElementById("fee[<?=$key;?>]").value = "<?=$invoice_detail["fee"];?>";
			</script> <?php
		}
		?> <script> substract_row('detail_area1','row_detail1_'); </script> <?php
	
		$db->addtable("jurnal_details"); $db->awhere("jurnal_id IN (SELECT id FROM jurnals WHERE invoice_id = '".$_GET["id"]."' AND description LIKE 'Account Receivable Invoice No%')");
		foreach($db->fetch_data(true) as $key => $jurnal){
			?> <script>
				adding_row('detail_area','row_detail_');
				document.getElementById("coa[<?=$key;?>]").value = "<?=$jurnal["coa"];?>";
				document.getElementById("jurnal_description[<?=$key;?>]").value = "<?=str_replace([chr(13).chr(10),'"',"'"],[" ","",""],$jurnal["description"]);?>";
				document.getElementById("debit[<?=$key;?>]").value = "<?=$jurnal["debit"];?>";
				document.getElementById("credit[<?=$key;?>]").value = "<?=$jurnal["kredit"];?>";
			</script> <?php
		}
		?> <script> substract_row('detail_area','row_detail_'); </script> <?php
	?>
	<script>
		function go_paid(){
			modalBody = "<table>";
			modalBody += "<tr>";
			modalBody += "<td><b>Payment Date : </b></td>";
			modalBody += "<td><input type='date' id='paid_at' value='<?=substr($__now,0,10);?>'></td>";
			modalBody += "</tr>";
			modalBody += "<tr>";
			modalBody += "<td><b>Payment to: </b></td>";
			modalBody += "<td>";
			modalBody += "<select id='paid_bank_coa'>";
			modalBody += "<option value=''>-pilih bank/kas-</option>";
			<?php
				$coas = $db->fetch_all_data("coa",[],"prf_code<>''","coa");
				foreach($coas as $coa){
			?>
			modalBody += "<option value='<?=$coa["coa"];?>'><?=$coa["prf_code"];?></option>";
			<?php } ?>
			modalBody += "</select>";
			modalBody += "</td>";
			modalBody += "</tr>";
			modalBody += "<tr>";
			modalBody += "<td><b>PPH 23</b></td>";
			modalBody += "<td><input id='payment_pph23' value='0'></td>";
			modalBody += "</tr>";
			modalBody += "<tr>";
			modalBody += "<td><b>Titipan</b></td>";
			modalBody += "<td><input id='payment_titipan' value='0'></td>";
			modalBody += "</tr>";
			modalFooter = "<button type=\"button\" class=\"btn btn-primary\" onclick=\"window.location='?id=<?=$_GET["id"];?>&payment=1&paid_at='+paid_at.value+'&paid_bank_coa='+paid_bank_coa.value+'&payment_pph23='+payment_pph23.value+'&payment_titipan='+payment_titipan.value;\">OK</button>";
			modalFooter += "<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>";
			$('#modalTitle').html("Invoice Payment");
			$('#modalBody').html(modalBody);
			$('#modalFooter').html(modalFooter);
			$('#myModal').modal('show');
		}
	</script>
	<?php
		if($data["invoice_status_id"] != "1"){
			if($data["invoice_status_id"] == "0") echo "<b>Invoice is Outstanding</b><br>";
			if($data["invoice_status_id"] == "2") echo "<b>Invoice is Received at ".format_tanggal($data["paid_at"])."</b><br>";
			echo $f->input("paid","Payment","type='button' onclick=\"go_paid();\"");
		} else {
			echo "<b>Invoice is Paid at ".format_tanggal($data["paid_at"])."</b>";
		}
	?><br><br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>