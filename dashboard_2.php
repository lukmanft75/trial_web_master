<?php
include("classes/fusioncharts.php");
//$arrcategories = array("salesorder","outstanding","reimbursement","total_po","tax23","vat");
$arrcategories = array("salesorder","outstanding");
?>
<script type="text/javascript" src="fusioncharts/fusioncharts.js"></script>

<table width="100%">
	<tr>
		<?php
		foreach($arrcategories as $post_value){
			?> <td width="50%"><div id="chartpie3d-<?=$post_value;?>"></div></td> <?php
		}
		?>
	</tr>
</table>
<br><br>

<?php foreach($arrcategories as $post_value){ ?> <div id="chart-<?=$post_value;?>"></div><br><br> <?php } ?>

<table width="100%">
	<tr>
		<?php
		foreach($arrcategories as $post_value){
			?> <td width="50%"><div id="chartpyramid-<?=$post_value;?>"></div></td> <?php
		}
		?>
	</tr>
</table>


<?php
foreach($arrcategories as $post_value){
	$arrdata[$post_value]["chart"]["caption"] = $arr_posts[$post_value];
	$arrdata[$post_value]["chart"]["subcaption"] = "Periode ".date("F Y",mktime(0,0,0,substr($startdate,5,2),1,substr($startdate,0,4)));
	$arrdata[$post_value]["chart"]["subcaption"] .= " - ".date("F Y",mktime(0,0,0,substr($enddate,5,2),1,substr($enddate,0,4)));
	$arrdata[$post_value]["chart"]["theme"] = "ocean";
	$i_month = 0;
	foreach($total_value[$post_value][1] as $nowdate => $value){
		$arrdata[$post_value]["categories"][0]["category"][$i_month]["label"] = $nowdate;
		$db->addtable("divisions");
		$db->order("id");
		foreach($db->fetch_data(true) as $i_divisi => $arrs){
			$arrdata[$post_value]["dataset"][$i_divisi]["seriesname"] = $arrs["name"];
			$arrdata[$post_value]["dataset"][$i_divisi]["data"][$i_month]["value"] = $total_value[$post_value][$arrs["id"]][$nowdate];
		} $i_month++;
	}
	$fusioncharts = new FusionCharts("mscombi2d", "mscombi2d-".$post_value, "100%", 300, "chart-".$post_value, "json", json_encode($arrdata[$post_value]));
	$fusioncharts->render();
}

foreach($arrcategories as $post_value){
	$arrdata2[$post_value]["chart"]["caption"] = $arr_posts[$post_value];
	$arrdata2[$post_value]["chart"]["subcaption"] = "Periode ".date("F Y",mktime(0,0,0,substr($startdate,5,2),1,substr($startdate,0,4)));
	$arrdata2[$post_value]["chart"]["subcaption"] .= " - ".date("F Y",mktime(0,0,0,substr($enddate,5,2),1,substr($enddate,0,4)));
	$arrdata2[$post_value]["chart"]["theme"] = "ocean";
	$arrdata2[$post_value]["chart"]["showlegend"] = "1";
	$db->addtable("divisions");
	$db->order("id");
	foreach($db->fetch_data(true) as $i_divisi => $arrs){
		$arrdata2[$post_value]["data"][$i_divisi]["label"] = $arrs["name"];
		foreach($total_value[$post_value][$arrs["id"]] as $nowdate => $value){
			$arrdata2[$post_value]["data"][$i_divisi]["value"] += $value;
		}
	}
	
	$fusioncharts = new FusionCharts("pie3d", "pie3d-".$post_value, "100%", 300, "chartpie3d-".$post_value, "json", json_encode($arrdata2[$post_value]));
	$fusioncharts->render();
}

foreach($arrcategories as $post_value){
	$arrdata3[$post_value]["chart"]["caption"] = $arr_posts[$post_value];
	$arrdata3[$post_value]["chart"]["subcaption"] = "Periode ".date("F Y",mktime(0,0,0,substr($startdate,5,2),1,substr($startdate,0,4)));
	$arrdata3[$post_value]["chart"]["subcaption"] .= " - ".date("F Y",mktime(0,0,0,substr($enddate,5,2),1,substr($enddate,0,4)));
	$arrdata3[$post_value]["chart"]["bgcolor"] = "FFFFFF";
	$arrdata3[$post_value]["chart"]["basefontcolor"] = "333333";
	$arrdata3[$post_value]["chart"]["pyramidyscale"] = "400";
	
	$db->addtable("invoice");
	$db->addfield("distinct(concat(client_id)) as client_id");
	$db->addfield("concat(total) as salesorder");
	$db->addfield("concat(total-receive) as outstanding");
	$db->addfield("reimbursement");
	$db->addfield("total_po");
	$db->addfield("tax23");
	$db->addfield("vat");
	$db->awhere("issue_at >= '".$startdate."-01' AND issue_at <=  '".$enddate."-31'");
	foreach($db->fetch_data(true) as $invoices){
		$arrdata3[$post_value]["data"][$invoices["client_id"]]["value"] += $invoices[$post_value];
	}
	
	if(count($arrdata3[$post_value]["data"]) > 0){
		foreach($arrdata3[$post_value]["data"] as $client_id => $arrX){
			$arrdata3[$post_value]["data"][$client_id]["name"] = $db->fetch_single_data("clients","name",array("id" => $client_id));
		}
	}
	
	$fusioncharts = new FusionCharts("pyramid", "pyramid-".$post_value, "100%", 700, "chartpyramid-".$post_value, "json", json_encode($arrdata3[$post_value]));
	$fusioncharts->render();
}
?>