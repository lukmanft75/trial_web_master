<?php
	include_once "../common.php"; 
	if(isset($_GET["mode"])){ $_mode = $_GET["mode"]; } else { $_mode = ""; }
	if(isset($_GET["cost_center_code"])){ $cost_center_code = $_GET["cost_center_code"]; } else { $cost_center_code = ""; }
	if(isset($_GET["nominal"])){ $nominal = $_GET["nominal"]; } else { $nominal = ""; }
	if($_mode == "get_select_checker" || $_mode == "get_select_signer" || $_mode == "get_select_approve"){
		$projects = explode(":",$project);
		$project_id = $projects[0];
		$scope_id = $projects[1];
		if($projects[2] > 0) $region_id = $projects[2];
		else $region_id = $_GET["region_id"];
		
		$project_id = $db->fetch_single_data("cost_centers","project_id",["code"=>$cost_center_code]);
		$scope_id = $db->fetch_single_data("cost_centers","scope_id",["code"=>$cost_center_code]);
		$region_ids = pipetoarray($db->fetch_single_data("cost_centers","region_ids",["code"=>$cost_center_code]));
		
		$whereWithRegion = "";
		if(count($region_ids) > 0){
			$whereWithRegion = " AND (";
			foreach($region_ids as $region_id){
				if($region_id > 0) $whereWithRegion .= " region_id = '".$region_id."' OR ";
			}
			$whereWithRegion = substr($whereWithRegion,0,-4).") ";
		}
		
		if($_mode == "get_select_checker"){
			$whereRole = " AND role='checker'";
		}
		if($_mode == "get_select_signer"){
			$whereRole = " AND role='signer'";
		}
		if($_mode == "get_select_approve"){
			if($nominal <= $db->fetch_single_data("indottech_roles","approve_max",[],["approve_max DESC"])){
				$whereRole = " AND role='approver' AND approve_min <= '".$nominal."' AND approve_max >= '".$nominal."'";
			} else {
				$checkers = array();
				$checkers["ahanifah@corphr.com"] = "ahanifah@corphr.com";
				echo "ahanifah@corphr.com";
				exit();
			}
		}
		
		$checkers = array();
		$data = $db->fetch_all_data("indottech_roles",[],"project_id='".$project_id."' AND scope_id='".$scope_id."' $whereWithRegion $whereRole");
		if(count($data) <= 0) $data = $db->fetch_all_data("indottech_roles",[],"project_id='".$project_id."' AND scope_id='".$scope_id."' $whereRole");
		
		echo $db->fetch_single_data("users","email",["id" => $data[0]["user_id"]]);
	}
?>