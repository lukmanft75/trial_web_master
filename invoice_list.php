<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("invoice");$db->where("id",$_GET["deleting"]);$db->delete_();
		$db->addtable("invoice_detail");$db->where("invoice_id",$_GET["deleting"]);$db->delete_();
		$jurnal_id_delete = $db->fetch_single_data("jurnals","id",array("invoice_id"=>$_GET["deleting"]));
		$db->addtable("jurnals");$db->where("invoice_id",$_GET["deleting"]);$db->delete_();
		$db->addtable("jurnal_details");$db->where("jurnal_id",$jurnal_id_delete);$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
	if(isset($_POST["go_paid"])){
		foreach($_POST["pph23"] as $invoice_id => $pph23){
			$invoice_status_id = $db->fetch_single_data("invoice","invoice_status_id",["id" => $invoice_id]);
			$titipan = $_POST["titipan"][$invoice_id];
			if($invoice_status_id == 0){
				$db->addtable("invoice");	$db->where("id",$invoice_id);
				$db->addfield("invoice_status_id");	$db->addvalue("1");
				$db->addfield("paid_at");			$db->addvalue($_POST["paid_at"]);
				$db->addfield("paid_bank_coa");		$db->addvalue($_POST["paid_bank_coa"]);
				$db->update();
				//insert jurnal
				$nominal = 0;
				$invoce_details = $db->fetch_all_data("invoice_detail",[],"invoice_id = '".$invoice_id."'");
				foreach($invoce_details as $invoce_detail){
					$nominal += $invoce_detail["reimbursement"] + $invoce_detail["fee"];
				}
				$vat = $db->fetch_single_data("invoice","vat",["id" => $invoice_id]);
				$tax23 = $db->fetch_single_data("invoice","tax23",["id" => $invoice_id]);
				$invoice_num = $db->fetch_single_data("invoice","num",["id" => $invoice_id]);
				$invoiceDescription = $db->fetch_single_data("invoice","description",["id" => $invoice_id]);
				$nominal += $vat + $tax23;
				$paymentDescription = "Payment Invoice No: ".$invoice_num." -- ".$invoiceDescription;
				$ar_jurnal_id = $db->fetch_single_data("jurnals","id",["invoice_id" => $invoice_id,"description" => "Account Receivable Invoice No%:LIKE"]);
				$coaKredit = $db->fetch_single_data("jurnal_details","coa",["jurnal_id"=>$ar_jurnal_id,"debit"=>"0:<>","coa"=>"(SELECT coa FROM coa WHERE description LIKE 'piutang%'):IN"]);
				$db->addtable("jurnals");
				$db->addfield("tanggal");		$db->addvalue($_POST["paid_at"]);
				$db->addfield("invoice_id");	$db->addvalue($invoice_id);
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
					$db->addfield("coa");			$db->addvalue($_POST["paid_bank_coa"]);
					$db->addfield("description");	$db->addvalue($paymentDescription);
					$db->addfield("debit");			$db->addvalue($nominal - $pph23 - $titipan);
					$db->insert();
					
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
					$db->addfield("coa");			$db->addvalue($coaKredit);
					$db->addfield("description");	$db->addvalue($paymentDescription);
					$db->addfield("kredit");		$db->addvalue($nominal);
					$db->insert();
					
					if($pph23 <> 0){
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue($__coa["BDD PPh23"]);
						$db->addfield("description");	$db->addvalue($paymentDescription);
						$db->addfield("debit");			$db->addvalue($pph23);
						$db->insert();
					}	

					if($titipan <> 0){
						$db->addtable("jurnal_details");
						$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
						$db->addfield("coa");			$db->addvalue("");
						$db->addfield("description");	$db->addvalue("Titipan - ".$paymentDescription);
						$db->addfield("debit");			$db->addvalue($titipan);
						$db->insert();
					}
				}
				$message = "<font color='blue'>Invoice Paid at ".format_tanggal($_POST["paid_at"],"dMY")."</font>";
			}
		}
	}
