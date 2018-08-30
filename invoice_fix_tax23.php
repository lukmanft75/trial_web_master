<?php	
	set_time_limit(0);
	ini_set('memory_limit', '-1');
?>
<?php include_once "head.php";?>
<?php include_once "classes/simplexlsx.class.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php
	
	function  month_to_num($month){
		$month = strtolower(str_replace(array(" ",chr(10),chr(13)),"",$month));
		if(substr($month,0,3) == "jan") return "01";
		if(substr($month,0,3) == "feb" || substr($month,0,3) == "peb") return "02";
		if(substr($month,0,3) == "mar") return "03";
		if(substr($month,0,3) == "apr") return "04";
		if(substr($month,0,3) == "mei" || substr($month,0,3) == "may") return "05";
		if(substr($month,0,3) == "jun") return "06";
		if(substr($month,0,3) == "jul") return "07";
		if(substr($month,0,3) == "aug" || substr($month,0,3) == "agt") return "08";
		if(substr($month,0,3) == "sep") return "09";
		if(substr($month,0,1) == "o") return "10";
		if(substr($month,0,1) == "n") return "11";
		if(substr($month,0,2) == "de") return "12";
	}
	
	if(isset($_POST["step2"])) {
		$sheet = $_POST["sheet"];
		$file_name = $_POST["file_name"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($_POST["sheet"]);
		
		$col = array();
		$rowdata = $contents[0];
		foreach($rowdata as $key => $header){
			if(preg_match("/(inv no)/",strtolower($header)) 			&& !isset($col["inv_no"]))			$col["inv_no"] = $key;
			if(preg_match("/(inv)*(date)/",strtolower($header)) 		&& !isset($col["inv_date"]))		$col["inv_date"] = $key;
			if(preg_match("/(divisi)/",strtolower($header)) 			&& !isset($col["divisi"]))			$col["divisi"] = $key;
			if(preg_match("/(client)/",strtolower($header)) 			&& !isset($col["client"]))			$col["client"] = $key;
			if(preg_match("/(description)/",strtolower($header)) 		&& !isset($col["description"]))		$col["description"] = $key;
			if(preg_match("/(periode)*(billing)/",strtolower($header)) 	&& !isset($col["periode_billing"]))	$col["periode_billing"] = $key;
			if(preg_match("/(po)*(number)/",strtolower($header)) 		&& !isset($col["po_number"]))		$col["po_number"] = $key;
			if(preg_match("/(reimbursement)/",strtolower($header)) 		&& !isset($col["reimbursement"]))	$col["reimbursement"] = $key;
			if(preg_match("/(fee)*(total)/",strtolower($header)) 		&& !isset($col["fee_total_po"]))	$col["fee_total_po"] = $key;
			if(preg_match("/(tax)*(23)/",strtolower($header)) 			&& !isset($col["tax_23"]))			$col["tax_23"] = $key;
			if(preg_match("/(vat)/",strtolower($header)) 				&& !isset($col["vat"])
				&& strpos(" ".strtolower($header),"faktur") <= 0)											$col["vat"] = $key;
			if(preg_match("/(settlement)/",strtolower($header)) 		&& !isset($col["settlement"]))		$col["settlement"] = $key;
			if(preg_match("/(sales)*(order)/",strtolower($header)) 		&& !isset($col["sales_order"]))		$col["sales_order"] = $key;
			if(preg_match("/(outstanding)/",strtolower($header)) 		&& !isset($col["outstanding"]))		$col["outstanding"] = $key;
			if(preg_match("/(date)*(of)*(paid)/",strtolower($header)) 	&& !isset($col["date_of_paid"]))	$col["date_of_paid"] = $key;
			if(preg_match("/(receive)/",strtolower($header)) 			&& !isset($col["receive"]))			$col["receive"] = $key;
			if(preg_match("/(remark)/",strtolower($header)) 			&& !isset($col["remark"]))			$col["remark"] = $key;			
		}
		
		$xls_headers = array();
		$xls_headers[""] = "---";
		foreach($contents[0] as $headerindex => $headername){ $xls_headers[$headerindex] = $headername." (".$headerindex.")";}
		?>
		<table width="100%"><tr><td align="center">
			<table width="100"><tr><td nowrap>
				<?=$f->start();?>
					<?=$f->input("sheet",$sheet,"type='hidden'");?>
					<fieldset>
						<table>
							<tr><td>Sheet</td><td> : <?=$xlsx->sheetNames()[$sheet];?></td></tr>
						</table>
						<hr>
						<table border="1">
							<tr><td><b>Chr Dashboards</b></td><td><b>Index Kolom di Excell</b></td></tr>
							<?php 
								foreach($col as $headername => $headerindex){
									if(is_array($headerindex)){
										echo "<tr><td>$headername</td><td></td></tr>";
										foreach($headerindex as $headername1 => $headerindex1){
											if($headername != "allowances"){
												$sel_headers = $f->select("sel_header[$headername][$headername1]",$xls_headers,$headerindex1);
												echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$headername1</td><td>$sel_headers</td></tr>";
											} else {
												$sel_allowances = $f->select("sel_allowances[$headername1]",$db->fetch_select_data("allowances","id","name",array(),array(),"",true));
												echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$headerindex1</td><td>$sel_allowances</td></tr>";
											}
										}
									} else {
										$sel_headers = $f->select("sel_header[$headername]",$xls_headers,$headerindex);
										echo "<tr><td>$headername</td><td>$sel_headers</td></tr>";
									}
								} 
							?>
						</table>
						<?=$f->input("step3","Next","type='submit'","btn_sign");?>
					</fieldset>
					<?=$f->input("file_name",$file_name,"type='hidden'");?>
				<?=$f->end();?>
			</td></tr></table>	
		</td></tr></table>	
		<?php
	}
	
	if(isset($_POST["step3"])) {
		$sheet = $_POST["sheet"];
		$file_name = $_POST["file_name"];
		$sel_header = $_POST["sel_header"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($sheet);
		$num_clients = 0;
		$num_divisions = 0;
		$num_po = 0;
		$num_invoice = 0;
		$invoiceDeleted = 0;
		
		foreach($contents as $key => $rowdata){
			if($key > 0){
				if($rowdata[$sel_header["inv_no"]] == "") break;
				
				$issue_at = xls_date($rowdata[$sel_header["inv_date"]]);
				$paid_at = xls_date($rowdata[$sel_header["date_of_paid"]]);
				$num = "CHR-0".substr($issue_at,5,2)."/".$rowdata[$sel_header["inv_no"]]."/".substr($issue_at,2,2);
				$currency_id = "IDR"; 
				if(stripos(" ".$rowdata[$sel_header["client"]],"Engility Corporation") > 0) $currency_id = "USD";				//////////////if(strpos("$",$contents_ex[$key][16]["format"]) > 0) $currency_id = "USD";
				
				//cari client
				$db->addtable("clients");
				$db->addfield("id");
				$client = trim(str_ireplace(array("pt","tbk",".",",","Servis"),"",$rowdata[$sel_header["client"]]));
				$db->awhere("name LIKE '%".str_replace(" ","%",$client)."%'");
				$db->limit(1);
				$arrtemp = $db->fetch_data();
				$client_id = $arrtemp["id"];
				
				
				$rowdata[$sel_header["divisi"]] = str_replace(" ","",$rowdata[$sel_header["divisi"]]);
				if($rowdata[$sel_header["divisi"]] == "NSN") $rowdata[$sel_header["divisi"]] = "Indottech";
				if($rowdata[$sel_header["divisi"]] == "TR") $rowdata[$sel_header["divisi"]] = "CHR Training";
				if($rowdata[$sel_header["divisi"]] == "JK") $rowdata[$sel_header["divisi"]] = "JalurKerja.Com";
				$db->addtable("divisions");
				$db->addfield("id");
				$db->awhere("name LIKE '".$rowdata[$sel_header["divisi"]]."'");
				$db->limit(1);
				$arrtemp = $db->fetch_data();
				$division_id = $arrtemp["id"];
				if($division_id <= 0){
					$db->addtable("divisions");
					$db->addfield("name"); $db->addvalue($rowdata[$sel_header["divisi"]]);
					$inserting = $db->insert();
					$division_id = $inserting["insert_id"];
					if($inserting["affected_rows"] > 0) $num_divisions++;
				}
				
				//invoice status
				if($rowdata[$sel_header["sales_order"]] == $rowdata[$sel_header["outstanding"]]) $invoice_status_id = 0;//outstanding
				if($rowdata[$sel_header["sales_order"]] == $rowdata[$sel_header["receive"]]) $invoice_status_id = 1;//paid
				if($rowdata[$sel_header["receive"]] > 0 && $rowdata[$sel_header["sales_order"]] != $rowdata[$sel_header["receive"]]) $invoice_status_id = 2;//receive
				
				$po_no = explode(",",$rowdata[$sel_header["po_number"]]); $po_no = $po_no[0];
				//update invoice
				
				$invoice_id = $db->fetch_single_data("invoice","id",["num" => $num.":LIKE","tax23"=>"0"]);
				if($invoice_id){
					$db->addtable("invoice");			$db->where("id",$invoice_id);
					$db->addfield("vat");				$db->addvalue($rowdata[$sel_header["vat"]]);
					$db->addfield("reimbursement");		$db->addvalue($rowdata[$sel_header["reimbursement"]]);
					$db->addfield("fee");				$db->addvalue($rowdata[$sel_header["fee_total_po"]]);
					$db->addfield("total_po");			$db->addvalue($rowdata[$sel_header["reimbursement"]] + $rowdata[$sel_header["fee_total_po"]]);
					$db->addfield("tax23");				$db->addvalue($rowdata[$sel_header["tax_23"]]);
					$db->addfield("total");				$db->addvalue($rowdata[$sel_header["sales_order"]]);
					$db->addfield("inwords");			$db->addvalue(convert_number_to_words($rowdata[$sel_header["sales_order"]]));
					$db->addfield("invoice_status_id");	$db->addvalue($invoice_status_id);
					$db->addfield("receive");			$db->addvalue($rowdata[$sel_header["receive"]]);
					$db->addfield("paid_at");			$db->addvalue($paid_at);
					$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("updated_by");		$db->addvalue($__username);
					$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
					$updating = $db->update();
					if($updating["affected_rows"] > 0) $num_invoice++;
				}
			}
		}
		
		echo "<b>";
		echo "<font color='blue'>Tax 23 Fixed</font><br><br>";
		echo "Invoice	: ".$num_invoice."<br>";
		echo "</b>";
		echo $f->input("refresh","Refresh","type='button' onclick=\"window.location='?';\"","btn_sign");
		unlink("upload_files/".$file_name);
	}
	
	if(isset($_POST["step1"])) {
		$file_name = date("YmdHis").".xlsx";
		move_uploaded_file($_FILES['xlsx']['tmp_name'],"upload_files/".$file_name);
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
?>
	<table width="100%"><tr><td align="center">
		<table width="100"><tr><td nowrap>
			<?=$f->start();?>
				<fieldset>
					Sheet: <?=$f->select("sheet",$xlsx->sheetNames());?><br>
					<?=$f->input("step2","Next","type='submit'","btn_sign");?>
				</fieldset>
				<?=$f->input("file_name",$file_name,"type='hidden'");?>
			<?=$f->end();?>
		</td></tr></table>	
	</td></tr></table>	
<?php	
	}
?>

<?php if(!isset($_POST["step1"]) && !isset($_POST["step2"]) && !isset($_POST["step3"])) { ?>
	<table width="100%"><tr><td align="center">
		<table width="100"><tr><td nowrap>
		<?=$f->start("","POST","","enctype=\"multipart/form-data\"");?>
			Choose File for Upload : <?=$f->input("xlsx","","type='file' accept='.xlsx'");?>
			<br><br>
			<?=$f->input("step1","Upload","type='submit'","btn_sign");?>
		<?=$f->end();?>
		</td></tr></table>	
	</td></tr></table>	
<?php } ?>
<?php include_once "footer.php";?>