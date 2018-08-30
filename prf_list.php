<?php
	if($_GET["export"]){
		$_exportname = "prfList.xls";
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
		if($db->fetch_single_data("prf","approve_by",array("id" => $_GET["deleting"])) == ""){
			$db->addtable("prf");
			$db->where("id",$_GET["deleting"]);
			if($__group_id > 4){
				$db->where("created_by",$__username);
			}
			$db->delete_();
			?> <script> window.location="?";</script> <?php
		}else{
			?> <script> alert('This PRF has Approved, You`re not allow to delete this PRF'); </script> <?php
		}
	}
	
	$arr_range_date = ["" => "",
						"maker_at" => "Maker Date",
						"checker_at" => "CheckerDate",
						"signer_at" => "Signer Date",
						"finance_at" => "Finance Date",
						"accounting_at" => "Authorize Date",
						"approve_at" => "Approve Date",
						"jurnal_at" => "Journaling Date",
						"paid_at" => "Paid Date"];
?>
<?php if(!$_isexport){ ?>
	<div class="bo_title">PRF</div>
	<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
	<div id="bo_filter">
		<div id="bo_filter_container">
			<?=$f->start("filter","GET");?>
				<?=$t->start();?>
				<?php
					$sel_project = $f->select("project_id",$db->fetch_select_data("indottech_projects","id","concat('[',initial,'] ',name)",[],[],"",true),$_GET["project_id"],"style='height:20px;'");
					$sel_scope = $f->select("scope_id",$db->fetch_select_data("indottech_scopes","id","concat('[',initial,'] ',name)",[],[],"",true),$_GET["scope_id"],"style='height:20px;'");
					$sel_region = $f->select("region_id",$db->fetch_select_data("indottech_regions","id","concat('[',initial,'] ',name)",[],[],"",true),$_GET["region_id"],"style='height:20px;'");
					$sel_cost_center = $f->select("cost_center_code",$db->fetch_select_data("cost_centers","code","concat('[',code,'] ',name)",["departement" => "Indottech"],[],"",true),$_GET["cost_center_code"],"style='height:20px;'");
					$sel_range_date = $f->select("range_date",$arr_range_date,$_GET["range_date"],"style='height:20px;'");
					$range_date_1 = $f->input("range_date_1",@$_GET["range_date_1"],"type='date' style='width:130px;'");
					$range_date_2 = $f->input("range_date_2",@$_GET["range_date_2"],"type='date' style='width:130px;");
					
					$created_by = $f->input("created_by",@$_GET["created_by"]);
					$checker_by = $f->input("checker_by",@$_GET["checker_by"]);
					$signer_by = $f->input("signer_by",@$_GET["signer_by"]);
					$approve_by = $f->input("approve_by",@$_GET["approve_by"]);
					$paid = $f->select("paid",[""=>"","1"=>"paid","2"=>"unpaid"],@$_GET["paid"],"style='height:20px;'");
				?>
				<?=$t->row(array("Project",$sel_project));?>
				<?=$t->row(array("Scope",$sel_scope));?>
				<?=$t->row(array("Region",$sel_region));?>
				<?=$t->row(array("Cost Center",$sel_cost_center));?>
				<?=$t->row(array($sel_range_date,$range_date_1." - ".$range_date_2));?>
				<?=$t->row(array("Created By",$created_by));?>
				<?=$t->row(array("Checker By",$checker_by));?>
				<?=$t->row(array("Signer By",$signer_by));?>
				<?=$t->row(array("Approve By",$approve_by));?>
				<?=$t->row(array("Is Paid",$paid));?>
				<?=$t->end();?>
				<?=$f->input("page","1","type='hidden'");?>
				<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
				<?=$f->input("do_filter","Load","type='submit' style='width:150px;'");?>
				<?=$f->input("export","Export to Excel","type='submit' style='width:150px;'");?>
				<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\" style='width:150px;'");?>
			<?=$f->end();?>
		</div>
	</div>
<?php } else { ?>
	<h2><b>PRF</b></h2>
	<?php 
		echo "<b>";
		if($_GET["project_id"]!=""){ echo $db->fetch_single_data("indottech_projects","name",["id" => $_GET["project_id"]]); } else { echo "All Projects";}
		if($_GET["scope_id"]!=""){ echo " - ".$db->fetch_single_data("indottech_scopes","name",["id" => $_GET["scope_id"]]); } else { echo " - All Scopes";}
		if($_GET["region_id"]!=""){ echo " - ".$db->fetch_single_data("indottech_regions","name",["id" => $_GET["region_id"]]); } else { echo " - All Regions";}
		if($_GET["cost_center_code"]!=""){ echo "<br>Cost Center : ".$db->fetch_single_data("cost_centers","name",["code" => $_GET["cost_center_code"]]); } else { echo "<br>All Cost Centers";}
		echo "</b><br>";
		if(@$_GET["range_date"]!="" && (@$_GET["range_date_1"]!="" || @$_GET["range_date_2"]!="")){
			echo $arr_range_date[$_GET["range_date"]]." : ";
			if(@$_GET["range_date_1"]!="") echo format_tanggal($_GET["range_date_1"]);
			if(@$_GET["range_date_2"]!="") echo " s/d ".format_tanggal($_GET["range_date_2"]);
		}
	?>
<?php } ?>

