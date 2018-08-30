<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("wcc");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">WCC</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$wcc_no 				= $f->input("wcc_no",@$_GET["wcc_no"]);
				$po_no			 		= $f->input("po_no",@$_GET["po_no"]);
				$storage_position 		= $f->input("storage_position",@$_GET["storage_position"]);
				$created_at				= $f->input("created_at",@$_GET["created_at"],"type='date'");
                
			?>
			     <?=$t->row(array("WCC No",$wcc_no));?>
                 <?=$t->row(array("PO No",$po_no));?>
                 <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
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
	if(@$_GET["wcc_no"]!="") 				$whereclause .= "(wcc_no LIKE '%".$_GET["wcc_no"]."%') AND ";
	if(@$_GET["po_no"]!="") 				$whereclause .= "(po_no LIKE '%".$_GET["po_no"]."%') AND ";
	if(@$_GET["storage_position"]!="") 		$whereclause .= "(storage_position LIKE '%".$_GET["storage_position"]."%') AND ";
    if(@$_GET["created_at"]!="") 			$whereclause .= "(created_at LIKE '".$_GET["created_at"]."') AND ";
   	
	$db->addtable("wcc");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("wcc");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$wccs = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='wcc_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('wcc_no');\">WCC No</div>",
						"<div onclick=\"sorting('po_no');\">PO No</div>",
						"<div onclick=\"sorting('storage_position');\">Contract Storage Position/Code</div>",
						"Softcopy File",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						""));?>
	<?php foreach($wccs as $no => $wcc){ ?>
		<?php
			$actions = "<a href=\"wcc_edit.php?id=".$wcc["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$wcc["id"]."';}\">Delete</a>
						";
                        
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"wcc_edit.php?id=".$wcc["id"]."\">".$wcc["id"]."</a>",
						"<a href=\"wcc_edit.php?id=".$wcc["id"]."\">".$wcc["wcc_no"]."</a>",
                        "<a href=\"wcc_edit.php?id=".$wcc["id"]."\">".$wcc["po_no"]."</a>",
                        $wcc["storage_position"],
                        "<a href=\"files_wcc/".$wcc["softcopy"]."\" target=\"_BLANK\">".$wcc["softcopy"]."</a>",
                        format_tanggal($wcc["created_at"],"d M Y"),
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>