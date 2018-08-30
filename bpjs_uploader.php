<?php	
	set_time_limit(0);
	ini_set('memory_limit', '-1');
?>
<?php include_once "head.php";?>
<?php include_once "classes/simplexlsx.class.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php
	function position_names($position){
		$arr = explode("=>",$position);
		foreach($arr as $key => $_position){
			$x = explode("(",$_position);
			$return[] = trim($x[0]);
		}
		return $return;
	}
	
	function  month_to_num($month){
		$month = strtolower(str_replace(array(" ",chr(10),chr(13)),"",$month));
		if(substr($month,0,3) == "jan") return "01";
		if(substr($month,0,3) == "feb" || substr($month,0,3) == "peb") return "02";
		if(substr($month,0,3) == "mar") return "03";
		if(substr($month,0,3) == "apr") return "04";
		if(substr($month,0,3) == "mei" || substr($month,0,3) == "may") return "05";
		if(substr($month,0,3) == "jun") return "06";
		if(substr($month,0,3) == "jul") return "07";
		if(substr($month,0,3) == "aug" || substr($month,0,3) == "agt" || substr($month,0,3) == "agu") return "08";
		if(substr($month,0,3) == "sep") return "09";
		if(substr($month,0,1) == "o") return "10";
		if(substr($month,0,1) == "n") return "11";
		if(substr($month,0,2) == "de") return "12";
	}
	
	if(isset($_POST["step2"])) {
		$project_id = $_POST["project_id"];
		$client_id = $db->fetch_single_data("projects","client_id",array("id"=>$project_id));
		$year = $_POST["year"];
		$sheet = $_POST["sheet"];
		$file_name = $_POST["file_name"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($_POST["sheet"]);
		//////////////$contents_ex = $xlsx->rowsEx($_POST["sheet"]);
		
		$col = array();
		foreach($contents as $key1 => $rowdata){
			if($rowdata[2] != "" && $rowdata[4] != ""){//penentuan kolom dari header
				foreach($rowdata as $key => $header){
					if(preg_match("/(no)/",strtolower($header)) 							&& !isset($col["no"]))				$col["no"] = $key;
					if(preg_match("/(code)/",strtolower($header)) 							&& !isset($col["code"]))			$col["code"] = $key;
					if(preg_match("/(nam[a,e])/",strtolower($header)) 						&& !isset($col["name"]))			$col["name"] = $key;
					if(preg_match("/(date)*(birth)/",strtolower($header)) 					&& !isset($col["date_of_birth"]))	$col["date_of_birth"] = $key;
					if(preg_match("/(sex)/",strtolower($header)) 							&& !isset($col["sex"]))				$col["sex"] = $key;
					if(preg_match("/(status)/",strtolower($header)) 						&& !isset($col["status_pajak"]))	$col["status_pajak"] = $key;
					if(preg_match("/(pisa)/",strtolower($header)) 							&& !isset($col["pisa"]))			$col["pisa"] = $key;
					if(preg_match("/(pkwt)/",strtolower($header)) 							&& !isset($col["pkwt"]))			$col["pkwt"] = $key;
					if(preg_match("/(salary)/",strtolower($header))
						&& !isset($col["salary"]))																				$col["salary"] = $key;
					if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"ktp") > 0
						&& !isset($col["ktp"]))																					$col["ktp"] = $key;
					if(preg_match("/(bpjs)/",strtolower($header)) 							&& !isset($col["bpjs"]))			$col["bpjs"] = $key;
					if(preg_match("/(mail)/",strtolower($header)) 							&& !isset($col["email"]))			$col["email"] = $key;
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
				<?=$f->start();?>
					<?=$f->input("client_id",$client_id,"type='hidden'");?>
					<?=$f->input("project_id",$project_id,"type='hidden'");?>
					<?=$f->input("year",$year,"type='hidden'");?>
					<?=$f->input("sheet",$sheet,"type='hidden'");?>
					<?=$f->input("header_index","$key1","type='hidden'");?>
					<fieldset>
						<table>
							<tr><td>Client</td><td> : <?=$db->fetch_single_data("clients","name",array("id" => $client_id));?></td></tr>
							<tr><td>Project</td><td> : <?=$db->fetch_single_data("projects","name",array("id" => $project_id));?></td></tr>
							<tr><td>Year</td><td> : <?=$year;?></td></tr>
							<tr><td>Sheet</td><td> : <?=$xlsx->sheetNames()[$sheet];?></td></tr>
						</table>
						<hr>
						<table>
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
		$client_id = $_POST["client_id"];
		$project_id = $_POST["project_id"];
		$year = $_POST["year"];
		$sheet = $_POST["sheet"];
		$header_index = $_POST["header_index"];
		$file_name = $_POST["file_name"];
		$sel_header = $_POST["sel_header"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($sheet);
		
		$xls_headers = array();
		$xls_headers[""] = "---";
		foreach($contents[$header_index] as $headerindex => $headername){ $xls_headers[$headerindex] = $headername." (".$headerindex.")";}
		$num_updateCandidates = 0;
		$num_insertCandidates = 0;
		echo "<table border='1'>";
		foreach($contents as $key1 => $rowdata){
			// if($is_terminated == 1 && $rowdata[$sel_header["name"]] == ""){
				// break;
			// }
				if("OS" == substr($rowdata[$sel_header["code"]],0,2) && $rowdata[$sel_header["name"]] != ""){
					$started = true;
				}
				if($started){
					$no							= $rowdata[$sel_header["no"]];
					$code						= trim($rowdata[$sel_header["code"]]);
					$name						= $rowdata[$sel_header["name"]];
					$date_of_birth				= str_replace([chr(10),chr(13)],"",$rowdata[$sel_header["date_of_birth"]]);
					$birth 						= explode(",",$date_of_birth);
					$sex						= trim(strtoupper($rowdata[$sel_header["sex"]]));
					$status_pajak				= $rowdata[$sel_header["status_pajak"]];
					$pisa						= trim(strtolower($rowdata[$sel_header["pisa"]]));
					$pkwt						= xls_date($rowdata[$sel_header["pkwt"]]);
					$salary						= $rowdata[$sel_header["salary"]];
					$ktp						= $rowdata[$sel_header["ktp"]] * 1;
					$bpjs						= $rowdata[$sel_header["bpjs"]] * 1;
					$email						= $rowdata[$sel_header["email"]];
					
					if(strpos($date_of_birth,", ") > 0){
						$arrtemp = explode(", ",$date_of_birth); $date_of_birth = $arrtemp[1]; 
					}
					if(strlen($date_of_birth) == 10 && strpos($date_of_birth,"-") > 0){
						$arr = explode("-",$date_of_birth);
						if(strlen($arr[2]) == 4) $date_of_birth = $arr[2]."-".$arr[1]."-".$arr[0];
						if(strlen($arr[0]) == 4) $date_of_birth = $arr[0]."-".$arr[1]."-".$arr[2];
					}
					if(strlen($date_of_birth) == 10 && strpos($date_of_birth,"/") > 0){
						$arr = explode("/",$date_of_birth);
						if(strlen($arr[2]) == 4) $date_of_birth = $arr[2]."-".$arr[1]."-".$arr[0];
						if(strlen($arr[0]) == 4) $date_of_birth = $arr[0]."-".$arr[1]."-".$arr[2];
					}
					if(strlen($date_of_birth) == 5) { $date_of_birth = xls_date($date_of_birth); }
					$arr = explode(" ",$date_of_birth);
					if(count($arr) == 3 && strlen($arr[2]) == 4 && strlen($arr[0]) > 2){
						$date_of_birth = $arr[2]."-".month_to_num($arr[0])."-".substr("00",0,2-strlen($arr[1])).$arr[1];
					} else if(count($arr) == 4 && strlen($arr[3]) == 4){
						$date_of_birth = $arr[3]."-".month_to_num($arr[2])."-".substr("00",0,2-strlen($arr[1])).$arr[1];
					} else if(count($arr) == 3 && strlen($arr[2]) == 4){
						$date_of_birth = $arr[2]."-".month_to_num($arr[1])."-".substr("00",0,2-strlen($arr[0])).$arr[0];
					}
					
					if($bpjs > 0) $bpjs = substr("0000000000000",0,13-strlen($bpjs)).$bpjs;
					else $bpjs = "";

					if($ktp == 0) $ktp = "";

					if($code) $lastcode = $code;
					else if($name!="") $code = $lastcode;
					if($code){
						echo "<tr>";
						echo "<td nowrap>".$no."</td>";
						echo "<td nowrap>".$code."</td>";
						echo "<td nowrap>".$name."</td>";
						echo "<td nowrap>".$date_of_birth."</td>";
						echo "<td nowrap>".$sex."</td>";
						echo "<td nowrap>".$status_pajak."</td>";
						echo "<td nowrap>".$pisa."</td>";
						echo "<td nowrap>".$pkwt."</td>";
						echo "<td nowrap>".$salary."</td>";
						echo "<td nowrap>".$ktp."</td>";
						echo "<td nowrap>".$bpjs."</td>";
						echo "<td nowrap>".$email."</td>";
						echo "</tr>";
						
						$status_id = $db->fetch_single_data("statuses","id",["name"=>$status_pajak.":LIKE"]);
						if($pisa == "peserta"){
							$candidates_existed = $db->fetch_single_data("candidates","id",["code" => $code]);
							$db->addtable("candidates");
							$db->addfield("name");			$db->addvalue($name);
							$db->addfield("birthdate");		$db->addvalue($date_of_birth);
							$db->addfield("sex");			$db->addvalue($sex);
							$db->addfield("status_id");		$db->addvalue($status_id);
							$db->addfield("ktp");			$db->addvalue($ktp);
							$db->addfield("email");			$db->addvalue($email);
							$db->addfield("updated_at");	$db->addvalue($__now);
							$db->addfield("updated_by");	$db->addvalue($__username);
							$db->addfield("updated_ip");	$db->addvalue($__remoteaddr);
							if($candidates_existed > 0){
								$db->where("code",$code);
								$updating = $db->update();
								if($updating["affected_rows"] > 0){
									$num_updateCandidates++;
								}
							} else {
								$db->addfield("code");		$db->addvalue($code);
								
								$db->addfield("created_at");$db->addvalue($__now);
								$db->addfield("created_by");$db->addvalue($__username);
								$db->addfield("created_ip");$db->addvalue($__remoteaddr);
								$inserting = $db->insert();
								if($inserting["affected_rows"] > 0){
									$_inserted[$code] = 1;
									$num_insertCandidates++;
								}
							}
						}
					}
				}
		}
		echo "</table>";
		echo "<b>";
		echo "<font color='blue'>Data Uploaded</font><br><br>";
		echo "Inserted Candidates : ".$num_insertCandidates."<br>";
		echo "Updated Candidates : ".$num_updateCandidates."<br>";
		echo "</b>";
		echo $f->input("refresh","Refresh","type='button' onclick=\"window.location='?';\"","btn_sign");
		//unlink("upload_files/".$file_name);
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
					Choose Sheet: <?=$f->select("sheet",$xlsx->sheetNames());?><br>
					Project : <?=$f->select("project_id",$db->fetch_select_data("projects","id","name",array(),array(),"",true));?><br>
					<?php for($year = date("Y");$year > 2010 ; $year--){$years[$year] = $year;} ?>
					Year : <?=$f->select("year",$years);?><br><br>
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