<?php	
	$whereclause = "is_rejected = 0 AND ";
	if($__group_id > 4) $whereclause .= "(created_by = '".$__username."' OR checker_by = '".$__username."' OR signer_by = '".$__username."') AND ";
	if(@$_GET["project_id"]!="") $whereclause .= "(id IN (SELECT prf_id FROM indottech_prfs WHERE project_id = '".$_GET["project_id"]."')) AND ";
	if(@$_GET["scope_id"]!="") $whereclause .= "(id IN (SELECT prf_id FROM indottech_prfs WHERE scope_id = '".$_GET["scope_id"]."')) AND ";
	if(@$_GET["region_id"]!="") $whereclause .= "(id IN (SELECT prf_id FROM indottech_prfs WHERE region_id = '".$_GET["region_id"]."')) AND ";
	if(@$_GET["cost_center_code"]!="") $whereclause .= "cost_center_code = '".$_GET["cost_center_code"]."' AND ";
	if(@$_GET["range_date"]!=""){
		if(@$_GET["range_date_1"]!=""){
			$whereclause .= "(".$_GET["range_date"]." >= '".$_GET["range_date_1"]."') AND ";
		}
		if(@$_GET["range_date_2"]!=""){
			$whereclause .= "(".$_GET["range_date"]." <= '".$_GET["range_date_2"]."') AND ";
		}
	}
	if(@$_GET["created_by"]!="")$whereclause .= "(created_by LIKE '%".$_GET["created_by"]."%') AND ";
	if(@$_GET["paid"]=="1") 	$whereclause .= "(paid_by <> '') AND ";
	if(@$_GET["paid"]=="2") 	$whereclause .= "(paid_by = '') AND ";
	if(@$_GET["checker_by"]!="")$whereclause .= "(checker_by LIKE '".$_GET["checker_by"]."') AND ";
	if(@$_GET["signer_by"]!="") $whereclause .= "(signer_by LIKE '".$_GET["signer_by"]."') AND ";
	if(@$_GET["approve_by"]!="") $whereclause .= "(approve_by LIKE '".$_GET["approve_by"]."') AND ";
	$db->addtable("prf");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	if($whereclause != "") $TOTAL = $db->fetch_all_data("prf",["concat(sum(nominal)) as total"],substr($whereclause,0,-4))[0][0];
	else $TOTAL = $db->fetch_all_data("prf",["concat(sum(nominal)) as total"])[0][0];
	
	$db->addtable("prf");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(!$_isexport){
		$db->limit($start.",".$_rowperpage);
	}
	if(@$_GET["sort"] == "") $_GET["sort"] = "maker_at DESC";
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$prfs = $db->fetch_data(true);
?>
	<script>
		function unpaid(){
			document.getElementById("paid").value=2;
			document.getElementById('checker_by').value='';
			document.getElementById('signer_by').value='';
			document.getElementById('approve_by').value='';
			document.getElementById("do_filter").click();
		}
		function checker_by_me(){
			document.getElementById("paid").value='';
			document.getElementById('checker_by').value='<?=$__username;?>';
			document.getElementById('signer_by').value='';
			document.getElementById('approve_by').value='';
			document.getElementById('do_filter').click();
		}
		function signer_by_me(){
			document.getElementById("paid").value='';
			document.getElementById('checker_by').value='';
			document.getElementById('signer_by').value='<?=$__username;?>';
			document.getElementById('approve_by').value='';
			document.getElementById('do_filter').click();
		}
		function approve_by_me(){
			document.getElementById("paid").value='';
			document.getElementById('checker_by').value='';
			document.getElementById('signer_by').value='';
			document.getElementById('approve_by').value='<?=$__username;?>';
			document.getElementById('do_filter').click();
		}
	</script>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='prf_add.php';\"");?>
	<?=$f->input("unpaid","Show Unpaid","type='button' onclick=\"unpaid();\"");?>
	<?=$f->input("mychecker","Checker By Me","type='button' onclick=\"checker_by_me();\"");?>
	<?=$f->input("mysigner","Signer By Me","type='button' onclick=\"signer_by_me();\"");?>
	<?=$f->input("myapprove","Approve By Me","type='button' onclick=\"approve_by_me();\"");?>
	<?php if(!$_isexport){ ?>
		<?=$paging;?>
	<?php } ?>
	<?php if($_isexport){ $_tableattr = "border='1'";}?>
	<?=$t->start($_tableattr,"data_content");?>
	<?php
		$arrheader = array("No","");
		if($__group_id <= 4){
			array_push($arrheader,"<div onclick=\"sorting('code');\">Code</div>");
		}
		array_push($arrheader,"<div onclick=\"sorting('code_number');\">Code Number</div>");
        array_push($arrheader,"<div onclick=\"sorting('maker_at');\">Maker Date</div>");
        array_push($arrheader,"<div onclick=\"sorting('created_by');\">Created By</div>");
        array_push($arrheader,"<div onclick=\"sorting('nominal');\">Nominal</div>");
        array_push($arrheader,"<div onclick=\"sorting('purpose');\">Purpose</div>");
        array_push($arrheader,"<div onclick=\"sorting('checker_at');\">Checked</div>");
        array_push($arrheader,"<div onclick=\"sorting('signer_at');\">Signed</div>");
        array_push($arrheader,"<div onclick=\"sorting('approve_at');\">Approved</div>");
		if($__group_id <= 4){
			array_push($arrheader,"<div onclick=\"sorting('jurnal_by');\">Journal</div>");
		}
        array_push($arrheader,"<div onclick=\"sorting('paid_at');\">Paid</div>");
	?>
	<?=$t->header($arrheader);?>
	<?php
		$arrAttb = array("align='right' valign='top'","valign='top' nowrap","valign='top' nowrap","valign='top'","valign='top'","valign='top' align='right'","valign='top' style='word-wrap: break-word'","valign='top'","valign='top'");
		if($__group_id <= 4){
			$arrAttb = array("align='right' valign='top'","valign='top' nowrap","valign='top' nowrap","valign='top'","valign='top'","valign='top'","valign='top' align='right'","valign='top' style='word-wrap: break-word'","valign='top'","valign='top'");
		}
		$total = 0;
		$capTotalColspan = ($__group_id <= 4)?"6":"5";
	?>
	<?php foreach($prfs as $no => $prf){ ?>
		<?php
			$actions = "<a href=\"prf_view.php?id=".$prf["id"]."\">View</a> | <a href=\"prf_edit.php?id=".$prf["id"]."\">Edit</a> | <a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$prf["id"]."';}\">Delete</a>";
			if($prf["attachment"] != ""){
				$actions .= "<br><a target='_BLANK' href=\"../indottech/prf_attachments/".$prf["attachment"]."\">Attachment</a>";
			}
			if($prf["proof_of_payment"] != ""){
				$actions .= "|<a target='_BLANK' href=\"../indottech/prf_attachments/".$prf["proof_of_payment"]."\">Proof_of_Payment</a>";
			}
			if($prf["settlement"] != ""){
				$actions .= "|<a target='_BLANK' href=\"../indottech/prf_attachments/".$prf["settlement"]."\">Settlement</a>";
			}
            $checked = ($prf["checker_at"] != "0000-00-00" && $prf["checker_at"] != "") ? "Yes":"No";
            $signed = ($prf["signer_at"] != "0000-00-00" && $prf["signer_at"] != "") ? "Yes":"No";
            $approved = ($prf["approve_at"] != "0000-00-00" && $prf["approve_at"] != "") ? "Yes":"No";
            $journaled = ($prf["jurnal_by"] != "" && $prf["coa"] != "") ? "Yes":"No";
            $paid = ($prf["paid_by"] != "") ? format_tanggal($prf["paid_at"]):"";
            //$paid = format_tanggal($prf["paid_at"]);
			$total += $prf["nominal"];
			$arrRow = array();
			$arrRow = array($no+$start+1,$actions);
			if($__group_id <= 4){
				array_push($arrRow,"<a href=\"prf_view.php?id=".$prf["id"]."\">".$prf["code"]."</a>");
			}
			array_push($arrRow,"<a href=\"prf_view.php?id=".$prf["id"]."\">".$prf["code_number"]."</a>");
			array_push($arrRow,format_tanggal($prf["maker_at"],"dMY"));
			array_push($arrRow,$prf["created_by"]);
			array_push($arrRow,format_amount($prf["nominal"]));
			array_push($arrRow,$prf["purpose"]);
			array_push($arrRow,$checked);
			array_push($arrRow,$signed);
			array_push($arrRow,$approved);
			if($__group_id <= 4){
				$jurnal_id = $db->fetch_single_data("jurnals","id",["description" => "%{prf_id%".$prf["id"]."}%:LIKE"]);
				if($jurnal_id > 0) $journaled = "<a href='jurnals_edit.php?id=".$jurnal_id."' target='_BLANK'>".$journaled."</a>";
				array_push($arrRow,$journaled);
			}
			array_push($arrRow,$paid);
		?>
		<?=$t->row($arrRow,$arrAttb);?>
	<?php } ?>
	<?php if($maxrow > $_rowperpage){ ?>
		<?=$t->row(["<b>Total per Page</b>","<b>".format_amount($total)."</b>",""],["colspan='".$capTotalColspan."'","align='right'","colspan='5'"]);?>
	<?php } ?>
	<?=$t->row(["<b>Grand Total</b>","<b>".format_amount($TOTAL)."</b>",""],["colspan='".$capTotalColspan."'","align='right'","colspan='5'"]);?>
	<?=$t->end();?>
	<?php if(!$_isexport){ ?>
		<?=$paging;?>
	<?php } ?>
<?php include_once "footer.php";?>