?>
<script>
	function checkingInvoice(){
		var invoices = "";
		var c = document.getElementsByTagName('input');
		for (var i = 0; i < c.length; i++) {
			if (c[i].type == 'checkbox' && c[i].checked) {
				invoices += c[i].value+";";
			}
		}
		return invoices;
	}
	function go_paid(){
		var invoices = checkingInvoice();
		$.get( "ajax/payment_multiple_invoice.php?mode=go_paid&invoices="+invoices, function(data) {
			modalBody = "<form method='POST' action='?<?=$_SERVER["QUERY_STRING"];?>' id='frmPaymentMultiInvoice'>";
			modalBody += "<input type='hidden' name='go_paid' value='1'>";
			modalBody += "<table>";
			modalBody += "<tr>";
			modalBody += "<td><b>Payment Date : </b></td>";
			modalBody += "<td><input type='date' name='paid_at' value='<?=substr($__now,0,10);?>'></td>";
			modalBody += "</tr>";
			modalBody += "<tr>";
			modalBody += "<td><b>Payment to: </b></td>";
			modalBody += "<td>";
			modalBody += "<select name='paid_bank_coa'>";
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
			modalBody += "</table>";
			modalBody += data;
			modalBody += "</form>";
			modalFooter = "<button type=\"button\" class=\"btn btn-primary\" onclick=\"document.getElementById('frmPaymentMultiInvoice').submit();\">OK</button>";
			modalFooter += "<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>";
			$('#modalTitle').html("Invoice Payment");
			$('#modalBody').html(modalBody);
			$('#modalFooter').html(modalFooter);
			$('#myModal').modal('show');
		});
	}
</script>
<div class="bo_title">Invoice</div>
<?php if($message != "") echo $message; ?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_num = $f->input("txt_num",@$_GET["txt_num"]);
                $issueat = $f->input("issueat",@$_GET["issueat"],"type='date'");
                $issueat1 = $f->input("issueat1",@$_GET["issueat1"],"type='date' style='width:150px;'");
                $issueat2 = $f->input("issueat2",@$_GET["issueat2"],"type='date' style='width:150px;'");
                $duedate = $f->input("duedate",@$_GET["duedate"],"type='date'");
                $division = $f->select("division",$db->fetch_select_data("divisions","id","name",array(),array(),"",true),@$_GET["division"],"style='height:20px'");
				$client = $f->select("client",$db->fetch_select_data("clients","id","name",array(),array(),"",true),@$_GET["client"],"style='height:20px'");
				$currency = $f->select("currency",$db->fetch_select_data("currencies","id","concat(id) as id2",array(),array(),"",true),@$_GET["currency"],"style='height:20px'");
                $desc = $f->textarea("desc",@$_GET["desc"]);
                $billing = $f->input("billing",@$_GET["billing"]);
                $po_no = $f->input("po_no",@$_GET["po_no"]);
                $vat = $f->input("vat",@$_GET["vat"]);
                $total_po = $f->input("total_po",@$_GET["total_po"]);
                $tax23 = $f->input("tax23",@$_GET["tax23"]);
                $total = $f->input("total",@$_GET["total"]);
                $inwords = $f->input("inwords",@$_GET["inwords"]);
				$arr_invoice_status = array();
				$arr_invoice_status[""] = "";
				$_invoice_status = $db->fetch_select_data("invoice_status","id","name");
				foreach($_invoice_status as $key => $status){
					if($key == 0) $key = 3;
					$arr_invoice_status[$key] = $status;
				}
				$invoice_status = $f->select("invoice_status",$arr_invoice_status,@$_GET["invoice_status"],"style='height:20px'");
                $receive = $f->input("receive",@$_GET["receive"]);
                $paid_at = $f->input("paid_at",@$_GET["paid_at"],"type='date'");
			?>
			<?=$t->row(array("Invoice Number",$txt_num));?>
			<?=$t->row(array("PO No.",$po_no));?>
			<?=$t->row(array("Issue At",$issueat1." - ".$issueat2));?>
			<?=$t->row(array("Due Date",$duedate));?>
			<?=$t->row(array("Division",$division));?>
			<?=$t->row(array("Client",$client));?>
			<?=$t->row(array("Currency",$currency));?>
			<?=$t->row(array("Description",$desc));?>
			<?=$t->row(array("Billing Period",$billing));?>
			<?=$t->row(array("VAT",$vat));?>
			<?=$t->row(array("Total PO",$total_po));?>
			<?=$t->row(array("Tax 23",$tax23));?>
			<?=$t->row(array("Total",$total));?>
			<?=$t->row(array("Inwords",$inwords));?>
			<?=$t->row(array("Status",$invoice_status));?>
			<?=$t->row(array("Receive",$receive));?>
			<?=$t->row(array("Paid At",$paid_at));?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
		<?=$f->end();?>
	</div>
