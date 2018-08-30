<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("divisions");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Divisions</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_name = $f->input("txt_name",@$_GET["txt_name"]);
                $txt_description = $f->textarea("txt_description",@$_GET["txt_description"]);
			?>
			<?=$t->row(array("Division Name",$txt_name));?>
            <?=$t->row(array("Description",$txt_description));?>
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
	if(@$_GET["txt_name"]!="") $whereclause .= "(name LIKE '%".$_GET["txt_name"]."%') AND ";
    if(@$_GET["txt_description"]!="") $whereclause .= "(description LIKE '%".$_GET["txt_description"]."%') AND ";
	
	$db->addtable("divisions");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("divisions");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$divisions = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='divisions_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('name');\">Division Name</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($divisions as $no => $division){ ?>
		<?php
			$actions =/*  "<a href=\"divisions_view.php?id=".$division["id"]."\">View</a> |  */
						"<a href=\"divisions_edit.php?id=".$division["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$division["id"]."';}\">Delete</a>
						";
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"divisions_view.php?id=".$division["id"]."\">".$division["id"]."</a>",
						$division["name"],
                        $division["description"],
						format_tanggal($division["created_at"],"dMY"),
						$division["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>