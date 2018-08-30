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
	if($_GET["deleting"]){
		$db->addtable("all_data_update");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<?php if(!$_isexport){ ?>
	<div class="bo_title">All Data Update</div>
	<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
	<div id="bo_filter">
		<div id="bo_filter_container">
			<?=$f->start("filter","GET");?>
				<?=$t->start();?>
				<?php
				
					$code = $f->input("code",$_GET["code"]);
					$name = $f->select("name",$db->fetch_select_data("candidates","id","name",array(),array("name"),"",true),$_GET["name"],"style='height:25px;'");
					$client = $f->select("client",$db->fetch_select_data("clients","id","name",array(),array("name"),"",true),$_GET["client"],"style='height:25px;'");
					$tax_status = $f->select("tax_status",$db->fetch_select_data("statuses","id","name",array(),array("id"),"",true),$_GET["position"],"style='height:25px;'");
					$medical_status = $f->select("medical_status",$db->fetch_select_data("statuses","id","name",array("only_marital" => "1"),array("id"),"",true),$_GET["position"],"style='height:25px;'");
					$position = $f->select("position",$db->fetch_select_data("positions","id","name",array(),array("name"),"",true),$_GET["position"],"style='height:25px;'");
					$user = $f->input("user",$_GET["user"]);
				?>
				<?=$t->row(array("Code",$code));?>
				<?=$t->row(array("Candidate Name",$name));?>
				<?=$t->row(array("Client",$client));?>
				<?=$t->row(array("Tax Status",$tax_status));?>
				<?=$t->row(array("Medical Status",$medical_status));?>
				<?=$t->row(array("Position",$position));?>
				<?=$t->row(array("User",$user));?>
				<?=$t->end();?>
				<?=$f->input("page","1","type='hidden'");?>
				<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
				<?=$f->input("do_filter","Load","type='submit' style='width:180px;'");?>
				<?=$f->input("export","Export to Excel","type='submit' style='width:180px;'");?>
				<?=$f->input("reset","Reset","type='button' style='width:180px;' onclick=\"window.location='?';\"");?>
			<?=$f->end();?>
		</div>
	</div>
<?php } else { ?>
	<h1><b>All Data Update</b></h1>
<?php } ?>

<?php
	
	$whereclause = "";
    if(@$_GET["code"]!="") $whereclause .= "(code LIKE '%".$_GET["code"]."%') AND ";
	if(@$_GET["name"]!="") $whereclause .= "(candidate_id = '".$_GET["name"]."') AND ";
	if(@$_GET["client"]!="") $whereclause .= "(joborder_id IN (SELECT id FROM joborder WHERE client_id = '".$_GET["client"]."')) AND ";
	if(@$_GET["tax_status"]!="") $whereclause .= "(tax_status_id = '".$_GET["tax_status"]."') AND ";
	if(@$_GET["medical_status"]!="") $whereclause .= "(medical_status_id = '".$_GET["medical_status"]."') AND ";
	if(@$_GET["position"]!="") $whereclause .= "(position_id = '".$_GET["position"]."') AND ";
	if(@$_GET["user"]!="") $whereclause .= "(user LIKE '%".$_GET["user"]."%') AND ";
    
	if(!$_isexport){ 
		$db->addtable("all_data_update");
		if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
		$maxrow = count($db->fetch_data(true));
		$start = getStartRow(@$_GET["page"],$_rowperpage);
		$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	}
	
	$db->addtable("all_data_update");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(!$_isexport){ $db->limit($start.",".$_rowperpage); }
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$all_data_update = $db->fetch_data(true);
	$benefits_header = "BPJS Kesehatan";
	$benefits_header = "BPJS Ketenagakerjaan";
	$arr_header = array("No",
						"<div onclick=\"sorting('code');\">Code</div>",
						"<div onclick=\"sorting('candidate_id');\">Name</div>",
						"<div onclick=\"sorting('birthdate');\">Date of Birth</div>",
						"<div onclick=\"sorting('sex');\">Sex</div>",
						"<div onclick=\"sorting('tax_status_id');\">Tax Status</div>",
						"<div onclick=\"sorting('medical_status_id');\">Medical Status</div>",
						"Homebase",
						"<div onclick=\"sorting('position');\">Position</div>",
						"<div onclick=\"sorting('user');\">User</div>",
						"Original join<br>date");
						
	array_push($arr_header,"PKWT I");
	array_push($arr_header,"");
	array_push($arr_header,"");
	array_push($arr_header,"");
	array_push($arr_header,"");
	array_push($arr_header,"PKWT II");
	
	array_push($arr_header,"Least Day",
						"<div onclick=\"sorting('remarks');\">Remarks</div>",
						"Basic Salary");
						
	
	$db->addtable("allowances");$db->order("id");
	foreach($db->fetch_data(true) as $allowances){
		array_push($arr_header,$allowances["name"]);
	}
						
	$db->addtable("benefits");$db->order("id");
	foreach($db->fetch_data(true) as $benefits){
		array_push($arr_header,$benefits["name"]);
	}
	
	array_push($arr_header,"OT",
						"Benefit",
						"Address",
						"Phone No.",
						"Bank Account",
						"KTP",
						"NPWP",
						"Email",
						"Reason of Termination"
						);
	
	
	foreach($arr_header as $header_cap){
		if(stripos(" ".$header_cap,"pkwt") > 0){
			$arr_header_attr[] = "nowrap valign='top' colspan='2'";
		} else if($header_cap == "") {
			$arr_header_attr[] = "nowrap valign='top'";
		} else {
			$arr_header_attr[] = "nowrap valign='top' rowspan='2'";
		}
	}
	
	$arr_header2[] = "From";
	$arr_header2[] = "To";
	$arr_header2[] = "1";
	$arr_header2[] = "2";
	$arr_header2[] = "3";
	$arr_header2[] = "4";
	$arr_header2[] = "From";
	$arr_header2[] = "To";
?>
	<?php 
		if(!$_isexport){ 
			echo $f->input("back","Back","type='button' onclick=\"window.location='all_data_update_list.php';\"");
			echo $paging;
		}
	?>
	<script>
		function subwindow_homebases(all_data_update_id){
			$.fancybox.open({ href: "sub_window/win_homebases_list.php?all_data_update_id="+all_data_update_id, height: "80%", type: "iframe" });
		}
	</script>
	<?php if($_isexport){ $_tableattr = "border='1'"; }?>
	<?=$t->start($_tableattr,"data_content");?>
	<?=$t->header($arr_header,$arr_header_attr);?>
	<?=$t->header($arr_header2);?>
	<?php 
		foreach($all_data_update as $no => $data){
			$status_id = $db->fetch_single_data("candidates","status_id",array("id"=>$data["candidate_id"]));
			//find least_day
			$last_joborder_id = $db->fetch_single_data("joborder","id",array("joborder_id" => $data["joborder_id"]),array("created_at DESC")) * 1;
			if($last_joborder_id == 0) $last_joborder_id = $data["joborder_id"];
			$end_date = $db->fetch_single_data("joborder","join_end",array("id"=>$last_joborder_id));
			$least_day = 0;
			if($end_date > date("Y-m-d")) $least_day = day_diff($end_date,date("Y-m-d"));
			$pkwt1_from = format_tanggal($db->fetch_single_data("joborder","join_start",array("id" => $data["joborder_id"])),"d M Y");
			$pkwt1_to = format_tanggal($db->fetch_single_data("joborder","join_end",array("id" => $data["joborder_id"])),"d M Y");
			$db->addtable("joborder");
			$db->addfield("join_end");
			$db->where("joborder_id",$data["joborder_id"]);
			$db->where("is_amandemen",1);
			$db->limit(4);
			$db->order("created_at");
			$_amandemens = $db->fetch_data(true);
			$amandemen = array();
			foreach($_amandemens as $key => $_amandemen){ $amandemen[$key] = format_tanggal($_amandemen[0],"d M Y"); }
			
			$joborder_id_2 = "";$pkwt2_from = "";$pkwt2_to = "";
			$arr_joborder_id = $db->fetch_select_data("joborder","id","client_id",array("joborder_id" => $data["joborder_id"],"is_amandemen"=>"0"),array("created_at"));
			foreach($arr_joborder_id as $__joborder_id_xx => $client_id){$joborder_id_2 = $__joborder_id_xx; break;}
			if($joborder_id_2){
				$pkwt2_from = format_tanggal($db->fetch_single_data("joborder","join_start",array("id" => $joborder_id_2)),"d M Y");
				$pkwt2_to = format_tanggal($db->fetch_single_data("joborder","join_end",array("id" => $joborder_id_2)),"d M Y");
			}
			
			$arrhomebases = pipetoarray($data["homebase_ids"]); $homebases = "";
			foreach($arrhomebases as $homebase_id){
				if($homebase_id > 0) $homebases .= $db->fetch_single_data("homebases","name",array("id"=>$homebase_id))."<br>";
			}
			if(!$_isexport) $homebases .= "<img src='icons/search_window.png' onclick='subwindow_homebases(\"".$data["id"]."\");'>";
			
			$arr_row = array(
				$no+$start+1,
				$data["code"],
				$db->fetch_single_data("candidates","name",array("id"=>$data["candidate_id"])),
				format_tanggal($db->fetch_single_data("candidates","birthdate",array("id"=>$data["candidate_id"])),"d M Y"),
				$db->fetch_single_data("candidates","sex",array("id"=>$data["candidate_id"])),
				$db->fetch_single_data("statuses","name",array("id"=>$data["tax_status_id"])),
				$db->fetch_single_data("statuses","name",array("id"=>$data["medical_status_id"])),
				$homebases,
				$db->fetch_single_data("positions","name",array("id"=>$data["position_id"])),
				$data["user"],
				format_tanggal($data["original_join_date"],"d M Y"),
				$pkwt1_from,
				$pkwt1_to,
				$amandemen[0],
				$amandemen[1],
				$amandemen[2],
				$amandemen[3],
				$pkwt2_from,
				$pkwt2_to,
				$least_day,
				$data["remarks"],
				format_amount($db->fetch_single_data("joborder","basic_salary",array("id"=>$data["joborder_id"])))
			);
			
			$db->addtable("allowances");$db->order("id");
			$_allowances = $db->fetch_data(true);
			foreach($_allowances as $allowances){
				array_push($arr_row,format_amount($db->fetch_single_data("joborder_allowances","price",array("joborder_id" => $data["joborder_id"],"allowance_id" => $allowances["id"]))));
			}
			
			$db->addtable("benefits");$db->order("id");
			$_benefits = $db->fetch_data(true);
			foreach($_benefits as $benefits){
				if($benefits["id"] < 3){
					array_push($arr_row,$db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => $benefits["id"],"candidate_id" => $data["candidate_id"],"pisa" => "peserta")));
				} else {
					array_push($arr_row,format_amount($db->fetch_single_data("joborder_benefits","price",array("joborder_id" => $data["joborder_id"],"benefits_id" => $benefits["id"]))));					
				}
			}
			
			array_push($arr_row,
				($db->fetch_single_data("joborder","overtime",array("id"=>$data["joborder_id"])) == 1)?"Follow DEPNAKER":"No",
				$db->fetch_single_data("joborder","other_benefits",array("id"=>$data["joborder_id"])),
				$db->fetch_single_data("candidates","address",array("id"=>$data["candidate_id"])),
				$db->fetch_single_data("candidates","phone",array("id"=>$data["candidate_id"])),
				$db->fetch_single_data("candidates","bank_name",array("id"=>$data["candidate_id"])).": ".$db->fetch_single_data("candidates","bank_account",array("id"=>$data["candidate_id"])),
				$db->fetch_single_data("candidates","ktp",array("id"=>$data["candidate_id"])),
				$db->fetch_single_data("candidates","npwp",array("id"=>$data["candidate_id"])),
				$db->fetch_single_data("candidates","email",array("id"=>$data["candidate_id"])),
				""
			);
			echo $t->row($arr_row,
				array("align='right' valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top' nowrap",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"valign='top' nowrap",
						"align='right' valign='top'",
						"valign='top'",
						"align='right' valign='top'",
						"align='right' valign='top'",
						"align='right' valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'"
					)
			);
		} 
	?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>