</div>

<?php
	$whereclause = "";
	if(@$_GET["txt_num"]!="") $whereclause .= "(num LIKE '%".$_GET["txt_num"]."%') AND ";
    if(@$_GET["issueat"]!="") $whereclause .= "(issue_at LIKE '".$_GET["issueat"]."%') AND ";
    if(@$_GET["issueat1"]!="") $whereclause .= "(DATE(issue_at) >= '".$_GET["issueat1"]."') AND ";
    if(@$_GET["issueat2"]!="") $whereclause .= "(DATE(issue_at) <= '".$_GET["issueat2"]."') AND ";
    if(@$_GET["duedate"]!="") $whereclause .= "(due_date = '".$_GET["duedate"]."') AND ";
    if(@$_GET["division"]!="") $whereclause .= "(division_id = '".$_GET["division"]."') AND ";
	if(@$_GET["client"]!="") $whereclause .= "(client_id = '".$_GET["client"]."') AND ";
	if(@$_GET["currency"]!="") $whereclause .= "(currency_id = '".$_GET["currency"]."') AND ";
	if(@$_GET["desc"]!="") $whereclause .= "(description LIKE '%".$_GET["desc"]."%') AND ";
	if(@$_GET["billing"]!="") $whereclause .= "(billing_periode LIKE '%".$_GET["billing"]."%') AND ";
	if(@$_GET["po_no"]!="") $whereclause .= "(po_no LIKE '%".$_GET["po_no"]."%') AND ";
	if(@$_GET["vat"]!="") $whereclause .= "(vat ='".$_GET["vat"]."') AND ";
	if(@$_GET["reimbursement"]!="") $whereclause .= "(reimbursement ='".$_GET["reimbursement"]."') AND ";
	if(@$_GET["total_po"]!="") $whereclause .= "(total_po ='".$_GET["total_po"]."') AND ";
	if(@$_GET["tax23"]!="") $whereclause .= "(tax23 ='".$_GET["tax23"]."') AND ";
	if(@$_GET["total"]!="") $whereclause .= "(total ='".$_GET["total"]."') AND ";
	if(@$_GET["inwords"]!="") $whereclause .= "(inwords LIKE '%".$_GET["inwords"]."%') AND ";
	if(@$_GET["invoice_status"] != "") {
		if(@$_GET["invoice_status"] == "3") $_GET["invoice_status"] = "0";
		$whereclause .= "(invoice_status_id = '".$_GET["invoice_status"]."') AND ";
	}
	if(@$_GET["receive"]!="") $whereclause .= "(receive = '".$_GET["receive"]."') AND ";
	if(@$_GET["paid_at"]!="") $whereclause .= "(paid_at = '".$_GET["paid_at"]."') AND ";
	
	$db->addtable("invoice");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("invoice");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$invoice = $db->fetch_data(true);
	$_reimbursement = 0;
	$_fee = 0;
	$_total_po = 0;
	$_vat = 0;
	$_tax23 = 0;
	$_total = 0;
	$_receive = 0;
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='invoice_add.php';\"");?>
	<?=$f->input("rekap","Rekap","type='button' onclick=\"window.open('invoice_rekap.php');\"");?>
	<?=$f->input("xlsx_uploader","Upload From Xlsx","type='button' onclick=\"window.location='invoice_uploader.php';\"");?>
	<?=$f->input("payment","Payment","type='button' onclick=\"go_paid();\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('num');\">Invoice Number</div>",
						"<div onclick=\"sorting('po_no');\">PO No.</div>",
						"<div onclick=\"sorting('issue_at');\">Issue At</div>",
						"<div onclick=\"sorting('due_date');\">Due Date</div>",
                        "<div onclick=\"sorting('division_id');\">Div</div>",
						"<div onclick=\"sorting('client_id');\">Client</div>",
						"<div onclick=\"sorting('currency_id');\">Currency</div>",
						"<div onclick=\"sorting('billing_periode');\">Billing Periode</div>",
						"Reimbursement",
						"Fee",
						"<div onclick=\"sorting('total_po');\">Total PO</div>",
						"<div onclick=\"sorting('vat');\">Vat</div>",
						"<div onclick=\"sorting('tax23');\">Tax 23</div>",
						"<div onclick=\"sorting('total');\">Total</div>",
						"<div onclick=\"sorting('receive');\">Receive</div>",
						"<div onclick=\"sorting('paid_at');\">Paid At</div>",
						"<div onclick=\"sorting('invoice_status_id');\">Status</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($invoice as $no => $invoice_){ ?>
		<?php
			$actions1 = $f->input("chk[".$invoice_["id"]."]",$invoice_["id"],"type='checkbox' onclick='checkingInvoice();'")."&nbsp;";
			$actions1 .= "<a href=\"invoice_add.php?copy_id=".$invoice_["id"]."\">Copy</a>&nbsp;|&nbsp;";
			$actions1 .= "<a href=\"invoice_edit.php?id=".$invoice_["id"]."\">Edit</a>";
			$actions2 = "<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$invoice_["id"]."';}\">Delete</a>";
                        
			$division = $db->fetch_single_data("divisions","name",array("id"=>$invoice_["division_id"]));
            $client = $db->fetch_single_data("clients","name",array("id"=>$invoice_["client_id"]));
			$invoice_status = $db->fetch_single_data("invoice_status","name",array("id"=>$invoice_["invoice_status_id"]));
			$reimbursement = $db->fetch_single_data("invoice_detail","concat(sum(reimbursement))",array("invoice_id"=>$invoice_["id"]));
			$fee = $db->fetch_single_data("invoice_detail","concat(sum(fee))",array("invoice_id"=>$invoice_["id"]));
			if($invoice_["client_id"] != "6"){ $invoice_["num"] = substr_replace( $invoice_["num"], '20', -2, 0 ); }
			
			$_reimbursement += $reimbursement;
			$_fee += $fee;
			$_total_po += $invoice_["total_po"];
			$_vat += $invoice_["vat"];
			$_tax23 += $invoice_["tax23"];
			$_total += $invoice_["total"];
			$_receive += $invoice_["receive"];
		?>
		<?=$t->row(
					array($no+$start+1,
						$actions1,
						"<a href=\"invoice_view.php?id=".$invoice_["id"]."\">".$invoice_["id"]."</a>",
						"<a href=\"invoice_view.php?id=".$invoice_["id"]."\">".$invoice_["num"]."</a>",
                        "<a href=\"invoice_view.php?id=".$invoice_["id"]."\">".$invoice_["po_no"]."</a>",
                        format_tanggal($invoice_["issue_at"],"dMY"),
                        $invoice_["due_date"],
                        $division,
                        $client,
                        $invoice_["currency_id"],
                        $invoice_["billing_periode"],
                        format_amount($reimbursement,2),
                        format_amount($fee,2),
                        format_amount($invoice_["total_po"],2),
                        format_amount($invoice_["vat"],2),
                        format_amount($invoice_["tax23"],2),
                        format_amount($invoice_["total"],2),
                        format_amount($invoice_["receive"],2),
                        format_tanggal($invoice_["paid_at"]),
						$invoice_status,
						format_tanggal($invoice_["created_at"],"dMY"),
						$invoice_["created_by"],
						$actions2),
					array("align='right' valign='top'","","","","","","","","","","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'")
				);?>
	<?php } ?>
	<?php
		$_reimbursement = "&nbsp;&nbsp;<b>".format_amount($_reimbursement,2)."</b>";
		$_fee = "&nbsp;&nbsp;<b>".format_amount($_fee,2)."</b>";
		$_total_po = "&nbsp;&nbsp;<b>".format_amount($_total_po,2)."</b>";
		$_vat = "&nbsp;&nbsp;<b>".format_amount($_vat,2)."</b>";
		$_tax23 = "&nbsp;&nbsp;<b>".format_amount($_tax23,2)."</b>";
		$_total = "&nbsp;&nbsp;<b>".format_amount($_total,2)."</b>";
		$_receive = "&nbsp;&nbsp;<b>".format_amount($_receive,2)."</b>";
	?>
	<?=$t->row(["<b>Total per Page : </b>",$_reimbursement,$_fee,$_total_po,$_vat,$_tax23,$_total,$_receive,"","","","",""],["align='right' colspan='11'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'"]);?>
	<?php
		// if($whereclause != ""){
		if($maxrow <= 1000){
			$whereclause = substr($whereclause,0,-4);
			$TOTreimbursement = 0;
			$TOTfee = 0;
			$TOTtotal_po = 0;
			$TOTvat = 0;
			$TOTtax23 = 0;
			$TOTtotal = 0;
			$TOTreceive = 0;
			$allinvoices = $db->fetch_all_data("invoice",[],$whereclause);
			foreach($allinvoices as $no => $invoice){
				$TOTreimbursement += $db->fetch_single_data("invoice_detail","concat(sum(reimbursement))",array("invoice_id"=>$invoice["id"]));
				$TOTfee += $db->fetch_single_data("invoice_detail","concat(sum(fee))",array("invoice_id"=>$invoice["id"]));
				$TOTtotal_po += $invoice["total_po"];
				$TOTvat += $invoice["vat"];
				$TOTtax23 += $invoice["tax23"];
				$TOTtotal += $invoice["total"];
				$TOTreceive += $invoice["receive"];
			}
			
			$TOTreimbursement = "&nbsp;&nbsp;<b>".format_amount($TOTreimbursement,2)."</b>";
			$TOTfee = "&nbsp;&nbsp;<b>".format_amount($TOTfee,2)."</b>";
			$TOTtotal_po = "&nbsp;&nbsp;<b>".format_amount($TOTtotal_po,2)."</b>";
			$TOTvat = "&nbsp;&nbsp;<b>".format_amount($TOTvat,2)."</b>";
			$TOTtax23 = "&nbsp;&nbsp;<b>".format_amount($TOTtax23,2)."</b>";
			$TOTtotal = "&nbsp;&nbsp;<b>".format_amount($TOTtotal,2)."</b>";
			$TOTreceive = "&nbsp;&nbsp;<b>".format_amount($TOTreceive,2)."</b>";
			echo $t->row(["<b>GRAND TOTAL : </b>",$TOTreimbursement,$TOTfee,$TOTtotal_po,$TOTvat,$TOTtax23,$TOTtotal,$TOTreceive,"","","","",""],["align='right' colspan='11'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'","align='right' style='font-weight:bolder;'"]);
		}
	?>	
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>