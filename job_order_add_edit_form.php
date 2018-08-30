<script>
	var ready_to_save = false;
	function any_changed(){
		if(ready_to_save == false){
			var is_any_changed = 0;
			var data = "";
			$.post( "ajax/job_order_ajax.php?id=<?=$_GET["id"];?>&mode=is_any_changed", { post_data: $("#job_order_form").serialize() }).done(function( data ) { 
				if(data == "1"){
					ready_to_save = true;
					if(confirm("Ada perubahan angka yang dapat mempengaruhi perhitungan gaji. Apakah ingin mencatat perubahan di kolom Remark?")){
						var efficient_date = prompt("Please enter efficient date (Year-Month-Day) :", "<?=date("Y-m-d");?>");
						//set remark by ajax post return to Remarks
						$.post( "ajax/job_order_ajax.php?id=<?=$_GET["id"];?>&mode=get_remarks&efficient_date=" + efficient_date, { post_data: $("#job_order_form").serialize() }).done(function( data2 ) { 
							document.getElementById("remarks").value = document.getElementById("remarks").value + data2;
							document.getElementById("remarks").focus();
						}); 
					} else {
						document.getElementById("job_order_form").submit();
					}
				} else {
					document.getElementById("job_order_form").submit();
				}
			}); 
		} else {
			document.getElementById("job_order_form").submit();
		}
	}
</script>
<?php
	if($db->fetch_single_data("joborder","joborder_id",array("id"=>$_GET["id"])) == 0) $is_parent = true; else $is_parent = false;
	if($db->fetch_single_data("joborder","is_amandemen",array("id"=>$_GET["id"])) == 1) $is_amandemen = true; else $is_amandemen = false;
	if(substr($_SERVER["PHP_SELF"],-9) == "_edit.php") $is_parent_edit = true; else $is_parent_edit = false;	
	if($is_parent_edit) $joborder_id = $db->fetch_single_data("joborder","joborder_id",array("id"=>$_GET["id"]));
	else  $joborder_id = $_GET["joborder_id"];
	if($is_parent && $is_parent_edit){		
		//echo $f->input("extension","Add Extension","type='button' onclick=\"window.location='job_order_extension.php?joborder_id=".$_GET["id"]."';\"")."&nbsp;";
		// echo $f->input("amandemen","Add Amandemen","type='button' onclick=\"window.location='job_order_extension.php?is_amandemen=1&joborder_id=".$_GET["id"]."';\"")."&nbsp;";
		/* echo "<br><br>";
		
		$extensions = $db->fetch_select_data("joborder","id","joborder_id",array("joborder_id"=>$_GET["id"],"is_amandemen"=>"0"),array("created_at"));
		$no = 0;
		foreach($extensions as $ext_joborder_id => $value){
			$no++;
			echo $f->input("extension","Edit Extension ".$no,"type='button' onclick=\"window.location='job_order_edit.php?id=".$ext_joborder_id."';\"")."&nbsp;";
		}
		echo "<br><br>"; */
		
		/* $amandemens = $db->fetch_select_data("joborder","id","joborder_id",array("joborder_id"=>$_GET["id"],"is_amandemen"=>"1"),array("created_at"));
		$no = 0;
		foreach($amandemens as $ext_joborder_id => $value){
			$no++;
			echo $f->input("amandemen","Edit Amandemen ".$no,"type='button' onclick=\"window.location='job_order_edit.php?id=".$ext_joborder_id."';\"")."&nbsp;";
		}
		echo "<br>"; */
	} 
	
	if(!$is_parent){
		if($is_amandemen){
			?> <script> bo_title.innerHTML = "Edit Job Order (Amandemen) "; </script> <?php
		} else {
			?> <script> bo_title.innerHTML = "Edit Job Order (Extension) "; </script> <?php
		}
	}
