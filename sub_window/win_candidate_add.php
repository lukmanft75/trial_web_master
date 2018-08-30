<?php
	if(isset($_POST["saving_new"])){
		$db->addtable("candidates");
		$db->addfield("code");					$db->addvalue($_POST["code"]);
		$db->addfield("name");					$db->addvalue($_POST["name"]);
		$db->addfield("birthdate");				$db->addvalue($_POST["birthdate"]);
		$db->addfield("sex");					$db->addvalue($_POST["sex"]);
		$db->addfield("status_id");				$db->addvalue($_POST["status_id"]);
		$db->addfield("address");				$db->addvalue($_POST["address"]);
		$db->addfield("phone");					$db->addvalue($_POST["phone"]);
		$db->addfield("bank_name");				$db->addvalue($_POST["bank_name"]);
		$db->addfield("bank_account");			$db->addvalue($_POST["bank_account"]);
		$db->addfield("ktp");					$db->addvalue($_POST["ktp"]);
		$db->addfield("npwp");					$db->addvalue($_POST["npwp"]);
		$db->addfield("email");					$db->addvalue($_POST["email"]);
		$db->addfield("created_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");			$db->addvalue($__username);
		$db->addfield("created_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");			$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$candidate_id = $inserting["insert_id"];
			$bpjs_id[1] = $_POST["bpjs_kesehatan"];
			$bpjs_id[2] = $_POST["bpjs_ketenagakerjaan"];
			for ($ii = 1 ; $ii <= 2 ; $ii++){
				if($bpjs_id[$ii] != ""){
					$db->addtable("bpjs");
					$db->addfield("bpjs_type");			$db->addvalue($ii);
					$db->addfield("candidate_id");		$db->addvalue($candidate_id);
					$db->addfield("code");				$db->addvalue($_POST["code"]);
					$db->addfield("name");				$db->addvalue($_POST["name"]);
					$db->addfield("birthdate");			$db->addvalue($_POST["birthdate"]);
					$db->addfield("sex");				$db->addvalue($_POST["sex"]);
					$db->addfield("status_id");			$db->addvalue($_POST["status_id"]);
					$db->addfield("pisa");				$db->addvalue("peserta");
					$db->addfield("ktp");				$db->addvalue($_POST["ktp"]);
					$db->addfield("bpjs_id");			$db->addvalue($bpjs_id[$ii]);
					$db->addfield("email");				$db->addvalue($_POST["email"]);
					$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("created_by");		$db->addvalue($__username);
					$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
					$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
					$db->addfield("updated_by");		$db->addvalue($__username);
					$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
					$db->insert();
				}
			}
			?> <script> parent_load('<?=$_name;?>','<?=$inserting["insert_id"];?>','<?=$_POST["name"];?>'); </script> <?php
		} else {
			javascript("window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';");
		}
	}
	
	if(@$_POST["code"] == ""){
		$candidate_month = date("Y-m");
		$prefix = "OS".substr($candidate_month,2,2).substr($candidate_month,5,2);
		$db->addtable("candidates");$db->addfield("code");$db->awhere("code like '".$prefix."%'");$db->order("code DESC");$db->limit(1);
		$candidate_code = $db->fetch_data();
		$candidate_code = $candidate_code["code"];
		if($candidate_code == ""){
			$candidate_code = $prefix."001";
		} else {
			$candidate_code_now = (str_replace($prefix,"",$candidate_code) * 1) + 1;
			$candidate_code = $prefix.substr("000",0,3-strlen($candidate_code_now)).$candidate_code_now;
		}
		$_POST["code"] = $candidate_code;
	}
	
	$code = $f->input("code",@$_POST["code"]);
	$name = $f->input("name",@$_POST["name"]);
	$birthdate = $f->input("birthdate",@$_POST["birthdate"],"type='date'");
	$sex = $f->select("sex",array("M" => "M", "F" => "F"),@$_POST["sex"],"style='height:25px'");
	$status_id = $f->select("status_id",$db->fetch_select_data("statuses","id","name",array(),array(),"",true));
	$address = $f->textarea("address",$_POST["address"]);
	$phone = $f->input("phone",$_POST["phone"]);
	$bank_name = $f->input("bank_name",$_POST["bank_name"]);
	$bank_account = $f->input("bank_account",$_POST["bank_account"]);
	$ktp = $f->input("ktp",$_POST["ktp"]);
	$npwp = $f->input("npwp",$_POST["npwp"]);
	$bpjs_kesehatan = $f->input("bpjs_kesehatan",$_POST["bpjs_kesehatan"]);
	$bpjs_ketenagakerjaan = $f->input("bpjs_ketenagakerjaan",$_POST["bpjs_ketenagakerjaan"]);
	$email = $f->input("email",$_POST["email"]);
	
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]));?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Code",$code));?>
         <?=$t->row(array("Name",$name));?>
         <?=$t->row(array("Birthdate",$birthdate));?>
         <?=$t->row(array("Sex",$sex));?>
         <?=$t->row(array("Status",$status_id));?>
         <?=$t->row(array("Address",$address));?>
         <?=$t->row(array("Phone",$phone));?>
         <?=$t->row(array("Bank Name",$bank_name));?>
         <?=$t->row(array("Bank Account",$bank_account));?>
         <?=$t->row(array("KTP",$ktp));?>
         <?=$t->row(array("NPWP",$npwp));?>
         <?=$t->row(array("BPJS Kesehatan",$bpjs_kesehatan));?>
         <?=$t->row(array("BPJS Ketenagakerjaan",$bpjs_ketenagakerjaan));?>
         <?=$t->row(array("Email",$email));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	<br>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';\"");?>
<?=$f->end();?>