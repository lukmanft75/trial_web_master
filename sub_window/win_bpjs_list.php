<?php
	include_once "win_head.php";
	
	if($_GET["deleting"]){
		$db->addtable("bpjs");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?bpjs_type=<?=$_GET["bpjs_type"];?>&candidate_id=<?=$_GET["candidate_id"];?>";</script> <?php
	}
	
	$db->addtable("bpjs");
	$db->awhere("candidate_id = '".$_GET["candidate_id"]."' AND bpjs_type = '".$_GET["bpjs_type"]."'");
	$databpjs = $db->fetch_data(true);
	
	if($_GET["bpjs_type"] == '1') $_title = "BPJS Kesehatan";  
	if($_GET["bpjs_type"] == '2') $_title = "BPJS Ketenagakerjaan";  
?>
<h3><b><?=$_title;?></b></h3>
<?php
	if($_GET["addnew"]==1 || isset($_POST["saving_new"])){
		include_once "win_bpjs_add.php";
	} else {
		echo $f->input("addnew","Add New BPJS","type='button' onclick=\"window.location='?addnew=1&".$_SERVER["QUERY_STRING"]."';\"");
	}
?>
<br><br>
<?=$t->start("","data_content");?>
<?php
	$arr_header = array();
	array_push($arr_header,"No");
	array_push($arr_header,"Code");
	array_push($arr_header,"Name");
	if($_GET["bpjs_type"] == '2') array_push($arr_header,"Mother's Name");
	array_push($arr_header,"Date of Birth");
	array_push($arr_header,"PISA");
	array_push($arr_header,"Sex");
	array_push($arr_header,"NIK");
	array_push($arr_header,"No BPJS");
	array_push($arr_header,"Remarks");
	array_push($arr_header,"");
	echo $t->header($arr_header);
?>
<?php 
	foreach($databpjs as $no => $data){
		$actions = "<a href=\"win_bpjs_edit.php?id=".$data["id"]."&".$_SERVER["QUERY_STRING"]."\">Edit</a> |
					<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$data["id"]."&bpjs_type=".$_GET["bpjs_type"]."&candidate_id=".$_GET["candidate_id"]."';}\">Delete</a>";
		if($data["softcopy"] != "") $actions .= " | <a href=\"../files_bpjs/".$data["softcopy"]."\" target=\"_BLANK\">BPJS</a>";
		if($data["file_ktp"] != "") $actions .= " | <a href=\"../files_bpjs/".$data["file_ktp"]."\" target=\"_BLANK\">KTP</a>";
		if($data["file_kk"] != "") $actions .= " | <a href=\"../files_bpjs/".$data["file_kk"]."\" target=\"_BLANK\">KK</a>";
		if($data["file_pernyataan"] != "") $actions .= " | <a href=\"../files_bpjs/".$data["file_pernyataan"]."\" target=\"_BLANK\">Surat Pernyataan</a>";
		if($data["file_kjpensiun"] != "") $actions .= " | <a href=\"../files_bpjs/".$data["file_kjpensiun"]."\" target=\"_BLANK\">Kartu Jaminan Pensiun</a>";
	
		//$actions = "onclick=\"parent_load('".$_name."','".$data[$_id_field]."','".$data[$_caption_field]."');\"";
		$arr_row = array();
		array_push($arr_row,$no+1);
		array_push($arr_row,$data["code"]);
		array_push($arr_row,$data["name"]);
		if($_GET["bpjs_type"] == '2') array_push($arr_row,$data["mothers_name"]);
		array_push($arr_row,$data["birthdate"]);
		array_push($arr_row,$data["pisa"]);
		array_push($arr_row,$data["sex"]);
		array_push($arr_row,$data["ktp"]);
		array_push($arr_row,$data["bpjs_id"]);
		array_push($arr_row,$data["remarks"]);
		array_push($arr_row,$actions);
		echo $t->row($arr_row,array("align='right' valign='top' ","align='right' valign='top' "));
	} 
?>
<?=$t->end();?>