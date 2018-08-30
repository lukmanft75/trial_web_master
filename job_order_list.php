<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("joborder");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		$db->addtable("joborder_allowances");
		$db->where("joborder_id",$_GET["deleting"]);
		$db->delete_();
		$db->addtable("joborder_benefits");
		$db->where("joborder_id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Job Order</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$sel_client = $f->select("sel_client",$db->fetch_select_data("clients","id","name",array(),array("name"),"",true),$_GET["sel_client"],"style='height:25px;'");
                $sel_position = $f->select("sel_position",$db->fetch_select_data("positions","id","name",array(),array("name"),"",true),$_GET["sel_position"],"style='height:25px;'");
                $txt_report_to = $f->input("txt_report_to",@$_GET["txt_report_to"]);
                $sel_candidate = $f->select("sel_candidate",$db->fetch_select_data("candidates","id","name",array(),array("name"),"",true),$_GET["sel_candidate"],"style='height:25px;'");
                $cal_join_start = $f->input("cal_join_start",@$_GET["cal_join_start"],"type='date'");
                $cal_join_end = $f->input("cal_join_end",@$_GET["cal_join_end"],"type='date'");
                $sel_status_category = $f->select("sel_status_category",$db->fetch_select_data("status_categories","id","name",array(),array("name"),"",true),$_GET["sel_status_category"],"style='height:25px;'");
                $txt_reason_of_hiring = $f->textarea("txt_reason_of_hiring",@$_GET["txt_reason_of_hiring"]);
                $w_hours_start = $f->input("w_hours_start",@$_GET["w_hours_start"],"style='width:25px'");
                $w_hours_end = $f->input("w_hours_end",@$_GET["w_hours_end"],"style='width:25px'");
                $w_hours = $w_hours_start." - ".$w_hours_end; 
                $txt_remarks = $f->textarea("txt_remarks",@$_GET["txt_remarks"]);
                $txt_basic_salary = $f->input("txt_basic_salary",@$_GET["txt_basic_salary"]);
                $sel_overtime = $f->select("sel_overtime",array(""=>"","1" => "Follow DEPNAKER", "2" => "No OT"),$_GET["sel_overtime"],"style='height:25px'");
                $txt_fixed_allowance = $f->input("txt_fixed_allowance",@$_GET["txt_fixed_allowance"]);
				$sel_tax_paid_company = $f->select("sel_tax_paid_company",array(""=>"","1" => "Yes", "2" => "No"),$_GET["sel_tax_paid_company"],"style='height:25px'");
				$sel_medical = $f->select("sel_medical",array(""=>"", "1" => "Covered Insurance", "2" => "No"),$_GET["sel_medical"],"style='height:25px'");
				$sel_lebaran = $f->select("sel_lebaran",array(""=>"", "1" => "Base on government rules"),$_GET["sel_lebaran"],"style='height:25px'");
                $txt_other_benefits = $f->textarea("txt_other_benefits",@$_GET["txt_other_benefits"]);
                $cal_approved_at = $f->input("cal_approved_at",@$_GET["cal_approved_at"],"type='date'");
                $txt_approved_position = $f->input("txt_approved_position",@$_GET["txt_approved_position"]);
                $txt_approved_by = $f->input("txt_approved_by",@$_GET["txt_approved_by"]);
                $cal_checked_at = $f->input("cal_checked_at",@$_GET["cal_checked_at"],"type='date'");
                $txt_checked_position = $f->input("txt_checked_position",@$_GET["txt_checked_position"]);
                $txt_checked_by = $f->input("txt_checked_by",@$_GET["txt_checked_by"]);
			?>
			<?=$t->row(array("Clients",$sel_client));?>
            <?=$t->row(array("Position",$sel_position));?>
            <?=$t->row(array("Report To",$txt_report_to));?>
            <?=$t->row(array("Name of Candidate",$sel_candidate));?>
            <?=$t->row(array("Start Join Date",$cal_join_start));?>
            <?=$t->row(array("End Join Date",$cal_join_end));?>
            <?=$t->row(array("Category",$sel_status_category));?>
            <?=$t->row(array("Reason For Hiring",$txt_reason_of_hiring));?>
            <?=$t->row(array("Working Hours",$w_hours));?>
            <?=$t->row(array("Remarks",$txt_remarks));?>
            <?=$t->row(array("Basic Salary",$txt_basic_salary));?>
            <?=$t->row(array("Overtime",$sel_overtime));?>
            <?=$t->row(array("Fixed Allowance",$txt_fixed_allowance));?>
            <?=$t->row(array("Tax Paid By Company",$sel_tax_paid_company));?>
            <?=$t->row(array("Medical",$sel_medical));?>
            <?=$t->row(array("Lebaran Bonus",$sel_lebaran));?>
            <?=$t->row(array("Other Benefits",$txt_other_benefits));?>
            <?=$t->row(array("Approved At",$cal_approved_at));?>
            <?=$t->row(array("Approved Position",$txt_approved_position));?>
            <?=$t->row(array("Approved By",$txt_approved_by));?>
            <?=$t->row(array("Checked At",$cal_checked_at));?>
            <?=$t->row(array("Checked Position",$txt_checked_position));?>
            <?=$t->row(array("Checked By",$txt_checked_by));?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
		<?=$f->end();?>
	</div>
</div>

<?php
	$whereclause = "joborder_id = 0 AND (pkwt_for REGEXP '[0-9]+' OR pkwt_for = 'break') AND ";
    if(@$_GET["sel_client"]!="") $whereclause .= "(client_id ='".$_GET["sel_client"]."') AND ";
    if(@$_GET["sel_position"]!="") $whereclause .= "(position_id ='".$_GET["sel_position"]."') AND ";
	if(@$_GET["txt_report_to"]!="") $whereclause .= "(report_to LIKE '%".$_GET["txt_report_to"]."%') AND ";
    if(@$_GET["sel_candidate"]!="") $whereclause .= "(candidate_id ='".$_GET["sel_candidate"]."') AND ";
    if(@$_GET["cal_join_start"]!="") $whereclause .= "(join_start ='".$_GET["cal_join_start"]."') AND ";
    if(@$_GET["cal_join_end"]!="") $whereclause .= "(join_end ='".$_GET["cal_join_end"]."') AND ";
    if(@$_GET["sel_status_category"]!="") $whereclause .= "(status_category_id ='".$_GET["sel_status_category"]."') AND ";
    if(@$_GET["txt_reason_of_hiring"]!="") $whereclause .= "(reason_of_hiring LIKE '%".$_GET["txt_reason_of_hiring"]."%') AND ";
    if(@$_GET["w_hours_start"]!="") $whereclause .= "(w_hours_start ='".$_GET["w_hours_start"]."') AND ";
    if(@$_GET["w_hours_end"]!="") $whereclause .= "(w_hours_end ='".$_GET["w_hours_end"]."') AND ";
    if(@$_GET["txt_remarks"]!="") $whereclause .= "(remarks LIKE '%".$_GET["txt_remarks"]."%') AND ";
    if(@$_GET["txt_basic_salary"]!="") $whereclause .= "(basic_salary LIKE '%".$_GET["txt_basic_salary"]."%') AND ";
    if(@$_GET["sel_overtime"]!="") $whereclause .= "(overtime ='".$_GET["sel_overtime"]."') AND ";
    if(@$_GET["txt_fixed_allowance"]!="") $whereclause .= "(fixed_allowance LIKE '%".$_GET["txt_fixed_allowance"]."%') AND ";
    if(@$_GET["sel_tax_paid_company"]!="") $whereclause .= "(tax_paid_company ='".$_GET["sel_tax_paid_company"]."') AND ";
    if(@$_GET["sel_medical"]!="") $whereclause .= "(medical ='".$_GET["sel_medical"]."') AND ";
    if(@$_GET["sel_lebaran"]!="") $whereclause .= "(lebaran ='".$_GET["sel_lebaran"]."') AND ";
    if(@$_GET["txt_other_benefits"]!="") $whereclause .= "(other_benefits LIKE '%".$_GET["txt_other_benefits"]."%') AND ";
    if(@$_GET["cal_approved_at"]!="") $whereclause .= "(approved_at ='".$_GET["cal_approved_at"]."') AND ";
    if(@$_GET["txt_approved_position"]!="") $whereclause .= "(approved_position LIKE '%".$_GET["txt_approved_position"]."%') AND ";
    if(@$_GET["txt_approved_by"]!="") $whereclause .= "(approved_by LIKE '%".$_GET["txt_approved_by"]."%') AND ";
    if(@$_GET["cal_checked_at"]!="") $whereclause .= "(checked_at LIKE='".$_GET["cal_checked_at"]."') AND ";
    if(@$_GET["txt_checked_position"]!="") $whereclause .= "(txt_checked_position LIKE '%".$_GET["txt_checked_position"]."%') AND ";
    if(@$_GET["txt_checked_by"]!="") $whereclause .= "(checked_by LIKE '%".$_GET["txt_checked_by"]."%') AND ";
    
	$db->addtable("joborder");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("joborder");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$joborder = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='job_order_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No","",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('client_id');\">Client</div>",
                        "<div onclick=\"sorting('pkwt_for');\">PKWT</div>",
						"<div onclick=\"sorting('position_id');\">Position</div>",
						"<div onclick=\"sorting('report_to');\">Report To</div>",
                        "<div onclick=\"sorting('candidate_id');\">Candidate</div>",
						"<div onclick=\"sorting('join_start');\">Start Join Date</div>",
                        "<div onclick=\"sorting('join_end');\">End Join Date</div>",
						"<div onclick=\"sorting('status_category_id');\">Category</div>",
						"<div onclick=\"sorting('reason_of_hiring');\">Reason for Hiring</div>",
						"<div onclick=\"sorting('w_hours_start');\">Working Hours</div>",
                        "<div onclick=\"sorting('basic_salary');\">Basic Salary</div>",
						"Amandemen",
						"Extension",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>"));?>
	<?php foreach($joborder as $no => $joborder_){ ?>
		<?php
			$actions = "<a href=\"job_order_add.php?copyid=".$joborder_["id"]."\">New PKWT</a> |
						<a href=\"job_order_edit.php?id=".$joborder_["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$joborder_["id"]."';}\">Delete</a> | 
						<a href=\"job_order_extension.php?joborder_id=".$joborder_["id"]."\">Extend</a>
						";
                        
			$client = $db->fetch_single_data("clients","name",array("id"=>$joborder_["client_id"]));
			if(is_numeric($joborder_["pkwt_for"])) $joborder_["pkwt_for"]++;
            $position = $db->fetch_single_data("positions","name",array("id"=>$joborder_["position_id"]));
            $candidate = $db->fetch_single_data("candidates","name",array("id"=>$joborder_["candidate_id"]));
            $status_category = $db->fetch_single_data("status_categories","name",array("id"=>$joborder_["status_category_id"]));
            $w_hours = $joborder_["w_hours_start"]." - ".$joborder_["w_hours_end"];
			$overtime = ($joborder_["overtime"]=='1') ? $overtime = "Follow DEPNAKER" : $overtime = "No OT";
			$tax_paid_company = ($joborder_["tax_paid_company"]=='1') ? $tax_paid_company = "Yes" : $tax_paid_company = "No";
			$medical = ($joborder_["medical"]=='1') ? $medical = "Covered Insurance" : $medical = "No";
			$lebaran = ($joborder_["lebaran"]=='1') ? $lebaran = "Based on government rules" : $lebaran = "";
		?>
		<?=$t->row(
					array($no+$start+1,$actions,
						"<a href=\"job_order_edit.php?id=".$joborder_["id"]."\">".$joborder_["id"]."</a>",
                        "<a href=\"job_order_edit.php?id=".$joborder_["id"]."\">".$client."</a>",
                        "<a href=\"job_order_edit.php?id=".$joborder_["id"]."\">".$joborder_["pkwt_for"]."</a>",
                        "<a href=\"job_order_edit.php?id=".$joborder_["id"]."\">".$position."</a>",
						$joborder_["report_to"],
                        $candidate,
                        format_tanggal($joborder_["join_start"]),
                        format_tanggal($joborder_["join_end"]),
                        $status_category,
                        $joborder_["reason_of_hiring"],
                        $w_hours,
                        format_amount($joborder_["basic_salary"]),
						$db->fetch_single_data("joborder","concat(count(0)) as amandemen",array("is_amandemen"=>"1","joborder_id"=>$joborder_["id"])) * 1,
						$db->fetch_single_data("joborder","concat(count(0)) as amandemen",array("is_amandemen"=>"0","joborder_id"=>$joborder_["id"])) * 1,
						format_tanggal($joborder_["created_at"],"dMY"),
						$joborder_["created_by"]),
					array("align='right' valign='top'","","","","","","","","","","","","align='right' valign='top'","nowrap","nowrap")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>