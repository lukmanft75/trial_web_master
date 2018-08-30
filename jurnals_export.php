<?php
	$filename = "journals.xls";
	$__referer = $_GET["referer"];
	if($__referer == "jurnals_unbalance") $filename = "unbalance_journals.xls";
	if($__referer == "jurnals_undefined") $filename = "undefined_journals.xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=".$filename);
	header("Pragma: no-cache");
	header("Expires: 0");
	$_isexport = true;
	include_once "head.php";
	
	
	$whereclause = "";
	$title = "JOURNALS";
	if($__referer == "jurnals_unbalance"){
		$whereclause = "id IN (
							SELECT jurnal_id FROM (
								SELECT jurnal_id,(sum(debit) - sum(kredit)) as total FROM jurnal_details GROUP BY jurnal_id
							) as tbl WHERE (total > 0.09 OR total < -0.09)
					) AND ";
		$title = "UNBALANCE JOURNALS";
	}
	
	if($__referer == "jurnals_undefined"){
		$whereclause = "id IN (SELECT jurnal_id FROM jurnal_details WHERE coa = '') AND ";
		$title = "UNDEFINED JOURNALS";
	}
	
	if(@$_GET["tanggal"]!="") $whereclause .= "(tanggal >= '".$_GET["tanggal"]."') AND ";
	if(@$_GET["tanggal2"]!="") $whereclause .= "(tanggal <= '".$_GET["tanggal2"]."') AND ";
	if(@$_GET["invoice_num"]!="") $whereclause .= "(invoice_num LIKE '%".$_GET["invoice_num"]."%') AND ";
	if(@$_GET["prf_number"]!="") {
		$prf_id = $db->fetch_single_data("prf","id",["code_number" => $_GET["prf_number"]]);
		if(!$prf_id) $prf_id = 0;
		$whereclause .= "(description LIKE '% {prf_id:".$prf_id."%') AND ";
	}
	if(@$_GET["description"]!="") $whereclause .= "(description LIKE '%".$_GET["description"]."%') AND ";
	if(@$_GET["created_at"]!="") $whereclause .= "(created_at LIKE '%".$_GET["created_at"]."%') AND ";
	if(@$_GET["isapproved"]=="1") $whereclause .= "(isapproved = '1') AND ";
	if(@$_GET["isapproved"]=="2") $whereclause .= "(isapproved = '0') AND ";
	
	$db->addtable("jurnals");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(@$_GET["sort"] == "") $_GET["sort"] = "tanggal DESC";
	$db->order($_GET["sort"]);
	$jurnals = $db->fetch_data(true);
?>
<table><tr><td colspan="10" align="center"><h3><b><?=$title;?></b></h3></td></tr></table>
<table border="1">
	<tr>
		<td><b>Primary Key</b></td>
		<td><b>Journal ID</b></td>
		<td><b>Tanggal</b></td>
		<td><b>Invoice No</b></td>
		<td><b>Description</b></td>
		<td><b>Currency</b></td>
		<td><b>COA</b></td>
		<td></td>
		<td><b>Debit</b></td>
		<td><b>Credit</b></td>
	</tr>
	<?php 
		foreach($jurnals as $jurnal){
			$jurnal_details = $db->fetch_all_data("jurnal_details",[],"jurnal_id = '".$jurnal["id"]."'");
			foreach($jurnal_details as $jurnal_detail){
				$coaDescription = $db->fetch_single_data("coa","description",["coa" => $jurnal_detail["coa"]]);
				?>
				<tr>
					<td><?=$jurnal_detail["id"];?></td>
					<td><?=$jurnal["id"];?></td>
					<td><?=$jurnal["tanggal"];?></td>
					<td><?=$jurnal["invoice_num"];?></td>
					<td><?=$jurnal_detail["description"];?></td>
					<td><?=$jurnal["currency_id"];?></td>
					<td><?=$jurnal_detail["coa"];?></td>
					<td><?=$coaDescription;?></td>
					<td align="right"><?=$jurnal_detail["debit"];?></td>
					<td align="right"><?=$jurnal_detail["kredit"];?></td>
				</tr>
				<?php
			}
		}
	?>
</table>