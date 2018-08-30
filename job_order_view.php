<?php include_once "head.php";

	$db->addtable("joborder");$db->where("id",$_GET["id"]);$db->limit(1);$joborder = $db->fetch_data();
    $client = $db->fetch_single_data("clients","name",array("id"=>$joborder["client_id"]));
    $client_address = $db->fetch_single_data("clients","address",array("id"=>$joborder["client_id"]));
    $client_phone = $db->fetch_single_data("clients","phone",array("id"=>$joborder["client_id"]));
    $position = $db->fetch_single_data("positions","name",array("id"=>$joborder["position_id"]));
    $candidate = $db->fetch_single_data("candidates","name",array("id"=>$joborder["candidate_id"]));
    $category = $db->fetch_single_data("status_categories","name",array("id"=>$joborder["status_category_id"]));
	
	$overtime = "[&nbsp;&nbsp;] Follow DEPNAKER &nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;] No OT &nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;] Fix Rate";
    if($joborder["overtime"] == 1) $overtime = str_replace("[&nbsp;&nbsp;] Follow DEPNAKER","[X] Follow DEPNAKER",$overtime);
    else if($joborder["overtime"] == 2) $overtime = str_replace("[&nbsp;&nbsp;] No OT","[X] No OT",$overtime);
    else if($joborder["overtime"] == 3) $overtime = str_replace("[&nbsp;&nbsp;] Fix Rate","[X] Fix Rate",$overtime);
	
    if($joborder["contract_status"] == 1) $contract_status = "[X] Contract (PKWT) &nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;] Permanent (PKWTT)";
    else if($joborder["contract_status"] == 2) $contract_status = "[&nbsp;&nbsp;] Contract (PKWT) &nbsp;&nbsp;&nbsp;&nbsp; [X] Permanent (PKWTT)";
	
    if($joborder["tax_paid_company"] == 1) $tax_paid_company = "[X] Yes &nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;] No";
    else if($joborder["tax_paid_company"] == 2) $tax_paid_company = "[&nbsp;&nbsp;] Yes &nbsp;&nbsp;&nbsp;&nbsp; [X] No";
	
	$insurance_corp = $db->fetch_single_data("insurance_plan","insurance_corp",array("id"=>$joborder["insurace_plan_id"]));
	$plan = $db->fetch_single_data("insurance_plan","plan",array("id"=>$joborder["insurace_plan_id"]));
	
	if($joborder["insurace_family_cover"] == 1) $familycover = "[X] Employee Only &nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;] Cover Family";
	else if($joborder["insurace_family_cover"] == 2) $familycover = "[&nbsp;&nbsp;] Employee Only &nbsp;&nbsp;&nbsp;&nbsp; [X] Cover Family";
	
	if($joborder["insurance_is_inpatient"] == 1) $inpatient = "[X] Inpatient"; else $inpatient = "[&nbsp;&nbsp;] Inpatient";
	if($joborder["insurance_is_outpatient"] == 1) $outpatient = "[X] Outpatient"; else $outpatient = "[&nbsp;&nbsp;] Outpatient";
	if($joborder["insurance_is_dental"] == 1) $dental = "[X] Dental"; else $dental = "[&nbsp;&nbsp;] Dental";
	if($joborder["insurance_is_maternity"] == 1) $maternity = "[X] Maternity"; else $maternity = "[&nbsp;&nbsp;] Maternity";
	if($joborder["insurance_is_glasses"] == 1) $glasses = "[X] Glasses"; else $glasses = "[&nbsp;&nbsp;] Glasses";

?>

<style>
.t1{border-top:1px solid black;}
</style>

<div class="bo_title">View Job Order</div>
<?php
    $to = "<b>PT. Indo Human Resource</b><br />
            &nbsp;&nbsp;&nbsp;Epicentrum Walk OFfice Suites 7th Floor, Unit 0709A<br />
            &nbsp;&nbsp;&nbsp;Komplek Rasuna Epicentrum<br />
            &nbsp;&nbsp;&nbsp;Jl. HR. Rasuna Said - Kuningan<br>
            &nbsp;&nbsp;&nbsp;Jakarta 12940";
    $phone = "(021) 29941058-9";
    $fax = "(021) 29941055";
    $from = "From :<br><br><b>".$client."</b><br>".$client_address."<br> Phone : ".$client_phone;
