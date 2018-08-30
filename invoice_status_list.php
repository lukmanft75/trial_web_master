<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("invoice_status");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Invoice Status</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$name = $f->input("name",@$_GET["name"]);
                
			?>
                 <?=$t->row(array("Name",$name));?>
           
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
	if(@$_GET["name"]!="") $whereclause .= "(name LIKE '%".$_GET["name"]."%') AND ";
   	
	$db->addtable("invoice_status");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("invoice_status");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$invoice_status = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='invoice_status_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($invoice_status as $no => $invoice_state){ ?>
		<?php
			$actions = "<a href=\"invoice_status_view.php?id=".$invoice_state["id"]."\">View</a> | 
						<a href=\"invoice_status_edit.php?id=".$invoice_state["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$invoice_state["id"]."';}\">Delete</a>
						";
            
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"invoice_state_view.php?id=".$invoice_state["id"]."\">".$invoice_state["id"]."</a>",
                        $invoice_state["name"],
						format_tanggal($invoice_state["created_at"],"dMY"),
						$invoice_state["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>