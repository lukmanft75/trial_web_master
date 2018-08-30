<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("units");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Units</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_name = $f->input("txt_name",@$_GET["txt_name"]);
                $txt_description = $f->input("txt_description",@$_GET["txt_description"]);
			?>
			<?=$t->row(array("Unit Name",$txt_name));?>
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
	
	$db->addtable("units");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("units");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$units = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='units_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('name');\">Unit Name</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($units as $no => $unit){ ?>
		<?php
			$actions = /* "<a href=\"units_view.php?id=".$unit["id"]."\">View</a> |  */
						"<a href=\"units_edit.php?id=".$unit["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$unit["id"]."';}\">Delete</a>
						";
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"units_view.php?id=".$unit["id"]."\">".$unit["id"]."</a>",
						$unit["name"],
                        $unit["description"],
						format_tanggal($unit["created_at"],"dMY"),
						$unit["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>