?>
<table frame='box'>
<tr><td>
<?php
echo
    $t->start("width='600' border='0' cellpadding='3' style='padding: 10px;'").
        $t->row(array($from,"Job Order"),array("width='350' style='word-wrap: break-word'","valign='middle' align='center' style='font-size:32px;font-weight:bold'")).
    $t->end();
?>
</td></tr>
<tr><td>
<tr><td><hr /><hr /></td></tr><tr><td>
<?php
    echo
    $t->start("width='400' cellpadding='3' style='padding: 10px;'").
        $t->row(array("To",": &nbsp;".$to)).
        $t->row(array("Phone",": &nbsp;".$phone)).
        $t->row(array("Fax",": &nbsp;".$fax)).
        $t->row(array("Date",": &nbsp;".date("d F y"))).
    $t->end();   
?>
</td></tr>
<tr><td><hr /></td></tr><tr><td>
<tr><td>
<?php
    echo
    $t->start("width='400' cellpadding='3' style='padding: 10px;'").
        $t->row(array("Position",": ".$position)).
        $t->row(array("Report to",": ".$joborder["report_to"])).
        $t->row(array("Name of Candidate",": ".$candidate)).
        $t->row(array("Join Date",": ".$joborder["join_start"]." -- ".$joborder["join_end"])).
        $t->row(array("Category",": ".$category)).
        $t->row(array("Reason for Hiring",": ".$joborder["reason_of_hiring"])).
        $t->row(array("Working Hours",": ".$joborder["w_hours_start"]." - ".$joborder["w_hours_end"]." WIB (8 Hours)")).
        $t->row(array("Remarks",": ".$joborder["remarks"])).
    $t->end();
    
?>
</td></tr>
<tr><td>
<?php
    echo
    $t->start("width='400' cellpadding='3' style='padding: 10px;'").
        $t->row(array("Basic Salary",": IDR ".format_amount($joborder["basic_salary"])." Gross/Month")).
        $t->row(array("Allowances"));
		
		$db->addtable("joborder_allowances");$db->where("joborder_id",$joborder["id"]);$jo_allo = $db->fetch_data();
		foreach($jo_allo as $jo_all){
			$allo_name = $db->fetch_single_data("allowances","name",array("id"=>$jo_all["allowance_id"]));
			echo $t->row(array("&nbsp;&nbsp;&nbsp;".$allo_name,": IDR ".format_amount($jo_all["price"])." Gross/Month"));
		}
		
	echo
        $t->row(array("Overtime",": ".$overtime)).
        $t->row(array("Contract Status",": ".$contract_status)).
        $t->row(array("Tax Paid By Company",": ".$tax_paid_company)).
        $t->row(array("Benefits"));
		
		$db->addtable("joborder_benefits");$db->where("joborder_id",$joborder["id"]);$jo_bene = $db->fetch_data();
		foreach($jo_bene as $jo_ben){
			$bene_name = $db->fetch_single_data("benefits","name",array("id"=>$jo_ben["benefits_id"]));
			echo $t->row(array("&nbsp;&nbsp;&nbsp;".$bene_name,": ".$jo_ben["percentage"]."% - IDR ".format_amount($jo_ben["price"])));
		}
	
	echo
        $t->row(array("Medical",": ".$insurance_corp." - ".$plan)).
        $t->row(array("","&nbsp;&nbsp;".$familycover)).
        $t->row(array("","&nbsp;&nbsp;".$inpatient)).
        $t->row(array("","&nbsp;&nbsp;".$outpatient)).
        $t->row(array("","&nbsp;&nbsp;".$dental)).
        $t->row(array("","&nbsp;&nbsp;".$maternity)).
        $t->row(array("","&nbsp;&nbsp;".$glasses)).
        $t->row(array("Lebaran Bonus",": ".$joborder["lebaran"])).
        $t->row(array("Other Benefits",": ".$joborder["other_benefits"])).
    $t->end();
?>
</td></tr>
<tr><td>
<?php
    echo
    $t->start("width='600' cellpadding='3' style='padding: 10px;'").
        $t->row(array("Approved By :","Checked By :")).
        $t->row(array("&nbsp;")).
        $t->row(array("&nbsp;")).
        $t->row(array("&nbsp;")).
        $t->row(array("&nbsp;")).
        $t->row(array("&nbsp;")).
        $t->row(array("<hr>","<hr>")).
        $t->row(array($joborder["approved_position"],$joborder["checked_position"])).
        $t->row(array("Name : ".$joborder["approved_by"],"Name : ".$joborder["checked_by"])).
    $t->end();
?>
</td></tr>
</table>
<br />
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>
