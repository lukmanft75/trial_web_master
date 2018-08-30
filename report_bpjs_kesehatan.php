<?php
	if($_GET["export"]){
		$_exportname = "bpjs_kesehatan.xls";
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
	<div class="bo_title">BPJS KESEHATAN REPORT</div>
	<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
	<div id="bo_filter">
		<div id="bo_filter_container">
			<?=$f->start("filter","GET");?>
				<?=$t->start();?>
				<?php
				
					$code = $f->input("code",$_GET["code"]);
					$name = $f->select("name",$db->fetch_select_data("candidates","id","name",array(),array("name"),"",true),$_GET["name"],"style='height:25px;'");
				?>
				<?=$t->row(array("Code",$code));?>
				<?=$t->row(array("Candidate Name",$name));?>
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
	<h1><b>BPJS KESEHATAN REPORT</b></h1>
<?php } ?>

<?php
	
	$whereclause = "id IN (SELECT distinct(candidate_id) FROM joborder WHERE join_end > DATE(NOW())) AND ";
    if(@$_GET["code"]!="") $whereclause .= "(code LIKE '%".$_GET["code"]."%') AND ";
    if(@$_GET["name"]!="") $whereclause .= "(id = '".$_GET["name"]."') AND ";
    
	if(!$_isexport){ 
		$db->addtable("candidates");
		if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
		$maxrow = count($db->fetch_data(true));
		$start = getStartRow(@$_GET["page"],$_rowperpage);
		$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	}
	
	$db->addtable("candidates");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(!$_isexport){ $db->limit($start.",".$_rowperpage); }
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$candidates = $db->fetch_data(true);
	$arr_header = array("No",
						"<div onclick=\"sorting('code');\">Code</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('birthdate');\">Date of Birth</div>",
						"PISA",
						"<div onclick=\"sorting('sex');\">Sex</div>",
						"Status",
						"PKWT I<br> From",
						"Basic Salary",
						"NIK",
						"No BPJS",
						"Email");
						
	if(!$_isexport){
		//echo $f->input("back","Back","type='button' onclick=\"window.location='all_data_update_list.php';\"");
		echo $paging;
	}
	if($_isexport){ $_tableattr = "border='1'"; }
?>
	<?=$t->start($_tableattr,"data_content");?>
	<?=$t->header($arr_header,$arr_header_attr);?>
	<?php 
		foreach($candidates as $no => $data){
			$arr_row = array(
				$no+$start+1,
				"<a href='files_bpjs/".$data["id"].".pdf' target='_BLANK'>".$data["code"]."</a>",
				$data["name"],
				format_tanggal($data["birthdate"],"d M Y"),
				ucfirst("peserta"),
				$data["sex"],
				$db->fetch_single_data("statuses","name",array("id" => $data["status_id"])),
				format_tanggal($db->fetch_single_data("joborder","join_start",array("candidate_id" => $data["id"],"join_start" => "0000-00-00:>"),array("join_start")),"d M Y"),
				format_amount($db->fetch_single_data("joborder","basic_salary",array("candidate_id" => $data["id"],"join_start" => "0000-00-00:>"),array("join_start"))),
				$data["ktp"],
				$db->fetch_single_data("bpjs","bpjs_id",array("code" => $data["code"],"pisa" => "peserta","bpjs_type" => "1")),
				$data["email"]				
			);
			echo $t->row($arr_row,
				array("align='right' valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top'",
						"valign='top' align='right'",
						"valign='top'",
						"valign='top'",
						"valign='top'"
					)
			);
			$db->addtable("bpjs"); $db->where("code",$data["code"]);  $db->where("bpjs_type","1"); $db->where("pisa","peserta","s","!="); $db->order("id");
			$bpjs_s = $db->fetch_data(true);
			foreach($bpjs_s as $bpjs){
				$arr_row = array(
					"",
					"",
					$bpjs["name"],
					format_tanggal($bpjs["birthdate"],"d M Y"),
					ucfirst($bpjs["pisa"]),
					$bpjs["sex"],
					$db->fetch_single_data("statuses","name",array("id" => $bpjs["bpjs"])),
					"",
					"",
					$bpjs["ktp"],
					$bpjs["bpjs_id"],
					$bpjs["email"]				
				);
				echo $t->row($arr_row,array("valign='top'"));
			}
		} 
	?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>