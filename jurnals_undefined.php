<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$transaction_id = $db->fetch_single_data("jurnals","transaction_id",array("id" => $_GET["deleting"]));
		$db->addtable("jurnals");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		$db->addtable("jurnal_details");
		$db->where("jurnal_id",$_GET["deleting"]);
		$db->delete_();
		if($transaction_id > 0) {
			$db->addtable("transactions");
			$db->where("id",$transaction_id);
			$db->delete_();
		}
		?> <script> window.location="?";</script> <?php
	}
?>
<script>
	function journal_export(){
		document.getElementById("filter").action = "jurnals_export.php";
		document.getElementById("filter").target = "_blank";
		document.getElementById("do_filter").click();
		document.getElementById("filter").action = "";
		document.getElementById("filter").target = "";
	}
</script>
<div class="bo_title">Undefined Journals</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$f->input("referer","jurnals_undefined","type='hidden'");?>
			<?=$t->start();?>
			<?php
                $tanggal = $f->input("tanggal",@$_GET["tanggal"],"type='date' style='width:150px;'")." - ".$f->input("tanggal2",@$_GET["tanggal2"],"type='date' style='width:150px;'");
				$invoice_num = $f->input("invoice_num",@$_GET["invoice_num"]);
				$description = $f->input("description",@$_GET["description"]);
                $created_at = $f->input("created_at",@$_GET["created_at"]);
				$sel_isapproved = $f->select("isapproved",array(""=>"","1"=>"Yes","2"=>"No"),@$_GET["isapproved"],"style='height:20px;'");
			?>
			<?=$t->row(array("Journal Date",$tanggal));?>
			<?=$t->row(array("Invoice No",$invoice_num));?>
			<?=$t->row(array("Description",$description));?>
			<?=$t->row(array("Created At",$created_at));?>
			<?=$t->row(array("Is Approved",$sel_isapproved));?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
		<?=$f->end();?>
	</div>
</div>

<?php
	$whereclause = "id IN (SELECT jurnal_id FROM jurnal_details WHERE coa = '') AND ";
	if(@$_GET["tanggal"]!="") $whereclause .= "(tanggal >= '".$_GET["tanggal"]."') AND ";
	if(@$_GET["tanggal2"]!="") $whereclause .= "(tanggal <= '".$_GET["tanggal2"]."') AND ";
	if(@$_GET["invoice_num"]!="") $whereclause .= "(invoice_num LIKE '%".$_GET["invoice_num"]."%') AND ";
	if(@$_GET["description"]!="") $whereclause .= "(description LIKE '%".$_GET["description"]."%') AND ";
	if(@$_GET["created_at"]!="") $whereclause .= "(created_at LIKE '%".$_GET["created_at"]."%') AND ";
	if(@$_GET["isapproved"]=="1") $whereclause .= "(isapproved = '1') AND ";
	if(@$_GET["isapproved"]=="2") $whereclause .= "(isapproved = '0') AND ";
	
	$db->addtable("jurnals");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("jurnals");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] == "") $_GET["sort"] = "tanggal DESC";
	$db->order($_GET["sort"]);
	
	$jurnals = $db->fetch_data(true);
?>
	
	<?=$f->input("allJournals","Back to All Journals","type='button' onclick=\"window.location='jurnals_list.php';\"");?>
	<?=$f->input("export","Export","type='button' onclick='journal_export();'");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('tanggal');\">Journal Date</div>",
						"<div onclick=\"sorting('invoice_num');\">Invoice No</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						"Debit",
						"Credit",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($jurnals as $no => $jurnal){ ?>
		<?php
			$actions = "<a href=\"jurnals_edit.php?id=".$jurnal["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$jurnal["id"]."';}\">Delete</a>
						";
                        
			$debit = $db->fetch_single_data("jurnal_details","concat(sum(debit))",array("jurnal_id" => $jurnal["id"]));
			$credit = $db->fetch_single_data("jurnal_details","concat(sum(kredit))",array("jurnal_id" => $jurnal["id"]));
			$a = strpos($jurnal["description"],"{prf_id:");
			if($a > 0){
				$b = strpos($jurnal["description"],"}",$a)-1;
				$pattern = "{".substr($jurnal["description"],$a+1,$b-$a)."}";
				$prf_id = explode(":",substr($jurnal["description"],$a+1,$b-$a))[1];
				$link = "<a target='_BLANK' href='prf_view.php?id=".$prf_id."'>".$pattern."</a>";
				$jurnal["description"] = str_replace($pattern,$link,$jurnal["description"]);
			}
			$invoice_id = $db->fetch_single_data("invoice","id",["num" => $jurnal["invoice_num"].":LIKE"]);
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"jurnals_edit.php?id=".$jurnal["id"]."\">".$jurnal["id"]."</a>",
                        format_tanggal($jurnal["tanggal"],"dMY"),
						"<a target='_BLANK' href=\"invoice_edit.php?id=".$invoice_id."\">".$jurnal["invoice_num"]."</a>",
						$jurnal["description"],
						format_amount($debit),
						format_amount($credit),
						format_tanggal($jurnal["created_at"],"dMY"),
						$jurnal["created_by"],
						$actions),
					array("align='right' valign='top'","","","","","align='right'","align='right'","","","","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>