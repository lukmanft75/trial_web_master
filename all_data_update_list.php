<?php
	if($_GET["export"]){
		$_exportname = "All_Data_Update.xls";
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$_exportname);
		header("Pragma: no-cache");
		header("Expires: 0");
		$_GET["do_filter"]="Load";
		$_isexport = true;
	}
	include_once "head.php";
?>
<?php
	if($_POST["move_to_terminated"]){
		$db->addtable("all_data_update");
		$db->where("id",$_POST["all_data_update_id"]);
		$db->addfield("is_terminated");			$db->addvalue(1);
		$db->addfield("reason_of_termination");	$db->addvalue($_POST["reason_of_termination"]);
		$db->addfield("date_of_termination");	$db->addvalue($_POST["date_of_termination"]);
		$db->update();
	}
	if($_GET["deleting"]){
		$db->addtable("all_data_update");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
	$_GET["client"] = $db->fetch_single_data("projects","client_id",array("id"=>$_GET["project"]));
?>
<?php if(!$_isexport){ ?>
	<div class="bo_title">All Data Update</div>
	<?=$f->start("filter","GET");?>
	<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
	<div id="bo_filter">
		<div id="bo_filter_container">
				<?=$t->start();?>
				<?php
				
					$code = $f->input("code",$_GET["code"]);
					$name = $f->input("name",$_GET["name"]);
					$client = $f->select("client",$db->fetch_select_data("clients","id","name",array(),array("name"),"",true),$_GET["client"],"style='height:25px;'");
					$project = $f->select("project",$db->fetch_select_data("projects","id","name",array(),array("name"),"",true),$_GET["project"],"style='height:25px;'");
					$tax_status = $f->select("tax_status",$db->fetch_select_data("statuses","id","name",array(),array("id"),"",true),$_GET["position"],"style='height:25px;'");
					$medical_status = $f->select("medical_status",$db->fetch_select_data("statuses","id","name",array("only_marital" => "1"),array("id"),"",true),$_GET["position"],"style='height:25px;'");
					$user = $f->input("user",$_GET["user"]);
				?>
				<?=$t->row(array("Code",$code));?>
				<?=$t->row(array("Candidate Name",$name));?>
				<?=$t->row(array("Tax Status",$tax_status));?>
				<?=$t->row(array("Medical Status",$medical_status));?>
				<?=$t->row(array("User",$user));?>
				<?=$t->end();?>
				<?=$f->input("page","1","type='hidden'");?>
				<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
		</div>
	</div>
	<?=$t->start();?>
		<?=$t->row(array("Project",$project));?>
	<?=$t->end();?>
	<?=$f->input("do_filter","Load","type='submit' style='width:180px;'");?>
	<?=$f->input("export","Export to Excel","type='submit' style='width:180px;'");?>
	<?=$f->input("reset","Reset","type='button' style='width:180px;' onclick=\"window.location='?';\"");?>
	<?=$f->end();?>
	<br><?=$f->input("add","Add All Data Update","type='button' style='width:180px;' onclick=\"window.location='all_data_update_add.php';\"");?><br><br>
<?php } else { ?>
	<h2><b>All Data Update</b></h2>
	<b><?=$db->fetch_single_data("projects","name",array("id" => $_GET["project"]));?></b>
<?php } ?>

<?php
	
	$whereclause = "";
	if(@$_GET["project"]!="") $whereclause .= "project_ids LIKE '%|".$_GET["project"]."|' AND ";
    if(@$_GET["code"]!="") $whereclause .= "(code LIKE '%".$_GET["code"]."%') AND ";
	if(@$_GET["name"]!="") $whereclause .= "(candidate_id IN (SELECT id FROM candidates WHERE name LIKE '%".$_GET["name"]."%')) AND ";
	if(@$_GET["client"]!="") $whereclause .= "(joborder_id IN (SELECT id FROM joborder WHERE client_id = '".$_GET["client"]."')) AND ";
	if(@$_GET["tax_status"]!="") $whereclause .= "(tax_status_id = '".$_GET["tax_status"]."') AND ";
	if(@$_GET["medical_status"]!="") $whereclause .= "(medical_status_id = '".$_GET["medical_status"]."') AND ";
	if(@$_GET["user"]!="") $whereclause .= "(user LIKE '%".$_GET["user"]."%') AND ";
	
	if($_GET["project"] > 0 && $_GET["client"] > 0){
		$project_id = $_GET["project"];
		$client_id = $_GET["client"];
		
		$db->addtable("all_data_update");
		if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
		$db->order("is_terminated");
		$all_data_updates = $db->fetch_data(true);
		//collect data
		$arrdata = array();
		$arrheaders = array();
		$arrheaders["code"] = "Code";
		$arrheaders["name"] = "Name";
		$arrheaders["birthdate"] = "Date Of Birth";
		$arrheaders["sex"] = "Sex";
		$arrheaders["tax_status"] = "Tax<br>Status";
		$arrheaders["medical_status"] = "Medical<br>Status";
		$arrheaders["homebase"] = "Homebase";
		$arrheaders["project"] = "Project";
		$arrheaders["position"] = "Position";
		$arrheaders["original_join_date"] = "Original Join Date";
		$arrheaders["user"] = "User/Manager";
		$_remarks = "";
		foreach($all_data_updates as $key1 => $all_data_update){
			$candidate_id = $all_data_update["candidate_id"];
			$arrdata[$key1]["id"] = $all_data_update["id"];
			$adu_joborder_id = $all_data_update["joborder_id"];
			$arrdata[$key1]["code"] = "<a href='all_data_update_edit.php?id=".$all_data_update["id"]."' target='_BLANK'>".$all_data_update["code"]."</a>";
			$arrdata[$key1]["name"] = "<a href='all_data_update_edit.php?id=".$all_data_update["id"]."' target='_BLANK'>".$db->fetch_single_data("candidates","name",array("id" => $candidate_id))."</a>";
			$arrdata[$key1]["birthdate"] = format_tanggal($db->fetch_single_data("candidates","birthdate",["id" => $candidate_id]),"dMY");
			$birthplace = $db->fetch_single_data("candidates","birthplace",["id" => $candidate_id]);
			if($birthplace != "") $arrdata[$key1]["birthdate"] = $birthplace.", ".$arrdata[$key1]["birthdate"];
			$arrdata[$key1]["sex"] = $db->fetch_single_data("candidates","sex",array("id" => $candidate_id));
			$arrdata[$key1]["tax_status"] = $db->fetch_single_data("statuses","name",array("id"=>$all_data_update["tax_status_id"]));
			$arrdata[$key1]["medical_status"] = $db->fetch_single_data("statuses","name",array("id"=>$all_data_update["medical_status_id"]));

			$arrhomebases = pipetoarray($all_data_update["homebase_ids"]); $homebases = "";
			foreach($arrhomebases as $homebase_id){ if($homebase_id > 0) $homebases .= $db->fetch_single_data("homebases","name",array("id"=>$homebase_id))."<br>"; }
			if(!$_isexport) $homebases .= "<img src='icons/search_window.png' onclick='subwindow_homebases(\"".$all_data_update["id"]."\");'>";
			$arrdata[$key1]["homebase"] = $homebases;

			$arrprojects = pipetoarray($all_data_update["project_ids"]); $projects = "";
			foreach($arrprojects as $project_id){ if($project_id > 0) $projects .= $db->fetch_single_data("projects","name",array("id"=>$project_id))."<br>"; }
			if(!$_isexport) $projects .= "<img src='icons/search_window.png' onclick='subwindow_projects(\"".$all_data_update["id"]."\");'>";
			$arrdata[$key1]["project"] = $projects;
			
			$arrpositions = pipetoarray($all_data_update["position_ids"]); $positions = "";
			foreach($arrpositions as $position_id){ if($position_id > 0) $positions .= $db->fetch_single_data("positions","name",array("id"=>$position_id))."<br>"; }
			if(!$_isexport) $positions .= "<img src='icons/search_window.png' onclick='subwindow_positions(\"".$all_data_update["id"]."\");'>";
			$arrdata[$key1]["position"] = $positions;
			
			$arrdata[$key1]["original_join_date"] = $all_data_update["original_join_date"];
			$arrdata[$key1]["user"] = $all_data_update["user"];
			
			//cari joborder id pertama
			$first_joborder_id = 0;
			$first_joborder_date = "";
			$db->addtable("joborder");$db->awhere("joborder_id='0' AND pkwt_for='0' AND client_id = '".$client_id."' AND project_id = '".$project_id."' AND candidate_id='".$candidate_id."' ORDER BY join_start DESC LIMIT 2");
			foreach($db->fetch_data(true) as $pkwt_1){ $first_joborder_id = $pkwt_1["id"]; $first_joborder_date = $pkwt_1["join_start"];}
			
			$db->addtable("joborder");$db->awhere("join_start >= '".$first_joborder_date."' AND joborder_id='0' AND (pkwt_for REGEXP '[0-9]+' OR pkwt_for = 'break') AND client_id = '".$client_id."' AND project_id = '".$project_id."' AND candidate_id='".$candidate_id."' ORDER BY join_start");
			// $db->addtable("joborder");$db->awhere("id='".$adu_joborder_id."' AND join_start >= '".$first_joborder_date."' AND joborder_id='0' AND (pkwt_for REGEXP '[0-9]+' OR pkwt_for = 'break') AND client_id = '".$client_id."' AND project_id = '".$project_id."' AND candidate_id='".$candidate_id."' ORDER BY join_start");
			$for = 0;
			$jo_s = $db->fetch_data(true);
			// echo $db->get_last_query()."<br>";
			$latest_join_end = "";
			$lates_jo_id = "";
			foreach($jo_s as $jo){
				/* if(in_array("1",$arrheaders["pkwt"]) && !$arrheaders["pkwt"][$for] && $jo["pkwt_for"] == 2){
					$arrheaders["pkwt"][$for] = "break";
					$for++;
				} */
				if(!$arrheaders["pkwt"][$for]) $arrheaders["pkwt"][$for] = $jo["pkwt_for"];
				$arrdata[$key1]["pkwt"][$for]["id"] = $jo["id"];
				$arrdata[$key1]["pkwt"][$for]["joborder_id"] = $jo["joborder_id"];
				$arrdata[$key1]["pkwt"][$for]["pkwt_for"] = $jo["pkwt_for"];
				$arrdata[$key1]["pkwt"][$for]["join_start"] = $jo["join_start"];
				$arrdata[$key1]["pkwt"][$for]["join_end"] = $jo["join_end"];
				$_remarks = $jo["remarks"];
				if($latest_join_end < $jo["join_end"]){
					$latest_join_end = $jo["join_end"];
					$lates_jo_id = $jo["id"];
				}
				$db->addtable("joborder");$db->awhere("joborder_id = '".$jo["id"]."' AND pkwt_for IN ('amandemen','extension') ORDER BY join_end");
				$jo_s1 = $db->fetch_data(true);
				foreach($jo_s1 as $jo1_key => $jo1){
					$jo1["pkwt_for"] = "extension";
					if($num_pkwt_child[$for] <= ($jo1_key+1) && isset($jo1_key)) $num_pkwt_child[$for] = ($jo1_key+1);
					$arrdata[$key1]["pkwt"][$for][$jo1["pkwt_for"]][$jo1_key]["id"] = $jo1["id"];
					$arrdata[$key1]["pkwt"][$for][$jo1["pkwt_for"]][$jo1_key]["join_start"] = $jo1["join_start"];
					$arrdata[$key1]["pkwt"][$for][$jo1["pkwt_for"]][$jo1_key]["join_end"] = $jo1["join_end"];
					$_remarks = $jo1["remarks"];
					if($latest_join_end < $jo1["join_end"]){
						$latest_join_end = $jo1["join_end"];
						$lates_jo_id = $jo1["id"];
					}
				}
				$for++;
			}
			
			$arrdata[$key1]["latest_join_end"] = $latest_join_end;
			$leastday = day_diff(date("Y-m-d"),$latest_join_end);
			$arrdata[$key1]["leastday"] = $leastday;
			if($leastday > 40) $arrdata[$key1]["remarks"] = "Active";
			if($leastday <= 40) $arrdata[$key1]["remarks"] = "Warning";
			if($leastday < 0) $arrdata[$key1]["remarks"] = "Expired";
			$arrheaders["leastday"] = "Least Day";
			$arrheaders["remarks"] = "Remarks";
			
			$thp = $db->fetch_single_data("joborder","thp",array("id"=>$lates_jo_id)) * 1;
			if($thp > 0){
				$arrdata[$key1]["thp"] = $thp;
				$arrheaders["thp"] = "THP";
			}
			$basic_salary = $db->fetch_single_data("joborder","basic_salary",array("id"=>$lates_jo_id)) * 1;
			if($basic_salary > 0){
				$arrdata[$key1]["basic_salary"] = $basic_salary;
				$arrheaders["basic_salary"] = "Basic Salary";
			}
			
			//allowances
			$arrdata[$key1]["allowances_jo_id"] = $lates_jo_id;
			$db->addtable("joborder_allowances");$db->where("joborder_id",$lates_jo_id);//$db->order("id");
			$jo_allws = $db->fetch_data(true);
			foreach($jo_allws as $allw_for => $jo_allw){
				$arrdata[$key1]["allowances"][$jo_allw["allowance_id"]] = $jo_allw["price"];
				$arrheaders["allowances"][$jo_allw["allowance_id"]] = $db->fetch_single_data("allowances","name",array("id" => $jo_allw["allowance_id"]))." Allw.";
			}
			
			$arrdata[$key1]["ot"] = ($db->fetch_single_data("joborder","overtime",array("id"=>$lates_jo_id)) == 2) ? "No" : "Yes";
			$arrdata[$key1]["thr"] = ($db->fetch_single_data("joborder","thr",array("id"=>$lates_jo_id)) == 2) ? "No" : "Yes";
			$arrdata[$key1]["asuransi"] = ($db->fetch_single_data("joborder","asuransi",array("id"=>$lates_jo_id)) == 2) ? "No" : "Yes";
			// $arrdata[$key1]["remarks2"] = $all_data_update["remarks"];
			$arrdata[$key1]["remarks2"] = str_replace(chr(13).chr(10),"<br>",$_remarks);
			$arrdata[$key1]["address"] = $db->fetch_single_data("candidates","address",array("id" => $candidate_id));
			$arrdata[$key1]["phone"] = $db->fetch_single_data("candidates","concat(phone,';',phone_2)",array("id" => $candidate_id));
			$arrdata[$key1]["bank_account"] = $db->fetch_single_data("candidates","concat(bank_name,': ',bank_account)",array("id" => $candidate_id));
			$arrdata[$key1]["ktp"] = $db->fetch_single_data("candidates","ktp",array("id" => $candidate_id));
			$arrdata[$key1]["jamsostek"] = $db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => "2","candidate_id" => $candidate_id,"pisa" => "peserta"));
			$arrdata[$key1]["bpjs"] = $db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => "1","candidate_id" => $candidate_id,"pisa" => "peserta"));
			$arrdata[$key1]["email"] = $db->fetch_single_data("candidates","email",array("id" => $candidate_id));
			$arrdata[$key1]["reason_of_termination"] = $all_data_update["reason_of_termination"];
			$arrdata[$key1]["is_terminated"] = $all_data_update["is_terminated"];
		}
		
		
		
		//=====================================================================//
		//PR SETELAH CUTI LEBARAN//
		//=====================================================================//
		/*
			All Data Update Project Telkomsel
			Bermasalah karena ada 2 nama Rahmat Fauzi yang pada dasarnya adalah belum expired, 
			tapi di munculkan di expired supaya keliatan history nya
			sehingga di akal2in seperti berikut:
				ada 2 baris All Data Update 
					SELECT * FROM  `all_data_update` WHERE `candidate_id` =13
				lalu datanya diatur sedemikian rupa sehingga sesuai dengan excellnya ????????????
				HAAAAAAAAAAAAAAAAAAAAAaaaaaa.............................
		*/
		/*********$arrdata[12]["pkwt"][0] = $arrdata[12]["pkwt"][4];
		unset($arrdata[12]["pkwt"][1]);
		unset($arrdata[12]["pkwt"][2]);
		unset($arrdata[12]["pkwt"][3]);
		unset($arrdata[12]["pkwt"][4]);
		unset($arrdata[60]["pkwt"][4]);
		$arrtemp = $arrdata[60]["pkwt"][3];
		$arrdata[60]["pkwt"][3] = $arrdata[60]["pkwt"][2];
		$arrdata[60]["pkwt"][2] = $arrtemp;**********/
		
		echo "<pre>";
		// print_r($arrdata);
		print_r($arrdata[12]["pkwt"]);
		print_r($arrdata[60]["pkwt"]);
		echo "</pre>";
		//=====================================================================//
		//END PR SETELAH CUTI LEBARAN//
		//=====================================================================//
					
		$arrheaders["ot"] = "OT";
		$arrheaders["thr"] = "THR";
		$arrheaders["asuransi"] = "Asuransi";
		$arrheaders["remarks2"] = "Remarks";
		$arrheaders["address"] = "Address";
		$arrheaders["phone"] = "Phone No.";
		$arrheaders["bank_account"] = "Bank account";
		$arrheaders["ktp"] = "KTP";
		$arrheaders["jamsostek"] = "Jamsostek";
		$arrheaders["bpjs"] = "BPJS";
		$arrheaders["email"] = "Email";
		$arrheaders["reason_of_termination"] = "Reason of Termination";
		ksort($arrheaders["allowances"]);
		
		$arr_header = array();
		$arr_header2 = array();
		if(!$_isexport){
			array_push($arr_header,"");$arr_header_attr[] = "nowrap valign='top' rowspan='2'";
		}
		array_push($arr_header,"NO");$arr_header_attr[] = "nowrap valign='top' rowspan='2'";
		$urutan_array = -1;
		foreach($arrheaders as $head_id => $head_cap){
			if(is_array($head_cap)){
				$urutan_array++;
				if($urutan_array == 0){//mode pkwt
					foreach($head_cap as $pkwt_key => $pkwt_ke){
						if(is_numeric($pkwt_ke)){
							$pkwt_ke = "PKWT ".($pkwt_ke+1);
						}else{
							$pkwt_ke = strtoupper($pkwt_ke);
						}
						array_push($arr_header,$pkwt_ke);
						$arr_header_attr[] = "nowrap valign='top' colspan='2'";
						array_push($arr_header2,"From");
						array_push($arr_header2,"To");
						if($num_pkwt_child[$pkwt_key]){
							array_push($arr_header,"");
							$arr_header_attr[] = "nowrap valign='top' colspan='".$num_pkwt_child[$pkwt_key]."'";
							for($xx = 1;$xx <= $num_pkwt_child[$pkwt_key];$xx++){
								array_push($arr_header2,$xx);
							}
						}
					}
				}
				if($urutan_array == 1){//mode allowance
					foreach($head_cap as $allowance_id => $allowance){
						array_push($arr_header,$allowance);
						$arr_header_attr[] = "nowrap valign='top' rowspan='2'";
					}
				}
			} else {
				array_push($arr_header,$head_cap);
				$arr_header_attr[] = "nowrap valign='top' rowspan='2'";
			}
		}
	?>
		<script>
			function subwindow_homebases(all_data_update_id){
				$.fancybox.open({ href: "sub_window/win_homebases_list.php?all_data_update_id="+all_data_update_id, height: "80%", type: "iframe" });
			}
			function subwindow_projects(all_data_update_id){
				$.fancybox.open({ href: "sub_window/win_projects_list.php?all_data_update_id="+all_data_update_id, height: "80%", type: "iframe" });
			}
			function subwindow_positions(all_data_update_id){
				$.fancybox.open({ href: "sub_window/win_positions_list.php?all_data_update_id="+all_data_update_id, height: "80%", type: "iframe" });
			}
			function move_to_terminated(all_data_update_id){
				var form_terminating = "<form method='POST'>";
				form_terminating += "		<h3 style='text-align:center; font-weight:bolder'>MOVE TO TERMINATED</h3>";
				form_terminating += "		<table id='editor_content'>";
				form_terminating += "			<tr><td>Reason Of Termination</td><td>:</td><td><input name='reason_of_termination' type='text' size='50'></td></tr>";
				form_terminating += "			<tr><td>Date Of Termination</td><td>:</td><td><input name='date_of_termination' type='date'></td></tr>";
				form_terminating += "		</table><br>";
				form_terminating += "		<input type='hidden' name='all_data_update_id' value='" + all_data_update_id + "'>";
				form_terminating += "		<input name='move_to_terminated' value='Move' type='submit'>";
				form_terminating += "		<input name='cancel' value='Cancel' type='button' onclick='$.fancybox.close();'>";
				form_terminating += "	</form>";
				$.fancybox.open({ content: form_terminating });
			}
		</script>
		<?php if($_isexport){ $_tableattr = "border='1'"; }?>
		<?=$t->start($_tableattr,"data_content");?>
		<?=$t->header($arr_header,$arr_header_attr);?>
		<?=$t->header($arr_header2);?>
		<?php 
			/* echo "<hr><pre>";
			print_r($arrdata);
			echo "</pre>"; */
			$terminated_rows == false;
			foreach($arrdata as $no => $data){
				if($terminated_rows == false && $data["is_terminated"] == 1){
					echo $t->end();
					echo "<br><br><h3 style='color:red;'><b>TERMINATED</b></h3>";
					echo $t->start($_tableattr,"data_content");
					echo $t->header($arr_header,$arr_header_attr);
					echo $t->header($arr_header2);
				}
				$tr_attr = "";
				if($data["is_terminated"] == 0){
					if($data["remarks"] == "Warning") $tr_attr = " bgcolor='yellow'";
					if($data["remarks"] == "Expired") $tr_attr = " bgcolor='red'";
					$terminated_rows = false;
				} else {
					$tr_attr = "style='color:red'";
					$terminated_rows = true;
				}
				
				$arr_row = array();
				$arr_row_attr = array();
				if(!$_isexport){
					if($terminated_rows == false){
						array_push($arr_row,"<a href=\"javascript:move_to_terminated(".$data["id"].");\" style=\"font-size:18px;color:red;\" title=\"Terminate\">X</a>");
					}else{
						array_push($arr_row,"");
					}
					$arr_row_attr[]="valign='top'";
				}
				$number = "<a href='all_data_update_edit.php?id=".$data["id"]."' target='_BLANK'>".($no+1)."</a>";
				array_push($arr_row,$number);$arr_row_attr[]="align='right' valign='top'";
				$urutan_array = -1;
				foreach($arrheaders as $head_id => $head_cap){
					if(is_array($head_cap)){
						$urutan_array++;
						if($urutan_array == 0){//mode pkwt
							foreach($head_cap as $pkwt_key => $pkwt_ke){
								//array push from to
								array_push($arr_row,"<a href=\"job_order_edit.php?id=".$data["pkwt"][$pkwt_key]["id"]."\" target=\"_BLANK\">".format_tanggal($data["pkwt"][$pkwt_key]["join_start"],"dMY")."</a>");$arr_row_attr[]="nowrap valign='top'";
								array_push($arr_row,"<a href=\"job_order_edit.php?id=".$data["pkwt"][$pkwt_key]["id"]."\" target=\"_BLANK\">".format_tanggal($data["pkwt"][$pkwt_key]["join_end"],"dMY")."</a>");$arr_row_attr[]="nowrap valign='top'";
								if($num_pkwt_child[$pkwt_key]){
									for($xx = 1;$xx <= $num_pkwt_child[$pkwt_key];$xx++){
										/* if(count($data["pkwt"][$pkwt_key]["amandemen"]) > 0) $amandemen_extension = "amandemen";
										else $amandemen_extension = "extension"; */
										$amandemen_extension = "extension";
										$joborder_extension_id = $data["pkwt"][$pkwt_key][$amandemen_extension][$xx-1]["id"];
										array_push($arr_row,"<a href=\"job_order_extension_edit.php?id=".$joborder_extension_id."\" target=\"_BLANK\">".format_tanggal($data["pkwt"][$pkwt_key][$amandemen_extension][$xx-1]["join_end"],"dMY")."</a>");
										$arr_row_attr[]="nowrap valign='top'";
									}
								}
							}
						}
						if($urutan_array == 1){//mode allowance
							foreach($head_cap as $allowance_id => $allowance){
								if($data["allowances"][$allowance_id] > 0){
									$_pkwt_for = $db->fetch_single_data("joborder","pkwt_for",array("id" => $data["allowances_jo_id"]));
									if($_pkwt_for == "extension" || $_pkwt_for == "amandemen")
										$editmode = "job_order_extension_edit.php?id=".$data["allowances_jo_id"];
									else
										$editmode = "job_order_edit.php?id=".$data["allowances_jo_id"];
									array_push($arr_row,"<a href='".$editmode."' target='_BLANK'>".format_amount($data["allowances"][$allowance_id])."</a>");
								} else {
									array_push($arr_row,"");
								}
								$arr_row_attr[] = "nowrap align='right' valign='top'";
							}
						}
					} else {
						if($head_id == "basic_salary"
							|| $head_id == "leastday"){
							$value = ($data[$head_id] > 0) ? format_amount($data[$head_id]):"";
							array_push($arr_row,$value);
							$arr_row_attr[]="nowrap align='right' valign='top'";
						}else{
							if($head_id == "original_join_date") $data[$head_id] = format_tanggal($data[$head_id],"dMY");
							array_push($arr_row,$data[$head_id]);
							$arr_row_attr[]="nowrap valign='top'";
						}
					}
				}
				echo $t->row($arr_row,$arr_row_attr,$tr_attr);
			}
		?>
		<?=$t->end();?>
	<?php } ?>
<?php include_once "footer.php";?>