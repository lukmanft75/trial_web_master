<?php
	if($_GET["export"]){
		$_exportname = "Invoice_Rekap.xls";
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$_exportname);
		header("Pragma: no-cache");
		header("Expires: 0");
		$_GET["do_filter"]="Load";
		$_isexport = true;
	}
	include_once "head.php";
?>
<?php if(!$_isexport){ ?>
	<div class="bo_title">Invoice Rekap</div>
	<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
	<div id="bo_filter">
		<div id="xbo_filter_container">
			<?=$f->start("filter","GET");?>
				<?=$t->start();?>
				<?php
					// ############## FILTER BY PERIODE AND CLIENT (Bisa All)
					$issueat = $f->input("issueat",@$_GET["issueat"],"type='month'");
					$client = $f->select("client",$db->fetch_select_data("clients","id","name",array(),array(),"",true),@$_GET["client"],"style='height:25px'");
				?>
				<?=$t->row(array("Issue At",$issueat));?>
				<?=$t->row(array("Client",$client));?>
				<?=$t->end();?>
				<?=$f->input("page","1","type='hidden'");?>
				<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
				<?=$f->input("do_filter","Load","type='submit'");?>
				<?=$f->input("export","Export to Excel","type='submit' style='width:180px;'");?>
				<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
			<?=$f->end();?>
		</div>
	</div>
<?php } else { ?>
	<h2><b>Invoice Rekap</b></h2>
	<b><?=$db->fetch_single_data("projects","name",array("id" => $_GET["project"]));?></b>
<?php } ?>

