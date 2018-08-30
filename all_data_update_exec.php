<?php
	error_reporting(0);
	$_enter = "
";
	$_SERVER["DOCUMENT_ROOT"] = "../";
	include_once "common.php";
	include_once "classes/simplexlsx.class.php";
	include_once "func.convert_number_to_words.php";
	include_once "func.allDataUpdate.php";
	$filename = "upload_files/all_data_update_uploader.conf";
	$handle = fopen($filename, "r");
	$config = fread($handle, filesize($filename));
	fclose($handle);
	$configs = explode(chr(13).chr(10),$config);
	
	foreach($configs as $config){
		$_POST = unserialize($config);
		if($_POST["sheet"] > 0 && $_POST["project_id"] == 5){
			$project_id = $_POST["project_id"];
			$year = $_POST["year"];
			$sheet = $_POST["sheet"];
			$header_index = $_POST["header_index"];
			$file_name = $_POST["file_name"];
			$sel_header = $_POST["sel_header"];
			$client_id = $db->fetch_single_data("projects","client_id",array("id"=>$project_id));
			$xlsx = new SimpleXLSX("upload_files/".$file_name);
			$contents = $xlsx->rows($sheet);
			
			echo $_enter."==================================================================".$_enter;
			echo "Client : ".$db->fetch_single_data("clients","name",array("id" => $client_id)).$_enter;
			echo "Project : ".$db->fetch_single_data("projects","name",array("id" => $project_id)).$_enter;
			echo "Year : ".$year.$_enter;
			echo "Sheet : ".$xlsx->sheetNames()[$sheet].$_enter;
			
			$xls_headers = array();
			$xls_headers[""] = "---";
			foreach($contents[$header_index] as $headerindex => $headername){ $xls_headers[$headerindex] = $headername." (".$headerindex.")";}
			
			$num_candidates = 0;
			$num_candidates_update = 0;
			$num_joborders = 0;
			$num_allowances = 0;
			$num_positions = 0;
			$num_alldataupdates = 0;
			$started = false;
			$is_terminated = 0;
			$codes = array();
			foreach($contents as $key1 => $rowdata){
				// if($started && $rowdata[$sel_header["code"]] == "" && $rowdata[$sel_header["no"]] == "" && $rowdata[$sel_header["name"]] == ""){
				if($is_terminated == 1 && $rowdata[$sel_header["name"]] == ""){
					break;
				}
				
				if(strtoupper($rowdata[$sel_header["no"]]) == "ENDFILE") break;
				if(strtoupper($rowdata[$sel_header["no"]]) == "TERMINATED") $is_terminated = 1;
				
				if("OS" == substr($rowdata[$sel_header["code"]],0,2) && $rowdata[$sel_header["name"]] != "" ){
					echo $_enter.$rowdata[$sel_header["code"]].$_enter;
					echo ".";
					$started = true;
					
					$no							= $rowdata[$sel_header["no"]];
					$code						= $rowdata[$sel_header["code"]];//
					$codes[$code]				= 1;
					$name						= $rowdata[$sel_header["name"]];//
					$date_of_birth				= $rowdata[$sel_header["date_of_birth"]];
					$birth 						= explode(",",$date_of_birth);
					if(is_numeric(substr($date_of_birth,0,1))){//jika birthdate tidak diawali dengan birthplace
						$birth[1] 				= $birth[0];
						$birthplace				= "";
					}else{
						$birthplace				= $birth[0];
					}
					$birthdate					= (substr($birth[1],0,1) == " ") ? substr($birth[1],1,strlen($birth[1])-1) : $birth[1];//
					$birthdate					= explode(" ",$birthdate);//
					$birthdate					= $birthdate[2]."-".month_to_num($birthdate[1])."-".$birthdate[0];//
					
					$sex						= $rowdata[$sel_header["sex"]];//
					$status_pajak				= $rowdata[$sel_header["status_pajak"]];//
					$status_asuransi			= $rowdata[$sel_header["status_asuransi"]];//
					$homebase					= $rowdata[$sel_header["homebase"]];
					$project					= $rowdata[$sel_header["project"]];
					$position					= $rowdata[$sel_header["position"]];//
					$join_date					= xls_date($rowdata[$sel_header["join_date"]]);//
					$user						= $rowdata[$sel_header["user"]];//
					$leastday					= $rowdata[$sel_header["leastday"]];
					$remarks					= $rowdata[$sel_header["remarks"]];
					$thp						= $rowdata[$sel_header["thp"]];//
					$basic_salary				= $rowdata[$sel_header["salary"]];//
					$overtime					= (strtolower($rowdata[$sel_header["overtime"]]) == "no") ? "2" : "1";//
					$thr						= (strtolower($rowdata[$sel_header["thr"]]) == "no") ? "2" : "1";//
					$asuransi					= (strtolower($rowdata[$sel_header["asuransi"]]) == "no") ? "2" : "1";//
					$remarks2					= $rowdata[$sel_header["remarks2"]];//
					$benefit					= $rowdata[$sel_header["benefit"]];
					$address					= $rowdata[$sel_header["address"]];//
					$phones						= explode("/",$rowdata[$sel_header["phone"]]);//
					$banks						= explode(":",$rowdata[$sel_header["bank_account"]]);//
					$ktp						= $rowdata[$sel_header["ktp"]];//
					$npwp						= $rowdata[$sel_header["npwp"]];//
					$jamsostek					= $rowdata[$sel_header["jamsostek"]];
					$bpjs						= $rowdata[$sel_header["bpjs"]];
					$email						= $rowdata[$sel_header["email"]];//
					$reason_of_termination		= $rowdata[$sel_header["reason_of_termination"]];//
					$cc							= $rowdata[$sel_header["cc"]];
					$indohr_referral			= $rowdata[$sel_header["indohr_referral"]];//
					$educational_background		= $rowdata[$sel_header["educational_background"]];//
					$pkwt_s						= $sel_header["pkwt"];//
					$break_s					= $sel_header["break"];//
					$amandemens					= $sel_header["amandemen"];//
					$extensions					= $sel_header["extension"];//				
					
					//CANDIDATES
					$candidate_id = $db->fetch_single_data("candidates","id",array("code"=>$code));
					$status_pajak_id = $db->fetch_single_data("statuses","id",array("name"=>$status_pajak));
					$status_asuransi_id = $db->fetch_single_data("statuses","id",array("name"=>$status_asuransi));
					
					if($candidate_id > 0 && $codes[$code] != 1){
						$db->addtable("joborder_allowances");$db->awhere("joborder_id IN (SELECT id FROM joborder WHERE client_id='".$client_id."' AND project_id='".$project_id."' AND candidate_id='".$candidate_id."')");$db->delete_();
						$db->addtable("all_data_update");$db->awhere("joborder_id IN (SELECT id FROM joborder WHERE client_id='".$client_id."' AND project_id='".$project_id."' AND candidate_id='".$candidate_id."')");$db->delete_();
						$db->addtable("joborder");$db->where("candidate_id",$candidate_id);$db->where("client_id",$client_id);$db->where("project_id",$project_id);$db->delete_();
					}
					
					$db->addtable("candidates");
					if($candidate_id > 0) 			$db->where("id",$candidate_id);
					
					$db->addfield("code");			$db->addvalue($code);
					$db->addfield("name");			$db->addvalue($name);
					$db->addfield("birthdate");		$db->addvalue($birthdate);
					$db->addfield("birthplace");	$db->addvalue($birthplace);
					$db->addfield("sex");			$db->addvalue(strtoupper($sex));
					$db->addfield("status_id");		$db->addvalue($status_pajak_id);
					$db->addfield("address");		$db->addvalue($address);
					$db->addfield("phone");			$db->addvalue(str_replace(array(" ",chr(13),chr(10)),"",$phones[0]));
					$db->addfield("phone_2");		$db->addvalue(str_replace(array(" ",chr(13),chr(10)),"",$phones[1]));
					$db->addfield("ktp");			$db->addvalue($ktp);
					$db->addfield("email");			$db->addvalue($email);
					$db->addfield("bank_name");		$db->addvalue(str_replace(array(" ",chr(13),chr(10)),"",$banks[0]));
					$db->addfield("bank_account");	$db->addvalue(str_replace(array(" ",chr(13),chr(10)),"",$banks[1]));
					$db->addfield("npwp");			$db->addvalue($npwp);
					$db->addfield("join_indohr_at");$db->addvalue($join_date);
					if(!$candidate_id){
						$inserting = $db->insert();
						if($inserting["affected_rows"] > 0) $num_candidates++;
						// echo "<br> Insert Candidate $code";
						echo ".";
					} else {
						$inserting = $db->update();
						if($inserting["affected_rows"] > 0) $num_candidates_update++;
						// echo "<br> Update Candidate $code";
						echo ".";
					}
					
					if($inserting["affected_rows"] > 0){
						
						if(!$candidate_id) $candidate_id = $inserting["insert_id"];
						
						$candidate_educations_id = $db->fetch_single_data("candidate_educations","id",array("candidate_id"=>$candidate_id));
						if($educational_background && !$candidate_educations_id){
							$degree_id = "";
							$educational = strtolower(str_replace(array(" ","-","/",chr(10),chr(13)),"",$educational_background));
							if(strpos(" ".$educational,"senior") > 0 && $degree_id == "") $degree_id = 1;
							if(strpos(" ".$educational,"menengah") > 0 && $degree_id == "") $degree_id = 1;
							if(strpos(" ".$educational,"smu") > 0 && $degree_id == "") $degree_id = 1;
							if(strpos(" ".$educational,"sma") > 0 && $degree_id == "") $degree_id = 1;
							if(strpos(" ".$educational,"smk") > 0 && $degree_id == "") $degree_id = 1;
							if(strpos(" ".$educational,"stm") > 0 && $degree_id == "") $degree_id = 1;
							if(strpos(" ".$educational,"d1") > 0 && $degree_id == "") $degree_id = 2;
							if(strpos(" ".$educational,"d2") > 0 && $degree_id == "") $degree_id = 2;
							if(strpos(" ".$educational,"d3") > 0 && $degree_id == "") $degree_id = 2;
							if(strpos(" ".$educational,"diploma") > 0 && $degree_id == "") $degree_id = 2;
							if(strpos(" ".$educational,"s1") > 0 && $degree_id == "") $degree_id = 3;
							if(strpos(" ".$educational,"s2") > 0 && $degree_id == "") $degree_id = 4;
							if(strpos(" ".$educational,"s3") > 0 && $degree_id == "") $degree_id = 5;
							if($degree_id){
								$db->addtable("candidate_educations");
								$db->addfield("candidate_id");	$db->addvalue($candidate_id);
								$db->addfield("degree_id");		$db->addvalue($degree_id);
								$db->insert();
								// echo "<br> Insert Candidate Education $code / $degree_id";
								echo ".";
							}
						}
					}
					
					//INSERT JOB ORDER		
					$arrposition_id	= array();
					foreach(position_names($position) as $key => $_position){
						$position_id = $db->fetch_single_data("positions","id",array("name"=>$_position.":LIKE"));
						if($position_id <=0 ){
							$db->addtable("positions");
							$db->addfield("name");				$db->addvalue($_position);
							$inserting = $db->insert();
							if($inserting["affected_rows"] > 0){
								$num_positions++;
								$position_id = $inserting["insert_id"];
							}
						}
						$arrposition_id[] = $position_id;
					}
					
					$position_id = sel_to_pipe($arrposition_id);
					
					$first_joborder_id = 0;
					
					foreach($pkwt_s as $_pkwt_for => $pkwt_index){
						$pkwt_for = $sel_header["pkwt_ke"][$_pkwt_for];
						/* foreach($break_s as $break_for => $break_index){
							if($pkwt_index > $break_index) $pkwt_for = 0; // asumsi hanya ada 1 pkwt setelah break
							if(stripos(" ".$xls_headers[$pkwt_index],"iii") > 0) $pkwt_for = 2;
						} */
						$pkwt_from = xls_date($rowdata[$pkwt_index]);
						$pkwt_to = xls_date($rowdata[$pkwt_index+1]);
						if($pkwt_for == 0) $status_category_id = 1; else $status_category_id = 2;
						if(strpos(" ".strtolower($indohr_referral),"indohr") > 0) $status_category_id = 6;
						if(strpos(" ".strtolower($indohr_referral),"refer") > 0) $status_category_id = 5;
						
						if($pkwt_from || $pkwt_to){
							$inserting = insert_jo(0,$client_id,$project_id,0,$pkwt_for,$position_id,$user,$candidate_id,$pkwt_from,$pkwt_to,$status_category_id,$remarks2,$thp,$basic_salary,$overtime,$thr,$asuransi);
							if($inserting["affected_rows"] > 0){
								// echo "<br> Insert Job Order $code / ".$inserting["insert_id"];
								echo ".";
								if(!$first_joborder_id) $first_joborder_id = $inserting["insert_id"];
								$num_joborders++;
							}
						}
					}
					
					foreach($break_s as $break_for => $break_index){
						$break_ke = $sel_header["break_ke"][$break_for];
						$break_ke = "";
						$pkwt_from = xls_date($rowdata[$break_index]);
						$pkwt_to = xls_date($rowdata[$break_index+1]);
						if($break_for == 0) $status_category_id = 1; else $status_category_id = 2;
						if(strpos(" ".strtolower($indohr_referral),"indohr") > 0) $status_category_id = 6;
						if(strpos(" ".strtolower($indohr_referral),"refer") > 0) $status_category_id = 5;
						
						if($pkwt_from || $pkwt_to){
							$inserting = insert_jo(0,$client_id,$project_id,0,"break".$break_ke,$position_id,$user,$candidate_id,$pkwt_from,$pkwt_to,$status_category_id,$remarks2,$thp,$basic_salary,$overtime,$thr,$asuransi);
							if($inserting["affected_rows"] > 0){
								// echo "<br> Insert Job Order (Break) $code / ".$inserting["insert_id"];
								echo ".";
								$num_joborders++;
							}
						}
					}
					
					foreach($amandemens as $pkwt_for => $amandemen_index){
						$looping = true;
						$pkwt_index = $_POST["sel_header"]["pkwt"][$pkwt_for];
						foreach($break_s as $break_for => $break_index){
							/////if($pkwt_index > $break_index) $pkwt_for = 0; // asumsi hanya ada 1 pkwt setelah break
						}
						$pkwt_from = xls_date($rowdata[$pkwt_index]);
						$joborder_id = $db->fetch_single_data("joborder","id",array("pkwt_for"=>$pkwt_for,"client_id"=>$client_id,"project_id"=>$project_id,"candidate_id"=>$candidate_id,"join_start"=>$pkwt_from.":<="),array("id DESC"));
						$xx = $amandemen_index;
						while($looping){
							$amandemen = xls_date($rowdata[$xx]);
							if($pkwt_from && $amandemen){
								$inserting = insert_jo($joborder_id,$client_id,$project_id,1,"amandemen",$position_id,$user,$candidate_id,$pkwt_from,$amandemen,0,$remarks2,$thp,$basic_salary,$overtime,$thr,$asuransi);
								if($inserting["affected_rows"] > 0){
									// echo "<br> Insert Job Order (Amandemen) $code / ".$joborder_id." / ".$inserting["insert_id"];
									echo ".";
									$num_joborders++;
								}
							}
							
							$xx++;
							
							if(in_array($xx,$_POST["sel_header"]) 
								|| in_array($xx,$_POST["sel_header"]["pkwt"]) 
								|| in_array($xx,$_POST["sel_header"]["break"]) 
								|| $xx > 100) $looping = false;
						}
					}
					
					foreach($extensions as $pkwt_for => $extension_index){
						$looping = true;
						$pkwt_index = $_POST["sel_header"]["pkwt"][$pkwt_for];
						foreach($break_s as $break_for => $break_index){
							/////if($pkwt_index > $break_index) $pkwt_for = 0; // asumsi hanya ada 1 pkwt setelah break
						}
						$pkwt_from = xls_date($rowdata[$pkwt_index]);
						$joborder_id = $db->fetch_single_data("joborder","id",array("pkwt_for"=>$pkwt_for,"client_id"=>$client_id,"project_id"=>$project_id,"candidate_id"=>$candidate_id,"join_start"=>$pkwt_from.":<="),array("id DESC"));
						$xx = $extension_index;
						while($looping){
							$extension = xls_date($rowdata[$xx]);
							if($pkwt_from && $extension){
								$inserting = insert_jo($joborder_id,$client_id,$project_id,0,"extension",$position_id,$user,$candidate_id,$pkwt_from,$extension,0,$remarks2,$thp,$basic_salary,$overtime,$thr,$asuransi);
								if($inserting["affected_rows"] > 0){
									// echo "<br> Insert Job Order (Extension) $code / ".$joborder_id." / ".$inserting["insert_id"];
									echo ".";
									$num_joborders++;
								}
							}
							
							$xx++;
							if(in_array($xx,$_POST["sel_header"]) 
								|| in_array($xx,$_POST["sel_header"]["pkwt"]) 
								|| in_array($xx,$_POST["sel_header"]["break"]) 
								|| $xx > 100) $looping = false;
						}
					}
					
					$joborder_id = $db->fetch_single_data("joborder","id",array("pkwt_for"=>"0","client_id"=>$client_id,"candidate_id"=>$candidate_id),array("id DESC"));
					$joborder_ids = $db->fetch_select_data("joborder","id","concat(id) as id2",array("client_id"=>$client_id,"candidate_id"=>$candidate_id));
					
					foreach($_POST["sel_allowances"] as $all_index => $allowance_id){
						$price = $rowdata[$all_index] * 1;
						if($price > 0){
							foreach($joborder_ids as $jo_id){
								$db->addtable("joborder_allowances");
								$db->addfield("joborder_id");	$db->addvalue($jo_id);
								$db->addfield("allowance_id");	$db->addvalue($allowance_id);
								$db->addfield("price");			$db->addvalue($price);
								$inserting = $db->insert();
								if($inserting["affected_rows"] > 0){
									// echo "<br> Insert Allowance $code / ".$joborder_id." / ".$allowance_id." / ".$price;
									echo ".";
									$num_allowances++;
								}
							}
						}
					}
					
					//INSERT ALL DATA UPDATE
					$db->addtable("all_data_update");
					$db->addfield("joborder_id");			$db->addvalue($first_joborder_id);
					$db->addfield("candidate_id");			$db->addvalue($candidate_id);
					$db->addfield("tax_status_id");			$db->addvalue($status_pajak_id);
					$db->addfield("medical_status_id");		$db->addvalue($status_asuransi_id);
					$db->addfield("original_join_date");	$db->addvalue($join_date);
					$db->addfield("code");					$db->addvalue($code);
					// $db->addfield("homebase_ids");			$db->addvalue($homebase_ids);
					$db->addfield("position_ids");			$db->addvalue($position_id);
					$db->addfield("user");					$db->addvalue($user);
					$db->addfield("project_ids");			$db->addvalue("|".$project_id."|");
					$db->addfield("salary_thp");			$db->addvalue($thp);
					$db->addfield("remarks");				$db->addvalue($remarks2);
					if($codes[$code] == 1 && false){
						$db->addfield("is_terminated");			$db->addvalue("0");
					} else {
						$db->addfield("is_terminated");			$db->addvalue($is_terminated);
					}
					$db->addfield("reason_of_termination");	$db->addvalue($reason_of_termination);
					$inserting = $db->insert();
					if($inserting["affected_rows"] > 0){
						$num_alldataupdates++;
					}
				}
			}
			
			echo $_enter;
			echo "Candidate : ".$num_candidates.$_enter;
			echo "Candidate Update : ".$num_candidates_update.$_enter;
			echo "Job Order : ".$num_joborders.$_enter;
			echo "Allowance : ".$num_allowances.$_enter;
			echo "Positions : ".$num_positions.$_enter;
			echo "All Data Update : ".$num_alldataupdates.$_enter;
			echo "==================================================================".$_enter.$_enter;
		}
	}
	
?>
