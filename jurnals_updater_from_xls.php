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
		for($xx = 0;$xx < 10; $xx++){
			$rowdata = $contents[$xx];
			foreach($rowdata as $key => $header){
				if(preg_match("/(primary)*(key)/",strtolower($header)) 		&& !isset($col["primary_key"]))		$col["primary_key"] = $key;
				if(preg_match("/(journal)*(id)/",strtolower($header)) 		&& !isset($col["journal_id"]))		$col["journal_id"] = $key;
				if(preg_match("/(tanggal)/",strtolower($header)) 			&& !isset($col["trx_date"]))		$col["trx_date"] = $key;
				if(preg_match("/(invoice)*(no)/",strtolower($header)) 		&& !isset($col["invoice_no"]))		$col["invoice_no"] = $key;
				if(preg_match("/(description)/",strtolower($header)) 		&& !isset($col["description"]))		$col["description"] = $key;	
				if(preg_match("/(currency)/",strtolower($header)) 			&& !isset($col["currency_id"]))		$col["currency_id"] = $key;	
				if(preg_match("/(coa)/",strtolower($header)) 				&& !isset($col["coa"]))				$col["coa"] = $key;	
				if(preg_match("/(debit)/",strtolower($header)) 				&& !isset($col["debit"]))			$col["debit"] = $key;	
				if(preg_match("/(credit)/",strtolower($header)) 			&& !isset($col["credit"]))			$col["credit"] = $key;	
			}
			if($col["trx_date"] && $col["description"]){ $header_i = $xx; break; }
		}
		
		$xls_headers = array();
		$xls_headers[""] = "---";
		foreach($contents[$header_i] as $headerindex => $headername){ $xls_headers[$headerindex] = $headername." (".$headerindex.")";}
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
				
				$primary_key = trim($rowdata[$sel_header["primary_key"]]);
				$journal_id = trim($rowdata[$sel_header["journal_id"]]);
				$trx_date = trim(xls_date($rowdata[$sel_header["trx_date"]]));
				$invoice_no = trim($rowdata[$sel_header["invoice_no"]]);
				$description = trim($rowdata[$sel_header["description"]]);
				$currency_id = trim(strtoupper($rowdata[$sel_header["currency_id"]]));
				$coa = substr(strtoupper($rowdata[$sel_header["coa"]]),0,6);
				$debit = $rowdata[$sel_header["debit"]];
				$credit = $rowdata[$sel_header["credit"]];
				
				$executing = false;
				
				$jurnal_detail_id = $db->fetch_single_data("jurnal_details","id",["id" => $primary_key]);
				if($jurnal_detail_id > 0){
					$executing = true;
				} else {
					$currentJournal = $db->fetch_all_data("jurnals",[],"id = '".$journal_id."'")[0];
					if($currentJournal["id"] != $journal_id){
						$currentJournal = $db->fetch_all_data("jurnals",[],"invoice_num = '".$invoice_no."'")[0];
						if($currentJournal["invoice_num"] == $invoice_no){
							$journal_id = $currentJournal["id"];
							$executing = true;
						}
					} else {
						$executing = true;
					}
				}
				
				if($executing){
					if($jurnal_detail_id > 0){
						$db->addtable("jurnal_details"); 	
						$db->where("id",$jurnal_detail_id);
						$db->addfield("coa");			$db->addvalue($coa);
						$db->addfield("description");	$db->addvalue($description);
						$db->addfield("debit");			$db->addvalue($debit);
						$db->addfield("kredit");		$db->addvalue($credit);
						$updating = $db->update();
					} else {
						if($db->fetch_single_data("jurnal_details","concat(count(0))",["jurnal_id" => $journal_id,"debit" => $debit,"kredit" => $credit]) == 1){
							$db->addtable("jurnal_details"); 	
							$db->where("jurnal_id",$journal_id);
							$db->where("debit",$debit);
							$db->where("kredit",$credit);
							$db->addfield("coa");			$db->addvalue($coa);
							$updating = $db->update();
						} else {
							$db->addtable("jurnal_details"); 	
							$db->where("jurnal_id",$journal_id); 
							$db->where("debit",$debit);
							$db->where("kredit",$credit);
							$db->where("coa","");
							$db->addfield("coa");			$db->addvalue($coa);
							$updating = $db->update();
						}
					}
					echo "<br>".$updating["sql"];
					if($updating["affected_rows"] > 0) $num_jurnal_details++;
				} else {
					echo "<br>".trim($rowdata[$sel_header["journal_id"]]);
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