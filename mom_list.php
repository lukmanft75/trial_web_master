<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("mom_documents");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">MOM Documents</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$name 					= $f->input("name",@$_GET["name"]);
				$storage_position 		= $f->input("storage_position",@$_GET["storage_position"]);
				$received_by_indohr_at	= $f->input("received_by_indohr_at",@$_GET["received_by_indohr_at"],"type='date'");
				$created_at				= $f->input("created_at",@$_GET["created_at"],"type='date'");
                
			?>
			     <?=$t->row(array("Document Name",$name));?>
                 <?=$t->row(array("Document Storage Position/Code",$storage_position));?>
                 <?=$t->row(array("Received At",$received_by_indohr_at));?>
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
	if(@$_GET["name"]!="") 					$whereclause .= "(name LIKE '%".$_GET["name"]."%') AND ";
	if(@$_GET["storage_position"]!="") 		$whereclause .= "(storage_position LIKE '%".$_GET["storage_position"]."%') AND ";
    if(@$_GET["received_by_indohr_at"]!="") $whereclause .= "(received_by_indohr_at LIKE '".$_GET["received_by_indohr_at"]."') AND ";
    if(@$_GET["created_at"]!="") 			$whereclause .= "(created_at LIKE '".$_GET["created_at"]."') AND ";
   	
	$db->addtable("mom_documents");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("mom_documents");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$mom_documents = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='mom_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('storage_position');\">Document Storage Position/Code</div>",
						"Softcopy File",
						"<div onclick=\"sorting('received_by_indohr_at');\">Received At</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						""));?>
	<?php foreach($mom_documents as $no => $mom_document){ ?>
		<?php
			$actions = "<a href=\"mom_edit.php?id=".$mom_document["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$mom_document["id"]."';}\">Delete</a>
						";
                        
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"mom_edit.php?id=".$mom_document["id"]."\">".$mom_document["id"]."</a>",
                        "<a href=\"mom_edit.php?id=".$mom_document["id"]."\">".$mom_document["name"]."</a>",
                        $mom_document["storage_position"],
                        "<a href=\"files_mom_indotech/".$mom_document["softcopy"]."\" target=\"_BLANK\">".$mom_document["softcopy"]."</a>",
                        format_tanggal($mom_document["received_by_indohr_at"],"d M Y"),
                        format_tanggal($mom_document["created_at"],"d M Y"),
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>