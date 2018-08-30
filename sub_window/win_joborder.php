<?php
	include_once "win_head.php";
	
	$db->addtable($_tablename);
	if($_POST["keyword"] != "") $db->awhere(
															"client_id IN (SELECT id FROM clients WHERE name LIKE '%".$_POST["keyword"]."$')
															OR position_id IN (SELECT id FROM positions WHERE name LIKE '%".$_POST["keyword"]."%')
															OR candidate_id IN (SELECT id FROM candidates WHERE name LIKE '%".$_POST["keyword"]."%' OR code LIKE '%".$_POST["keyword"]."%')"
													);
	$db->limit(500);
	$db->order("created_at DESC");
	$_data = $db->fetch_data(true);
?>
<script>
	function all_data_update_load(jo_id,code,name,birthdate,sex,tax_status_id,medical_status_id,position,join_start,remarks,basic_salary,meal_transport,comm_allow,fixed_allow,address,phone,ktp,jamsostek,bpjs_kesehatan,bpjs_ketenagakerjaan,email,bank_account,npwp){
		parent.document.getElementById("<?=$_GET["name"];?>").value = jo_id;
		parent.document.getElementById("sw_caption_<?=$_GET["name"];?>").innerHTML = jo_id;
		parent.document.getElementById("candidate_code").innerHTML = code;
		parent.document.getElementById("candidate_name").innerHTML = name;
		parent.document.getElementById("candidate_birthdate").innerHTML = birthdate;
		parent.document.getElementById("candidate_sex").innerHTML = sex;
		parent.document.getElementById("tax_status_id").value = tax_status_id;
		parent.document.getElementById("medical_status_id").value = medical_status_id;
		parent.document.getElementById("jo_position").innerHTML = position;
		parent.document.getElementById("jo_join_start").innerHTML = join_start;
		parent.document.getElementById("jo_remarks").innerHTML = remarks;
		parent.document.getElementById("jo_basic_salary").innerHTML = basic_salary;
		parent.document.getElementById("jo_meal_transport").innerHTML = meal_transport;
		parent.document.getElementById("jo_comm_allowance").innerHTML = comm_allow;
		parent.document.getElementById("jo_fixed_allowance").innerHTML = fixed_allow;
		parent.document.getElementById("candidate_address").innerHTML = address;
		parent.document.getElementById("candidate_phone").innerHTML = phone;
		parent.document.getElementById("candidate_ktp").innerHTML = ktp;
		parent.document.getElementById("candidate_jamsostek").innerHTML = jamsostek;
		parent.document.getElementById("candidate_bpjs_kesehatan").innerHTML = bpjs_kesehatan;
		parent.document.getElementById("candidate_bpjs_ketenagakerjaan").innerHTML = bpjs_ketenagakerjaan;
		parent.document.getElementById("candidate_email").innerHTML = email;
		parent.document.getElementById("candidate_bank_account").innerHTML = bank_account;
		parent.document.getElementById("candidate_npwp").innerHTML = npwp;
		parent.$.fancybox.close();
	}
</script>
<h3><b><?=$_title;?></b></h3>
<br><br>
<?=$f->start("","POST","?".$_SERVER["QUERY_STRING"]);?>
Search : <?=$f->input("keyword",$_POST["keyword"],"size='50'");?>&nbsp;<?=$f->input("search","Load","type='submit'");?>
<?=$f->end();?>
<br>
<?=$t->start("","data_content");?>
<?=$t->header(array("No","Job Order ID","Join Date","Category","Client","Candidate Code","Candidate Name","KTP","Sex"));?>
<?php 
	foreach($_data as $no => $data){
		$status_category = $db->fetch_single_data("status_categories","name",array("id" => $data["status_category_id"]));
		$client = $db->fetch_single_data("clients","name",array("id" => $data["client_id"]));
		$candidate_code = $db->fetch_single_data("candidates","code",array("id" => $data["candidate_id"]));
		$candidate_name = $db->fetch_single_data("candidates","name",array("id" => $data["candidate_id"]));
		$candidate_ktp = $db->fetch_single_data("candidates","ktp",array("id" => $data["candidate_id"]));
		$candidate_sex = $db->fetch_single_data("candidates","sex",array("id" => $data["candidate_id"]));
		$candidate_birthdate = $db->fetch_single_data("candidates","birthdate",array("id" => $data["candidate_id"]));
		$status_id = $db->fetch_single_data("candidates","status_id",array("id" => $data["candidate_id"]));
		$tax_status_id = $status_id;
		if($status_id > 5) $medical_status_id = 1; else $medical_status_id = $status_id;
		$position = $db->fetch_single_data("positions","name",array("id" => $data["position_id"]));
		$candidate_address = $db->fetch_single_data("candidates","address",array("id" => $data["candidate_id"]));
		$candidate_phone = $db->fetch_single_data("candidates","phone",array("id" => $data["candidate_id"]));
		$candidate_jamsostek = $db->fetch_single_data("candidates","phone",array("id" => $data["candidate_id"]));
		$bpjs_kesehatan = $db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => "1","candidate_id" => $data["candidate_id"],"pisa" => "peserta"));
		$bpjs_ketenagakerjaan = $db->fetch_single_data("bpjs","bpjs_id",array("bpjs_type" => "2","candidate_id" => $data["candidate_id"],"pisa" => "peserta"));
		$candidate_email = $db->fetch_single_data("candidates","email",array("id" => $data["candidate_id"]));
		$candidate_bank_account = $db->fetch_single_data("candidates","concat(bank_name,':',bank_account) as bank",array("id" => $data["candidate_id"]));
		$candidate_npwp = $db->fetch_single_data("candidates","npwp",array("id" => $data["candidate_id"]));
		//code,name,birthdate,sex,status,join_start,remarks,basic_salary,meal_transport,comm_allow,fixed_allow,address,phone,ktp,jamsostek,bpjs_kesehatan,bpjs_ketenagakerjaan,email,bank_account,npwp
		$actions = "onclick=\"all_data_update_load('".$data["id"]."'
																					,'".$candidate_code."'
																					,'".$candidate_name."'
																					,'".$candidate_birthdate."'
																					,'".$candidate_sex."'
																					,'".$tax_status_id."'
																					,'".$medical_status_id."'
																					,'".$position."'
																					,'".format_tanggal($data["join_start"])."'
																					,'".$data["remarks"]."'
																					,'".format_amount($data["basic_salary"])."'
																					,'".format_amount($data["meal_transport"])."'
																					,'".format_amount($data["comm_allowance"])."'
																					,'".format_amount($data["fixed_allowance"])."'
																					,'".$candidate_address."'
																					,'".$candidate_phone."'
																					,'".$candidate_ktp."'
																					,'".$candidate_jamsostek."'
																					,'".$bpjs_kesehatan."'
																					,'".$bpjs_ketenagakerjaan."'
																					,'".$candidate_email."'
																					,'".$candidate_bank_account."'
																					,'".$candidate_npwp."');\"";
		
		echo $t->row(array($no+1,$data["id"],format_tanggal($data["join_start"]),$status_category,$client,$candidate_code,$candidate_name,$candidate_ktp,$candidate_sex),array("align='right' valign='top' ".$actions,"align='right' valign='top' ".$actions,$actions,$actions,$actions));
	} 
?>
<?=$t->end();?>