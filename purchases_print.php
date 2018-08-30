<?php 
	$_GET["export"]="1";
	$_GET["do_filter"]="Load";
	$_isexport = true;
	include_once "head.php";
?>
<table width="100%"><tr><td align="center"><h3><b>Purchasing Requisition</b></h3></td></tr></table>
<table>
	<tr><td>Periode</td><td> : </td><td><?=format_tanggal($_GET["trx_periode"]."-01","M Y");?></td></tr>
</table>

<?php
	$whereclause = "";
	if(@$_GET["trx_periode"]!="") $whereclause .= "(periode LIKE '%".$_GET["trx_periode"]."%') AND ";
	else $whereclause .= "0 AND ";
	
	$db->addtable("purchases");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(@$_GET["sort"] == "") $_GET["sort"] = "id";
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$purchases = $db->fetch_data(true);
?>

	<?=$t->start("border='1' width='100%'","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('item');\">Item</div>",
						"<div onclick=\"sorting('esiro');\">ESIRO</div>",
						"<div onclick=\"sorting('created_by');\">Request By</div>",
						"Qty",
						"Unit",
						"Price",
						"Total"));?>
	
	<?php 
		$total = 0;
		foreach($purchases as $no => $purchase){ ?>
		<?php
			$esiro = "";
			if($purchase["esiro"] == "e") $esiro = "Empty";
			if($purchase["esiro"] == "s") $esiro = "Stock";
			if($purchase["esiro"] == "i") $esiro = "Inventory";
			if($purchase["esiro"] == "r") $esiro = "Replacement";
			if($purchase["esiro"] == "o") $esiro = "Other";
			$total += $purchase["total"];
		?>	
		<?=$t->row(
					array($no+$start+1,
						$purchase["item"],
						$esiro,
						$purchase["created_by"],
						$purchase["qty"],
						$purchase["unit"],
						number_format($purchase["price"],0,",","."),
						number_format($purchase["total"],0,",",".")),
					array("align='center' valign='top'","","","","align='right'","","align='right'","align='right'")
				);?>
	<?php } ?>
	<?=$t->row(
				array("<b>TOTAL CASH NEEDED</b>","","<b>".number_format($total,0,",",".")."</b>"),
				array("align='center' colspan='4'","colspan='3'","align='right'")
			);?>
	<?=$t->end();?>
	<br><br>
	<table border="1" width="100%">
		<tr><td style="height:50px" valign="top">Note :</td></tr>
	</table>
	<br><br>
	<table border="1" width="100%">
		<tr>
			<td align="center">Requester</td>
			<td align="center">Finance</td>
			<td align="center">Director</td>
		</tr>
		<tr><td style="height:80px">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	</table>
	<script> window.print(); window.close();</script>
<?php include_once "footer.php";?>