<?php
	function position_names($position){
		$arr = explode("=>",$position);
		foreach($arr as $key => $_position){
			$x = explode("(",$_position);
			$return[] = trim($x[0]);
		}
		return $return;
	}
	
	function month_to_num($month){
		$month = strtolower(str_replace(array(" ",chr(10),chr(13)),"",$month));
		if(substr($month,0,3) == "jan") return "01";
		if(substr($month,0,3) == "feb" || substr($month,0,3) == "peb") return "02";
		if(substr($month,0,3) == "mar") return "03";
		if(substr($month,0,3) == "apr") return "04";
		if(substr($month,0,3) == "mei" || substr($month,0,3) == "may") return "05";
		if(substr($month,0,3) == "jun") return "06";
		if(substr($month,0,3) == "jul") return "07";
		if(substr($month,0,3) == "aug" || substr($month,0,3) == "agt") return "08";
		if(substr($month,0,3) == "sep") return "09";
		if(substr($month,0,1) == "o") return "10";
		if(substr($month,0,1) == "n") return "11";
		if(substr($month,0,2) == "de") return "12";
	}
	
	function column_parsing($rowdata){
		foreach($rowdata as $key => $header){
			if(preg_match("/(no)/",strtolower($header)) 							&& !isset($col["no"]))				$col["no"] = $key;
			if(preg_match("/(code)/",strtolower($header)) 							&& !isset($col["code"]))			$col["code"] = $key;
			
			if(strtolower(str_replace(array(" ",chr(10),chr(13),"-"),"",$header)) == "cc"
				&& !isset($col["cc"]))																					$col["cc"] = $key;
			
			if(preg_match("/(indohr)*(referral)/",strtolower($header)) 				&& !isset($col["indohr_referral"]))	$col["indohr_referral"] = $key;
			if(preg_match("/(nam[a,e])/",strtolower($header)) 						&& !isset($col["name"]))			$col["name"] = $key;
			if(preg_match("/(date)*(birth)/",strtolower($header)) 					&& !isset($col["date_of_birth"]))	$col["date_of_birth"] = $key;
			if(preg_match("/(educat)/",strtolower($header)) 				&& !isset($col["educational_background"]))	$col["educational_background"] = $key;
			if(preg_match("/(sex)/",strtolower($header)) 							&& !isset($col["sex"]))				$col["sex"] = $key;
			if(preg_match("/(status)/",strtolower($header)) 						&& !isset($col["status_pajak"]))	$col["status_pajak"] = $key;
			
			if(preg_match("/(asuransi)/",strtolower($header)) 
				&& !isset($col["position"]) 	
				&& !isset($col["status_asuransi"]))																		$col["status_asuransi"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"homebase") > 0
				&& isset($col["code"])
				&& !isset($col["homebase"]))																			$col["homebase"] = $key;
			
			if(preg_match("/(project)/",strtolower($header)) 						&& !isset($col["project"]))			$col["project"] = $key;
			if(preg_match("/(position)/",strtolower($header))						&& !isset($col["position"]))		$col["position"] = $key;
			if(preg_match("/(actual|original).*(board|date)/",strtolower($header))	&& !isset($col["join_date"]))		$col["join_date"] = $key;
			if(preg_match("/(permanent).*(by).*/",strtolower($header))				&& !isset($col["join_date"]))		$col["join_date"] = $key;
			if(preg_match("/(user|manager)/",strtolower($header)) 					&& !isset($col["user"]))			$col["user"] = $key;
			if(preg_match("/(pkwt.*i)/",strtolower($header)))															$col["pkwt"][] = $key;
			
			if((strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"pkwtnew") > 0
				||strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"pkwt") > 0)
				&& isset($col["pkwt"][2]) && $col["pkwt"][2]!= $key)													$col["pkwt"][] = $key;
			
			if(preg_match("/(pkwt.*zen)|(break.*zen)/",strtolower($header)))											$col["break"][count($col["pkwt"])-1] = $key;
			if(preg_match("/(amandemen)/",strtolower($header)))															$col["amandemen"][count($col["pkwt"])-1] = $key;
			if(preg_match("/(extend.*)|(extension.*)/",strtolower($header)))											$col["extension"][count($col["pkwt"])-1] = $key;
			if(preg_match("/(least.*day)/",strtolower($header))					&& !isset($col["leastday"]))			$col["leastday"] = $key;
			if(preg_match("/(remarks)/",strtolower($header))					&& !isset($col["remarks"]))				$col["remarks"] = $key;
			if(preg_match("/(thp)/",strtolower($header))						&& !isset($col["thp"]))					$col["thp"] = $key;
			
			if(preg_match("/(salary)/",strtolower($header))
				&& !preg_match("/(thp)/",strtolower($header))
				&& !isset($col["salary"]))																				$col["salary"] = $key;
			
			if((strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "ot"
				|| strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "overtime")
				&& !isset($col["overtime"]))																			$col["overtime"] = $key;
				
			if(strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "thr"
				&& !isset($col["thr"]))																					$col["thr"] = $key;
				
			if(strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "asuransi"
				&& !isset($col["asuransi"])
				&& isset($col["status_asuransi"])
				&& $col["status_asuransi"] != $key)																		$col["asuransi"] = $key;
				
			if(strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "remarks"
				&& !isset($col["remarks2"])
				&& isset($col["remarks"])
				&& $col["remarks"] != $key)																				$col["remarks2"] = $key;
				
			if((strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "shift"
				|| strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "benefit")
				&& !isset($col["benefit"]))																				$col["benefit"] = $key;
			
			if(strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)) == "address"
				&& !isset($col["address"]))																				$col["address"] = $key;
			
			if((substr(strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),0,5) == "phone"
				|| strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"telp") > 0)
				&& !isset($col["phone"]))																				$col["phone"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"account") > 0
				&& isset($col["phone"])
				&& !isset($col["bank_account"]))																		$col["bank_account"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"ktp") > 0
				&& isset($col["phone"])
				&& !isset($col["ktp"]))																					$col["ktp"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"npwp") > 0
				&& isset($col["phone"])
				&& !isset($col["npwp"]))																				$col["npwp"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"jamsostek") > 0
				&& isset($col["phone"])
				&& !isset($col["jamsostek"]))																			$col["jamsostek"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"bpjs") > 0
				&& isset($col["phone"])
				&& !isset($col["bpjs"]))																				$col["bpjs"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13),"-"),"",$header)),"email") > 0
				&& isset($col["phone"])
				&& !isset($col["email"]))																				$col["email"] = $key;
				
			if(strpos(" ".strtolower(str_replace(array(" ",chr(10),chr(13)),"",$header)),"reasonoftermination") > 0
				&& isset($col["phone"])
				&& !isset($col["reason_of_termination"]))																$col["reason_of_termination"] = $key;
				
			
		}
		return $col;
	}
	
	function insert_jo($joborder_id,$client_id,$project_id,$is_amandemen,$pkwt_for,$position_id,$user,$candidate_id,$pkwt_from,$pkwt_to,$status_category_id,$remarks2,$thp,$basic_salary,$overtime,$thr,$asuransi){
		global $db,$__now,$__username,$__remoteaddr;
		$db->addtable("joborder");
		$db->addfield("joborder_id");			$db->addvalue($joborder_id);
		$db->addfield("is_amandemen");			$db->addvalue($is_amandemen);
		$db->addfield("pkwt_for");				$db->addvalue($pkwt_for);
		$db->addfield("client_id");				$db->addvalue($client_id);
		$db->addfield("project_id");			$db->addvalue($project_id);
		$db->addfield("position_id");			$db->addvalue($position_id);
		$db->addfield("report_to");				$db->addvalue($user);
		$db->addfield("candidate_id");			$db->addvalue($candidate_id);
		$db->addfield("join_start");			$db->addvalue($pkwt_from);
		$db->addfield("join_end");				$db->addvalue($pkwt_to);
		$db->addfield("status_category_id");	$db->addvalue($status_category_id);
		$db->addfield("w_hours_start");			$db->addvalue("08:00");
		$db->addfield("w_hours_end");			$db->addvalue("17:00");
		$db->addfield("remarks");				$db->addvalue($remarks2);
		$db->addfield("thp");					$db->addvalue($thp);
		$db->addfield("basic_salary");			$db->addvalue($basic_salary);
		$db->addfield("overtime");				$db->addvalue($overtime);
		$db->addfield("thr");					$db->addvalue($thr);
		$db->addfield("asuransi");				$db->addvalue($asuransi);
		$db->addfield("contract_status");		$db->addvalue(1);
		$db->addfield("created_at");			$db->addvalue($__now);
		$db->addfield("created_by");			$db->addvalue($__username);
		$db->addfield("created_ip");			$db->addvalue($__remoteaddr);
		$db->addfield("updated_at");			$db->addvalue($__now);
		$db->addfield("updated_by");			$db->addvalue($__username);
		$db->addfield("updated_ip");			$db->addvalue($__remoteaddr);
		return $db->insert();
	}
	
	function saveConfig($arr){
		$filename = "upload_files/all_data_update_uploader.conf";
		if(file_exists($filename)){
			$handle = fopen($filename, "r");
			$config = fread($handle, filesize($filename));
			fclose($handle);
		} else {
			$config = "";
		}
		
		$configs = explode(chr(13).chr(10),$config);
		$xx = 1;
		$config = "";
		foreach($configs as $_config){
			if($arr["sheet"] == $xx){
				if($arr["skipSheet"] == "Skip"){
					$config .= chr(13).chr(10);
				} else {
					$config .= serialize($arr).chr(13).chr(10);
				}
			} else {
				$config .= $_config.chr(13).chr(10);
			}
			$xx++;
		}
		
		$fp = fopen($filename, "w");
		fwrite($fp, $config);
		fclose($fp);
	}
	
	function getConfig($index){
		$filename = "upload_files/all_data_update_uploader.conf";
		$handle = fopen($filename, "r");
		$config = fread($handle, filesize($filename));
		fclose($handle);
		$configs = explode(chr(13).chr(10),$config);
		return unserialize($configs[$index-1]);
	}
?>