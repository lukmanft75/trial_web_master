<?php	
	set_time_limit(0);
	ini_set("memory_limit", "-1");
?>
<?php include_once "head.php";?>
<?php include_once "classes/simplexlsx.class.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php include_once "func.allDataUpdate.php";?>
<?php
	if(!isset($_GET["step"])){//STEP 0 {PEMBUKA}
?>
	<table width="100%"><tr><td align="center">
		<?=$f->start("","POST","?step=1","enctype=\"multipart/form-data\"");?>
			<?php for($year = date("Y");$year > 2010 ; $year--){$years[$year] = $year;} ?>
			<table>
				<tr><td>Choose File</td><td>:</td><td><?=$f->input("xlsx","","type='file' accept='.xlsx'");?></td></tr>
				<tr><td>Year</td><td>:</td><td><?=$f->select("year",$years);?></td></tr>
				<tr><td colspan="3"><?=$f->input("upload","Upload","type='submit'","btn_sign");?></td></tr>
			</table>
		<?=$f->end();?>
	</td></tr></table>	
<?php 
	} //END STEP 0
	
	if($_GET["step"] == 1) {//STEP 1 {UPLOAD FILE}
		// $file_name = date("YmdHis").".xlsx";
		$file_name = "source_adu.xlsx";
		move_uploaded_file($_FILES["xlsx"]["tmp_name"],"upload_files/".$file_name);
		?> <script> window.location="?year=<?=$_POST["year"];?>&step=2&file=<?=$file_name;?>"; </script> <?php
	}//END STEP 1

	if($_GET["step"] == 2) {//STEP 2
		$file_name = $_GET["file"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$sheet = $_GET["sheet"];
		if(!$sheet) $sheet = 1;
		$year = $_GET["year"];
		$data = getConfig($sheet);
		
		if($_POST["nextSheet"] == "Next"){
			if($_POST["project_id"] > 0){
				saveConfig($_POST);
				$sheet++;
				?> <script> window.location="?year=<?=$year;?>&step=2&file=<?=$file_name;?>&sheet=<?=$sheet;?>"; </script> <?php
			} else {
				echo "<font color='red'>Please choose project</font>";
			}
		}
		if($_POST["skipSheet"] == "Skip"){ 
			saveConfig($_POST); 
			$sheet++;
			?> <script> window.location="?year=<?=$year;?>&step=2&file=<?=$file_name;?>&sheet=<?=$sheet;?>"; </script> <?php
		}
		
		if($sheet > count($xlsx->sheetNames())){ echo "<font color='red'>Run `cd /var/www/html/chr_dashboards/ && php all_data_update_exec.php` at 103.253.113.201 terminal!</font>"; exit; }
		
		$contents = $xlsx->rows($sheet);
		//////////////$contents_ex = $xlsx->rowsEx($sheet);
		
		$col = array();
		foreach($contents as $key1 => $rowdata){
			if($rowdata[2] != "" && $rowdata[4] != ""){//penentuan kolom dari header
				$col= column_parsing($rowdata);
				//cari allowances antara salary/thp - OT
				if(isset($col["thp"])) $_start_allow = $col["thp"];
				if(isset($col["salary"])) $_start_allow = $col["salary"];
				for($xx = ($_start_allow + 1) ; $xx < $col["reason_of_termination"]; $xx ++){
					if(!in_array($xx,$col)) $col["allowances"][$xx] = $rowdata[$xx];
				}					
				//cari amandemen yg tidak ada headernya
				foreach($col["pkwt"] as $key => $_col){
					if(str_replace(array(" ",chr(10),chr(13)),"",$rowdata[$_col + 2]) == "") $col["amandemen"][$key] = $_col + 2;
				}
				break;
			}
		}		
		$xls_headers = array();
		$xls_headers[""] = "---";
		foreach($contents[$key1] as $headerindex => $headername){ $xls_headers[$headerindex] = $headername." (".$headerindex.")";}
		?>
		<table width="100%"><tr><td align="center">
			<table width="100"><tr><td nowrap>
				<?=$f->start("","POST","?year=".$year."&step=2&file=".$file_name."&sheet=".$sheet);?>
					<?=$f->input("year",$year,"type='hidden'");?>
					<?=$f->input("sheet",$sheet,"type='hidden'");?>
					<?=$f->input("header_index","$key1","type='hidden'");?>
					<fieldset>
						<table>
							<tr><td>Project</td><td> : <?=$f->select("project_id",$db->fetch_select_data("projects","id","name",array(),array(),"",true),$data["project_id"]);?></td></tr>
							<tr><td>Year</td><td> : <?=$year;?></td></tr>
							<tr><td>Sheet</td><td> : <?=$xlsx->sheetNames()[$sheet];?></td></tr>
							<tr><td>Sheet ID</td><td> : <?=$sheet;?></td></tr>
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
												if($headername == "pkwt"){
													if(stripos($xls_headers[$headerindex1],"i") > 0) $_pkwt_ke = 0;
													if(stripos($xls_headers[$headerindex1],"ii") > 0) $_pkwt_ke = 1;
													if(stripos($xls_headers[$headerindex1],"iii") > 0) $_pkwt_ke = 2;
													$sel_headers .= " ".$f->select("sel_header[pkwt_ke][$headername1]",["" => "--","0" => "1","1" => "2","2" => "3"],$_pkwt_ke);
												}
												if($headername == "break"){
													$sel_headers .= " ".$f->select("sel_header[break_ke][$headername1]",["0" => "1","1" => "2"],$_break_ke);
													$_break_ke++;
												}
												echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$headername1</td><td>$sel_headers</td></tr>";
											} else {
												$sel_allowances = $f->select("sel_allowances[$headername1]",$db->fetch_select_data("allowances","id","name",array(),array(),"",true),$data["sel_allowances"][$headername1]);
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
						<?=$f->input("nextSheet","Next","type='submit'","btn_sign");?>
						<?=$f->input("skipSheet","Skip","type='submit'","btn_sign");?>
					</fieldset>
					<?=$f->input("file_name",$file_name,"type='hidden'");?>
				<?=$f->end();?>
			</td></tr></table>	
		</td></tr></table>	
		<?php
		
		
	}//END STEP 2
?>


<?php include_once "footer.php";?>