<?php
	if($_GET["issueat"] != "" || $_GET["client"] != ""){
		$client_name = $db->fetch_single_data("clients","name",array("id" => $_GET["client"]));
		$month = substr($_GET["issueat"],5,2) * 1;
		?> <div class="bo_title"><?=$client_name;?> <?=format_tanggal($_GET["issueat"]."-01","F Y");?></div><br> <?php
		$whereclause = "";
		if(@$_GET["issueat"]!="") $whereclause .= "(issue_at LIKE '".$_GET["issueat"]."%') AND ";
		if(@$_GET["client"]!="") $whereclause .= "(client_id = '".$_GET["client"]."') AND ";
		
		$db->addtable("invoice");
		if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
		if(@$_GET["sort"] == "") $_GET["sort"] = "if(LENGTH(SUBSTRING_INDEX(num, '/', 2)) = 11,concat(SUBSTRING_INDEX(num, '/', 1),'/0',SUBSTRING_INDEX(num, '/', -2)),num)";
		if(@$_GET["sort"] == "num") $_GET["sort"] = "if(LENGTH(SUBSTRING_INDEX(num, '/', 2)) = 11,concat(SUBSTRING_INDEX(num, '/', 1),'/0',SUBSTRING_INDEX(num, '/', -2)),num)";
		if(@$_GET["sort"] == "num DESC") $_GET["sort"] = "if(LENGTH(SUBSTRING_INDEX(num, '/', 2)) = 11,concat(SUBSTRING_INDEX(num, '/', 1),'/0',SUBSTRING_INDEX(num, '/', -2)),num) DESC";
		$db->order($_GET["sort"]);
		$invoice = $db->fetch_data(true);
		
?>
		<?php if($_isexport){ $_tableattr = "border='1'"; }?>
		<?=$t->start($_tableattr,"data_content");?>
		<?=$t->header(array("No",
							"Month<br>/Year",
							"<div onclick=\"sorting('num');\">Inv No</div>",
							"<div onclick=\"sorting('issue_at');\">Inv Date</div>",
							"<div onclick=\"sorting('division_id');\">Divisi</div>",
							"<div onclick=\"sorting('client_id');\">Client</div>",
							"<div onclick=\"sorting('description');\">Description</div>",
							"<div onclick=\"sorting('billing_periode');\">Periode Billing</div>",
							"<div onclick=\"sorting('due_date');\">Due Date</div>",
							"<div onclick=\"sorting('po_no');\">PO Number</div>",
							"Reimbursement",
							"Fee",
							"<div onclick=\"sorting('total_po');\">Total PO</div>",
							"<div onclick=\"sorting('tax23');\">Tax 23</div>",
							"<div onclick=\"sorting('vat');\">Vat</div>",
							"<div onclick=\"sorting('total');\">Sales Order</div>",
							"Outstanding",
							"No WCC"));?>
		<?php
			$TOT_total_po = 0;
			$TOT_vat = 0;
			$TOT_total= 0;
			$TOT_outstanding = 0;
		?>
		<?php foreach($invoice as $no => $invoice_){ ?>
			<?php
				$kurs = $db->fetch_all_data("currencies_history",["kurs"],"currency_id = '".$invoice_["currency_id"]."' AND '".substr($invoice_["issue_at"],0,10)."' BETWEEN date1 AND date2")[0]["kurs"];
				if(!$kurs) $kurs = $db->fetch_single_data("currencies","kurs",["id" => $invoice_["currency_id"]]);
				if(!$kurs) $kurs = 1;
				$division = $db->fetch_single_data("divisions","name",array("id"=>$invoice_["division_id"]));
				$client = $db->fetch_single_data("clients","name",array("id"=>$invoice_["client_id"]));
				$invoice_status = $db->fetch_single_data("invoice_status","name",array("id"=>$invoice_["invoice_status_id"]));
				$reimbursement = $db->fetch_single_data("invoice_detail","concat(sum(reimbursement))",array("invoice_id"=>$invoice_["id"])) * $kurs;
				$fee = $db->fetch_single_data("invoice_detail","concat(sum(fee))",array("invoice_id"=>$invoice_["id"])) * $kurs;
				$outstanding = (($invoice_["total"] * 1) - ($invoice_["receive"] * 1)) * $kurs;
				$wcc_no = $db->fetch_single_data("wcc","wcc_no",array("po_no" => $invoice_["po_no"]));
				$due_date = date("d M Y",mktime(0,0,0,substr($invoice_["issue_at"],5,2),substr($invoice_["issue_at"],8,2) + $invoice_["due_date"],substr($invoice_["issue_at"],0,4)));
				
				
				$TOT_total_po += ($invoice_["total_po"] * $kurs);
				$TOT_vat += ($invoice_["vat"] * $kurs);
				$TOT_total += ($invoice_["total"] * $kurs);
				$TOT_outstanding += $outstanding;
			?>
			<?=$t->row(
						array($no+$start+1,
							$month,
							"<a href=\"invoice_view.php?id=".$invoice_["id"]."\">".$invoice_["num"]."</a>",
							format_tanggal($invoice_["issue_at"],"dMY"),
							$division,
							$client,
							str_replace("<br>","&nbsp;",$invoice_["description"]),
							$invoice_["billing_periode"],
							$due_date,
							"<a href=\"invoice_view.php?id=".$invoice_["id"]."\">".$invoice_["po_no"]."</a>",
							format_amount($reimbursement),
							format_amount($fee),
							format_amount($invoice_["total_po"] * $kurs),
							format_amount($invoice_["tax23"] * $kurs),
							format_amount($invoice_["vat"] * $kurs),
							format_amount($invoice_["total"] * $kurs),
							format_amount($outstanding),
							$wcc_no),
						array("align='right'","","","","","","","","","","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'","")
					);?>
		<?php } ?>
		<?=$t->row(
					array("",
						"<b>Grand Total</b>",
						"",
						"<b>".format_amount($TOT_total_po)."</b>",
						"",
						"<b>".format_amount($TOT_vat)."</b>",
						"<b>".format_amount($TOT_total)."</b>",
						"<b>".format_amount($TOT_outstanding)."</b>",
						""),
					array("colspan='10'","","align='right'","align='right'","align='right'","align='right'","align='right'","align='right'","")
				);?>
		<?=$t->end();?>
	<?php } ?>
<?php include_once "footer.php";?>
