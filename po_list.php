<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("po");$db->where("id",$_GET["deleting"]);$db->delete_();
		$db->addtable("po_detail");$db->where("po_id",$_GET["deleting"]);$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Purchase Order</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_num = $f->input("txt_num",@$_GET["txt_num"]);
                $txt_docdate = $f->input("txt_docdate",@$_GET["txt_docdate"]);
				$client = $f->select("client",$db->fetch_select_data("clients","id","name",array(),array(),"",true),@$_GET["client"],"style='height:25px'");
				$currency = $f->select("currency",$db->fetch_select_data("currencies","id","concat(id) as id2",array(),array(),"",true),@$_GET["currency"],"style='height:25px'");
                $total_amount = $f->input("total_amount",@$_GET["total_amount"]);
                $vat_amount = $f->input("vat_amount",@$_GET["vat_amount"]);
			?>
			<?=$t->row(array("PO Number",$txt_num));?>
			<?=$t->row(array("Document Date",$txt_docdate));?>
			<?=$t->row(array("Client",$client));?>
			<?=$t->row(array("Currency",$currency));?>
			<?=$t->row(array("Total Amount",$total_amount));?>
			<?=$t->row(array("VAT Amount",$vat_amount));?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
		<?=$f->end();?>
	</div>
</div>

<?php
	$whereclause = "";
	if(@$_GET["txt_num"]!="") $whereclause .= "(num LIKE '%".$_GET["txt_num"]."%') AND ";
    if(@$_GET["txt_docdate"]!="") $whereclause .= "(doc_date LIKE '%".$_GET["txt_docdate"]."%') AND ";
	if(@$_GET["client"]!="") $whereclause .= "(client_id = '".$_GET["client"]."') AND ";
	if(@$_GET["currency"]!="") $whereclause .= "(currency_id = '".$_GET["currency"]."') AND ";
	if(@$_GET["total_amount"]!="") $whereclause .= "(total = '".$_GET["total_amount"]."') AND ";
	if(@$_GET["vat_amount"]!="") $whereclause .= "(vat = '".$_GET["vat_amount"]."') AND ";
	
	$db->addtable("po");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("po");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$po = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='po_add.php';\"");?>
	<?=$f->input("xlsx_uploader","Upload From Xlsx","type='button' onclick=\"window.location='po_uploader.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('num');\">PO Number</div>",
						"<div onclick=\"sorting('doc_date');\">Document Date</div>",
						"<div onclick=\"sorting('client_id');\">Client</div>",
                        "<div onclick=\"sorting('currency_id');\">Currency</div>",
						"<div onclick=\"sorting('total');\">Total Amount</div>",
						"<div onclick=\"sorting('vat');\">VAT Amount</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($po as $no => $po_){ ?>
		<?php
			$actions = "<a href=\"po_edit.php?id=".$po_["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$po_["id"]."';}\">Delete</a>
						";
			$client = $db->fetch_single_data("clients","name",array("id"=>$po_["client_id"]));
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"po_edit.php?id=".$po_["id"]."\">".$po_["id"]."</a>",
						$po_["num"],
                        format_tanggal($po_["doc_date"],"dMY"),
                        $client,
                        $po_["currency_id"],
                        format_amount($po_["total"]),
                        format_amount($po_["vat"]),
						format_tanggal($po_["created_at"],"dMY"),
						$po_["created_by"],
						$actions),
					array("align='right' valign='top'","","","","","","align='right'","align='right'")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>