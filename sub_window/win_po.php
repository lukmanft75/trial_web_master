<?php
	include_once "win_head.php";
	?> <script> 
		if(parent.document.getElementById("client_id").value == ""){
			alert("Please select client first!"); 
			parent.$.fancybox.close();
		}
	</script> <?php
	if($_GET["client_id"] == ""){
		?> <script> window.location = location.href + "&client_id="+parent.document.getElementById("client_id").value; </script> <?php
	}
	$db->addtable($_tablename);
	$whereclause = "client_id = '".$_GET["client_id"]."'";
	if($_POST["keyword"] != "") $whereclause .= " AND (num LIKE '%".$_POST["keyword"]."%'
												OR doc_date LIKE '%".$_POST["keyword"]."%'
												OR description LIKE '%".$_POST["keyword"]."%'
												OR total = '".$_POST["keyword"]."')";
	$db->awhere($whereclause);
	$db->limit(1000);
	$db->order("created_at DESC");
	$_data = $db->fetch_data(true);
?>
<script>
	function parent_load(po_no,description,total,wcc_no){
		parent.document.getElementById("<?=$_GET["name"];?>").value = po_no;
		parent.document.getElementById("sw_caption_<?=$_GET["name"];?>").innerHTML = po_no;		
		parent.document.getElementById("description").value = description;	
		parent.document.getElementById("total").value = formatNumber(total);	
		parent.document.getElementById("invoice_description[0]").value = description;		
		parent.document.getElementById("reimbursement[0]").value = total;	
		parent.document.getElementById("wcc_no").value = wcc_no;	
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
<?=$t->header(array("No","Po No","Po Date","Description","Total","Total Invoiced","PO OnHand"));?>
<?php 
	foreach($_data as $no => $data){
		$invoiced_po = $db->fetch_single_data("invoice","concat(sum(total)) as total",array("po_no"=>$data["num"]));
		$po_onhand = $data["total"] - $invoiced_po;
		if($po_onhand != 0) {
			$wcc_no = $db->fetch_single_data("wcc","wcc_no",array("po_no" => $data["num"].":LIKE"));
			$actions = "onclick=\"parent_load('".$data["num"]."','".$data["description"]."','".$po_onhand."','".$wcc_no."');\"";
			echo $t->row(array($no+1,$data["num"],format_tanggal($data["doc_date"],"dMY"),$data["description"],format_amount($data["total"]),format_amount($invoiced_po),format_amount($po_onhand)),
					array("align='right' valign='top' ".$actions,"valign='top' ".$actions,"valign='top' ".$actions,"valign='top' ".$actions,"align='right' valign='top'".$actions,"align='right' valign='top'".$actions,"align='right' valign='top'".$actions));
		}
	}
?>
<?=$t->end();?>