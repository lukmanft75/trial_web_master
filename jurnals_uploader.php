<?php	
	set_time_limit(0);
	ini_set('memory_limit', '-1');
?>
<?php include_once "head.php";?>
<?php include_once "classes/simplexlsx.class.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php	
	if(isset($_POST["step2"])) {
		$sheet = $_POST["sheet"];
		$file_name = $_POST["file_name"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($_POST["sheet"]);
		
		$col = array();
		$rowdata = $contents[0];
		foreach($rowdata as $key => $header){
			if(preg_match("/(date)/",strtolower($header)) 				&& !isset($col["trx_date"]))		$col["trx_date"] = $key;
			if(preg_match("/(invoice)*(no)/",strtolower($header)) 		&& !isset($col["invoice_no"]))		$col["invoice_no"] = $key;
			if(preg_match("/(description)/",strtolower($header)) 		&& !isset($col["description"]))		$col["description"] = $key;	
			if(preg_match("/(currency)/",strtolower($header)) 			&& !isset($col["currency_id"]))		$col["currency_id"] = $key;	
			if(preg_match("/(coa)/",strtolower($header)) 				&& !isset($col["coa"]))				$col["coa"] = $key;	
			if(preg_match("/(debit)/",strtolower($header)) 				&& !isset($col["debit"]))			$col["debit"] = $key;	
			if(preg_match("/(credit)/",strtolower($header)) 			&& !isset($col["credit"]))			$col["credit"] = $key;	
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
									$sel_headers = $f->select("sel_header[$headername]",$xls_headers,$headerindex);
									echo "<tr><td>$headername</td><td>$sel_headers</td></tr>";
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
		$num_jurnal = 0;
		$num_jurnal_details = 0;
		
		$onproc_trx_date = "";
		$onproc_invoice_no = "";
		$onproc_description = "";
		$onproc_currency_id = "";
		
		foreach($contents as $key => $rowdata){
			if($key > 0){
				if($rowdata[$sel_header["trx_date"]] !="" && $rowdata[$sel_header["description"]] == "") break;
				
				$trx_date = trim(xls_date($rowdata[$sel_header["trx_date"]]));
				$invoice_no = trim($rowdata[$sel_header["invoice_no"]]);
				$description = trim($rowdata[$sel_header["description"]]);
				$currency_id = trim(strtoupper($rowdata[$sel_header["currency_id"]]));
				$coa = substr(strtoupper($rowdata[$sel_header["coa"]]),0,6);
				if(strpos($coa,"-") <= 0) $coa = substr($coa,0,3)."-".substr($coa,3,2);
				$debit = $rowdata[$sel_header["debit"]];
				$credit = $rowdata[$sel_header["credit"]];
				
				if($onproc_trx_date != $trx_date || $onproc_invoice_no != $invoice_no || $onproc_description != $description || $onproc_currency_id != $currency_id){
					$onproc_trx_date = $trx_date;
					$onproc_invoice_no = $invoice_no;
					$onproc_description = $description;
					$onproc_currency_id = $currency_id;
					$invoice_id = $db->fetch_single_data("invoice","id",array("num" => $invoice_no));
					
					$db->addtable("jurnals");
					$db->addfield("id");
					$db->awhere("tanggal LIKE '%".$trx_date."%' AND invoice_num LIKE '%".$invoice_no."%' AND description LIKE '%".$description."%' AND currency_id = '".$currency_id."'");
					$db->limit(1);
					$jurnal = $db->fetch_data();
					$jurnal_id = $jurnal["id"];
					if($jurnal_id > 0){
						$db->addtable("jurnals");$db->where("id",$jurnal_id);$db->delete_();
						$db->addtable("jurnal_details");$db->where("jurnal_id",$jurnal_id);$db->delete_();
					}
					
					$db->addtable("jurnals");
					$db->addfield("tanggal"); 		$db->addvalue($trx_date);
					$db->addfield("invoice_id"); 	$db->addvalue($invoice_id);
					$db->addfield("invoice_num"); 	$db->addvalue($invoice_no);
					$db->addfield("description"); 	$db->addvalue($description);
					$db->addfield("currency_id"); 	$db->addvalue($currency_id);
					$db->addfield("status"); 		$db->addvalue(1);
					$db->addfield("created_at"); 	$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("created_by"); 	$db->addvalue($__username);
					$db->addfield("created_ip"); 	$db->addvalue($_SEVER["REMOTE_ADDR"]);
					$db->addfield("updated_at"); 	$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("updated_by"); 	$db->addvalue($__username);
					$db->addfield("updated_ip"); 	$db->addvalue($_SEVER["REMOTE_ADDR"]);
					$db->addfield("isapproved"); 	$db->addvalue(1);
					$inserting = $db->insert();
					if($inserting["affected_rows"] > 0){
						$jurnal_id = $inserting["insert_id"];
						$num_jurnal++;
					}
				}
				if($jurnal_id > 0){
					$db->addtable("jurnal_details");
					$db->addfield("jurnal_id"); 	$db->addvalue($jurnal_id);
					$db->addfield("coa"); 			$db->addvalue($coa);
					$db->addfield("description"); 	$db->addvalue($description);
					$db->addfield("debit"); 		$db->addvalue($debit);
					$db->addfield("kredit"); 		$db->addvalue($credit);
					$inserting = $db->insert();
					if($inserting["affected_rows"] > 0){
						$num_jurnal_details++;
					}
				}
			}
		}
		
		echo "<b>";
		echo "<font color='blue'>Data Uploaded</font><br><br>";
		echo "Journals 	: ".$num_jurnal."<br>";
		echo "Journal Details : ".$num_jurnal_details."<br>";
		echo "</b>";
		echo $f->input("refresh","Refresh","type='button' onclick=\"window.location='?';\"","btn_sign");
		echo $f->input("list","Journals List","type='button' style='width:150px;' onclick=\"window.location='jurnals_list.php';\"","btn_sign");
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
			<?=$f->input("list","Transactions List","type='button' style='width:150px;' onclick=\"window.location='jurnals_list.php';\"","btn_sign");?>
		<?=$f->end();?>
		</td></tr></table>	
	</td></tr></table>	
<?php } ?>
<?php include_once "footer.php";?>