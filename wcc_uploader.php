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
		$header_row = $_POST["header_row"];
		$first_row = $_POST["first_row"];
		$max_row = $_POST["max_row"];
		$client_id = $_POST["client"];
		$file_name = $_POST["file_name"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($_POST["sheet"]);
		
		$col = array();
		$rowdata = $contents[$header_row];
		foreach($rowdata as $key => $header){
			if(preg_match("/(po)*(num)/",strtolower($header)) 			&& !isset($col["num"]))				$col["num"] = $key;
			if(preg_match("/(sow)/",strtolower($header)) 				&& !isset($col["sow"]))				$col["sow"] = $key;
			if(preg_match("/(amount)/",strtolower($header)) 			&& !isset($col["amount"]))			$col["amount"] = $key;
			if(preg_match("/(wcc)*(number)/",strtolower($header)) 		&& !isset($col["wcc_no"]))			$col["wcc_no"] = $key;
			if(preg_match("/(ms)*(date)/",strtolower($header)) 			&& !isset($col["doc_date"]))		$col["doc_date"] = $key;
		}
		
		$xls_headers = array();
		$xls_headers[""] = "---";
		foreach($contents[$header_row] as $headerindex => $headername){ $xls_headers[$headerindex] = $headername." (".$headerindex.")";}
		?>
		<table width="100%"><tr><td align="center">
			<table width="100"><tr><td nowrap>
				<?=$f->start();?>
					<?=$f->input("sheet",$sheet,"type='hidden'");?>
					<?=$f->input("client_id",$client_id,"type='hidden'");?>
					<?=$f->input("header_row",$header_row,"type='hidden'");?>
					<?=$f->input("first_row",$first_row,"type='hidden'");?>
					<?=$f->input("max_row",$max_row,"type='hidden'");?>
					<fieldset>
						<table>
							<tr><td>Sheet</td><td> : <?=$xlsx->sheetNames()[$sheet];?></td></tr>
							<tr><td>Client</td><td> : <?=$db->fetch_single_data("clients","name",array("id"=>$client_id));?></td></tr>
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
		$header_row = $_POST["header_row"];
		$first_row = $_POST["first_row"];
		$max_row = $_POST["max_row"];
		$client_id = $_POST["client_id"];
		$file_name = $_POST["file_name"];
		$sel_header = $_POST["sel_header"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($sheet);
		$num_po = 0;
		$num_wcc_new = 0;
		$num_wcc_old = 0;
		
		$po = array();
		foreach($contents as $key => $rowdata){
			if($key >= $first_row){
				if($key >= $max_row) break;
				
				$col["num"] = $key;
				$col["sow"] = $key;
				$col["amount"] = $key;
				$col["wcc_no"] = $key;
				$col["doc_date"] = $key;
				
				$num = trim($rowdata[$sel_header["num"]]);
				$sow = $rowdata[$sel_header["sow"]];
				$amount = $rowdata[$sel_header["amount"]];
				$wcc_no = trim($rowdata[$sel_header["wcc_no"]]);
				$doc_date = xls_date($rowdata[$sel_header["doc_date"]]);
				
				if($num != ""){
					$po[$num][$key]["sow"] = $sow;
					$po[$num][$key]["ammount"] = $amount;
					$po[$num][$key]["wcc_no"] = $wcc_no;
					$po[$num][$key]["doc_date"] = $doc_date;
				}
			}
		}
		
		foreach($po as $num => $details){
			if($db->fetch_single_data("po","id",array("num" => $num.":LIKE")) < 1){
				//insert po
				$db->addtable("po");
				$db->addfield("num");				$db->addvalue($num);
				$db->addfield("client_id");			$db->addvalue($client_id);
				$db->addfield("currency_id");		$db->addvalue("IDR");
				$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("created_by");		$db->addvalue($__username);
				$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");		$db->addvalue($__username);
				$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$inserting = $db->insert();
				if($inserting["affected_rows"] > 0){
					$num_po++;
					$po_id = $inserting["insert_id"];
					//insert po_detail
					$total = 0;
					$doc_date = "0000-00-00";
					$sow = "";
					foreach($details as $key => $detail){
						$db->addtable("po_detail");
						$db->addfield("po_id");				$db->addvalue($po_id);
						$db->addfield("po_num");			$db->addvalue($num);
						$db->addfield("description_detail");$db->addvalue($detail["sow"]);
						$db->addfield("qty");				$db->addvalue(1);
						$db->addfield("currency_id");		$db->addvalue("IDR");
						$db->addfield("price");				$db->addvalue($detail["ammount"]);
						$db->addfield("total_price");		$db->addvalue($detail["ammount"]);
						$db->insert();
						$total += $detail["ammount"];
						$doc_date = $detail["doc_date"];
						$sow = $detail["sow"];
					}
					$db->addtable("po");			$db->where("id",$po_id);
					$db->addfield("doc_date");		$db->addvalue($doc_date." 00:00:00");
					$db->addfield("description");	$db->addvalue($sow);
					$db->addfield("total");			$db->addvalue($total);
					$db->update();
				}
			}
			
			foreach($details as $key => $detail){
				if($detail["wcc_no"] != ""){
					$wcc_id = $db->fetch_single_data("wcc","id",array("wcc_no" => $detail["wcc_no"].":LIKE"));
					$db->addtable("wcc");
					$db->addfield("wcc_no");	$db->addvalue($detail["wcc_no"]);
					$db->addfield("po_no");		$db->addvalue($num);
					$db->addfield("created_at");$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("created_by");$db->addvalue($__username);
					$db->addfield("created_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
					$db->addfield("updated_at");$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("updated_by");$db->addvalue($__username);
					$db->addfield("updated_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
					if($wcc_id > 0){
						$db->where("id",$wcc_id); 
						$db->update();
						$num_wcc_old++;
					} else {
						$db->insert();
						$num_wcc_new++;
					}
				}
			}
		}
		
		echo "<b>";
		echo "<font color='blue'>Data Uploaded</font><br><br>";
		echo "PO 		: ".$num_po."<br>";
		echo "NEW WCC	: ".$num_wcc_new."<br>";
		echo "OLD WCC	: ".$num_wcc_old."<br>";
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
					Header Row : <?=$f->input("header_row","1","style='width:20px;'");?><br>
					First Row Content : <?=$f->input("first_row","2","style='width:20px;'");?><br>
					Estimate Max Row: <?=$f->input("max_row","1000","style='width:30px;'");?><br>
					Client : <?=$f->select("client",$db->fetch_select_data("clients","id","name",array(),array(),"",true),"","style='height:20px;'");?><br>
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
			Choose File for Upload : <?=$f->input("xlsx","","type='file' accept='.xlsx'");?><br>
			<br><br>
			<?=$f->input("step1","Upload","type='submit'","btn_sign");?>
		<?=$f->end();?>
		</td></tr></table>	
	</td></tr></table>	
<?php } ?>
<?php include_once "footer.php";?>