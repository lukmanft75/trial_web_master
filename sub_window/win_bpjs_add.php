<?php
	
	
	if(isset($_POST["saving_new"])){
		$db->addtable("bpjs");
		$db->addfield("bpjs_type");			$db->addvalue($_GET["bpjs_type"]);
		$db->addfield("candidate_id");		$db->addvalue($_GET["candidate_id"]);
		$db->addfield("code");				$db->addvalue($_POST["code"]);
		$db->addfield("name");				$db->addvalue($_POST["name"]);
		$db->addfield("mothers_name");		$db->addvalue($_POST["mothers_name"]);
		$db->addfield("birthdate");			$db->addvalue($_POST["birthdate"]);
		$db->addfield("sex");				$db->addvalue($_POST["sex"]);
		$db->addfield("status_id");			$db->addvalue($_POST["status_id"]);
		$db->addfield("pisa");				$db->addvalue($_POST["pisa"]);
		$db->addfield("pkwt_from");			$db->addvalue($_POST["pkwt_from"]);
		$db->addfield("basic_salary");		$db->addvalue($_POST["basic_salary"]);
		$db->addfield("ktp");				$db->addvalue($_POST["ktp"]);
		$db->addfield("bpjs_id");			$db->addvalue($_POST["bpjs_id"]);
		$db->addfield("email");				$db->addvalue($_POST["email"]);
		$db->addfield("remarks");			$db->addvalue($_POST["remarks"]);
		$db->addfield("info_to_empl_at");	$db->addvalue($_POST["info_to_empl_at"]);
		$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");		$db->addvalue($__username);
		$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] > 0){
			$bpjs_id = $inserting["insert_id"];
			foreach($_FILES as $file_mode => $arrfiles){
				if($arrfiles["tmp_name"]){
					$_ext = strtolower(pathinfo($_FILES['softcopy']['name'],PATHINFO_EXTENSION));
					// if($file_mode == "softcopy") $softcopy_name = $bpjs_id.".".$_ext;
					if($file_mode == "softcopy") $softcopy_name = $_GET["candidate_id"].".".$_ext;
					if($file_mode == "file_ktp") $softcopy_name = "ktp_".$bpjs_id.".".$_ext;
					if($file_mode == "file_kk") $softcopy_name = "kk_".$bpjs_id.".".$_ext;
					if($file_mode == "file_pernyataan") $softcopy_name = "pernyataan_".$bpjs_id.".".$_ext;
					if($file_mode == "file_kjpensiun") $softcopy_name = "kjpensiun_".$bpjs_id.".".$_ext;
					move_uploaded_file($arrfiles['tmp_name'],"../files_bpjs/".$softcopy_name);
					$db->addtable("bpjs");			$db->where("id",$bpjs_id);
					if($file_mode == "softcopy") $db->addfield("softcopy");
					if($file_mode == "file_ktp") $db->addfield("file_ktp");
					if($file_mode == "file_kk") $db->addfield("file_kk");
					if($file_mode == "file_pernyataan") $db->addfield("file_pernyataan");
					if($file_mode == "file_kjpensiun") $db->addfield("file_kjpensiun");
					$db->addvalue($softcopy_name);
					$db->update();
				}
			}
		}
		?><script> window.location="?<?=str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]);?>"; </script><?php
		
	}
	
	$candidate_id = $db->fetch_single_data("candidates","name",array("id"=>$_GET["candidate_id"]));
	$code = $f->input("code",$db->fetch_single_data("candidates","code",array("id"=>$_GET["candidate_id"])));
	$name = $f->input("name",@$_POST["name"]);
	$birthdate = $f->input("birthdate",@$_POST["birthdate"],"type='date'");
	$sex = $f->select("sex",array("M" => "M", "F" => "F"),@$_POST["sex"],"style='height:25px'");
	$status_id = $f->select("status_id",$db->fetch_select_data("statuses","id","name",array(),array(),"",true));
	$pisa = $f->select("pisa",array("peserta" => "Peserta", "istri" => "Istri", "suami"=>"Suami", "anak" => "Anak"),@$_POST["pisa"],"style='height:25px'");
	$pkwt_from = $f->input("pkwt_from",@$_POST["pkwt_from"],"type='date'");
	$jo_basic_salary = $db->fetch_single_data("joborder","basic_salary",array("candidate_id"=>$_GET["candidate_id"]),array("join_end DESC"));
	$basic_salary = $f->input("basic_salary",$jo_basic_salary,"type='number'");
	$ktp = $f->input("ktp",$_POST["ktp"]);
	$bpjs_id = $f->input("bpjs_id",$_POST["bpjs_id"]);
	$email = $f->input("email",$_POST["email"]);
	$remarks = $f->textarea("remarks",$_POST["remarks"]);
	$info_to_empl_at = $f->input("info_to_empl_at",@$_POST["info_to_empl_at"],"type='date'");
	$softcopy = $f->input("softcopy","","type='file'");
	$file_ktp = $f->input("file_ktp","","type='file'");
	if($_GET["bpjs_type"] == "1"){
		$file_kk = $f->input("file_kk","","type='file'");
		$file_pernyataan = $f->input("file_pernyataan","","type='file'");
	}
	if($_GET["bpjs_type"] == "2"){
		$file_kjpensiun = $f->input("file_kjpensiun","","type='file'");
		$txt_mothers_name = $f->input("mothers_name","");
	}
	
?>
<?=$f->start("","POST","?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"]),"enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
         <?=$t->row(array("Candidate",$candidate_id));?>
         <?=$t->row(array("Code",$code));?>
         <?=$t->row(array("Name",$name));?>
		 <?php
			if($_GET["bpjs_type"] == "2"){
				echo $t->row(array("Mother's Name",$txt_mothers_name));
			}
		 ?>
         <?=$t->row(array("Birthdate",$birthdate));?>
         <?=$t->row(array("Sex",$sex));?>
         <?=$t->row(array("Status",$status_id));?>
         <?=$t->row(array("Pisa",$pisa));?>
         <?=$t->row(array("PWKT From",$pkwt_from));?>
         <?=$t->row(array("Basic Salary",$basic_salary));?>
         <?=$t->row(array("NIK",$ktp));?>
         <?=$t->row(array("No BPJS",$bpjs_id));?>
         <?=$t->row(array("Email",$email));?>
         <?=$t->row(array("Remarks",$remarks));?>
         <?=$t->row(array("Info To Employer At",$info_to_empl_at));?>
         <?=$t->row(array("Softcopy BPJS",$softcopy));?>
         <?=$t->row(array("Softcopy KTP",$file_ktp));?>
		 <?php 
			if($_GET["bpjs_type"] == "1"){
				echo $t->row(array("Softcopy KK",$file_kk));
				echo $t->row(array("Surat Pernyataan",$file_pernyataan));
			} 
			if($_GET["bpjs_type"] == "2"){
				echo $t->row(array("Kartu Jaminan Pensiun",$file_kjpensiun));
			} 
		 ?>
	<?=$t->end();?>
	<br>
	<?=$f->input("saving_new","Save","type='submit'");?> <?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?".str_replace("addnew=1&","",$_SERVER["QUERY_STRING"])."';\"");?>
<?=$f->end();?>