?>
<?=$f->start("job_order_form","POST","?id=".$_GET["id"]."&is_amandemen=".$_GET["is_amandemen"]."&joborder_id=".$_GET["joborder_id"],"enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
		<?=$t->row(array("Client",$sel_client));?>
		<?=$t->row(array("PKWT",$sel_pkwt_for));?>
		<?=$t->row(array("Position",$sel_position));?>
		<?=$t->row(array("Report To",$txt_report_to));?>
		<?=$t->row(array("Name of Candidate",$sel_candidate));?>
		<?=$t->row(array("Start Join Date",$cal_join_start));?>
		<?=$t->row(array("End Date",$cal_join_end));?>
		<?=$t->row(array("Category",$sel_status_category));?>
		<?=$t->row(array("Reason For Hiring",$txt_reason_of_hiring));?>
		<?=$t->row(array("Working Hours",$w_hours));?>
		<?=$t->row(array("Remarks",$txt_remarks));?>
		<?=$t->row(array("Take Home Pay",$txt_thp));?>
		<?=$t->row(array("Basic Salary",$txt_basic_salary."&nbsp;&nbsp;&nbsp;&nbsp;Tax Paid By Company ".$sel_tax_paid_company));?>
		<?=$t->row(array("Allowances",$allowance_rows));?>
		<?=$t->row(array("Overtime",$sel_overtime));?>
		<?=$t->row(array("Contract Status",$sel_contract_status));?>
		<!--<?=$t->row(array("Fixed Allowance",$txt_fixed_allowance));?>-->
		<?=$t->row(array("Benefits",$benefit_rows));?>
		<?=$t->row(array("Medical","Insurance : ".$sel_insurace_plans));?>
		<?=$t->row(array("",$rad_insurace_family_cover));?>
		<?=$t->row(array("","<table><tr><td width='100'>".$chk_insurance_is_inpatient."</td><td>".$txt_inpatient_ammount."</td></tr></table>"));?>
		<?=$t->row(array("","<table><tr><td width='100'>".$chk_insurance_is_outpatient."</td><td nowrap>".$txt_outpatient_ammount."&nbsp;&nbsp;&nbsp;&nbsp;".$chk_insurance_is_outpatient_allin."</td></tr></table>"));?>
		<?=$t->row(array("","<table><tr><td width='100'>".$chk_insurance_is_dental."</td><td>".$txt_dental_ammount."</td></tr></table>"));?>
		<?php
			$maternity_ammount = "<table>";
			$maternity_ammount .= "<tr><td>Normal</td><td>".$txt_maternity_normal."</td></tr>";
			$maternity_ammount .= "<tr><td>Cecar</td><td>".$txt_maternity_cecar."</td></tr>";
			$maternity_ammount .= "<tr><td>Miscarriage</td><td>".$txt_maternity_miscarriage."</td></tr>";
			$maternity_ammount .= "</table>";
		?>
		<?=$t->row(array("","<table><tr><td width='100' valign='top'>".$chk_insurance_is_maternity."</td><td>".$maternity_ammount."</td></tr></table>"));?>
		<?=$t->row(array("","<table><tr><td width='100'>".$chk_insurance_is_glasses."</td><td>".$txt_glasses_ammount."</td></tr></table>"));?>
		<?=$t->row(array("Lebaran Bonus",$txt_lebaran));?>
		<?=$t->row(array("Other Benefits",$txt_other_benefits));?>
		<?=$t->row(array("Approved At",$cal_approved_at));?>
		<?=$t->row(array("Approved Position",$txt_approved_position));?>
		<?=$t->row(array("Approved By",$txt_approved_by));?>
		<?=$t->row(array("Checked At",$cal_checked_at));?>
		<?=$t->row(array("Checked Position",$txt_checked_position));?>
		<?=$t->row(array("Checked By",$txt_checked_by));?>
		<?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<?=$f->input("save","Save","style='display:none;'");?> 
	<?=$f->input("job_order_form_submit","Save","type='button' onclick='any_changed();'");?> 
	<?php
		if($is_parent_edit){
			if(!$is_parent){
				echo $f->input("back","Back","type='button' onclick=\"window.location='job_order_edit.php?id=".$joborder_id."';\"")."&nbsp;";
			} else {
				echo $f->input("back","Back","type='button' onclick=\"window.location='".str_replace(array("_add","_edit","_extension"),"_list",$_SERVER["PHP_SELF"])."';\"")."&nbsp;";
			}
		} else echo $f->input("back","Back","type='button' onclick=\"window.location='job_order_edit.php?id=".$joborder_id."';\"")."&nbsp;";
	?>
<?=$f->end();?>
<script>
	$(function(){
		$('#insurance_is_outpatient_allin').click(function(){
			if(insurance_is_outpatient_allin.checked == true){
				insurance_is_outpatient.checked = true;
				insurance_is_dental.checked = false;
				insurance_is_maternity.checked = false;
				insurance_is_glasses.checked = false;
				dental_ammount.value = "";
				maternity_normal.value = "";
				maternity_cecar.value = "";
				maternity_miscarriage.value = "";
				glasses_ammount.value = "";
			}
		});
		$('#insurance_is_dental,#insurance_is_maternity,#insurance_is_glasses').click(function(){
			if(insurance_is_dental.checked == true
				|| insurance_is_maternity.checked == true
				|| insurance_is_glasses.checked == true
			){ 
				insurance_is_outpatient.checked = true;
				insurance_is_outpatient_allin.checked = false;
			}
		});
	});
</script>