<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("ppn");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">PPn</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$client_id 				= $f->select("client_id",$db->fetch_select_data("clients","id","name",array(),array("name")),@$_GET["client_id"],"style='height:25px;'");
				$no_invoices			= $f->input("no_invoices",@$_GET["no_invoices"]);
				$storage_position		= $f->input("storage_position",@$_GET["storage_position"]);
				$vat_no					= $f->input("vat_no",@$_GET["vat_no"]);
				$created_at				= $f->input("created_at",@$_GET["created_at"],"type='date'");
                
			?>
			     <?=$t->row(array("Client",$client_id));?>
                 <?=$t->row(array("Invoice No",$no_invoices));?>
                 <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
                 <?=$t->row(array("VAT No",$vat_no));?>
                 <?=$t->row(array("Created At",$created_at));?>
           
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
	if(@$_GET["client_id"]!="") 			$whereclause .= "(client_id = '".$_GET["client_id"]."') AND ";
	if(@$_GET["no_invoices"]!="") 			$whereclause .= "(no_invoices LIKE '%".$_GET["no_invoices"]."%') AND ";
	if(@$_GET["storage_position"]!="") 		$whereclause .= "(storage_position LIKE '%".$_GET["storage_position"]."%') AND ";
	if(@$_GET["vat_no"]!="") 				$whereclause .= "(vat_no LIKE '%".$_GET["vat_no"]."%') AND ";
    if(@$_GET["created_at"]!="") 			$whereclause .= "(created_at LIKE '".$_GET["created_at"]."') AND ";
   	
	$db->addtable("ppn");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("ppn");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$ppns = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='ppn_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('client_id');\">Client</div>",
						"<div onclick=\"sorting('no_invoices');\">Invoices No</div>",
						"<div onclick=\"sorting('storage_position');\">Contract Storage Position/Code</div>",
						"<div onclick=\"sorting('vat_no');\">VAT No</div>",
						"<div onclick=\"sorting('nominal');\">Nominal</div>",
						"Softcopy File",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						""));?>
	<?php foreach($ppns as $no => $ppn){ ?>
		<?php
			$actions = "<a href=\"ppn_edit.php?id=".$ppn["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$ppn["id"]."';}\">Delete</a>
						";
                        
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"ppn_edit.php?id=".$ppn["id"]."\">".$ppn["id"]."</a>",
						"<a href=\"ppn_edit.php?id=".$ppn["id"]."\">".$db->fetch_single_data("clients","name",array("id"=>$ppn["client_id"]))."</a>",
                        "<a href=\"ppn_edit.php?id=".$ppn["id"]."\">".substr($ppn["no_invoices"],0,50)."</a>",
						$ppn["storage_position"],
						$ppn["vat_no"],
						format_amount($ppn["nominal"]),
                        "<a href=\"files_ppn/".$ppn["softcopy"]."\" target=\"_BLANK\">".$ppn["softcopy"]."</a>",
                        format_tanggal($ppn["created_at"],"d M Y"),
						$actions),
					array("align='right' valign='top'",
							"valign='top'",
							"valign='top'",
							"valign='top'",
							"valign='top'",
							"valign='top'",
							"align='right' valign='top'",
							"valign='top'",""
						)
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>