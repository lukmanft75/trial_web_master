<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("day_off");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Hari Libur dan Cuti Bersama</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_tanggal = $f->input("txt_tanggal",@$_GET["txt_tanggal"],"type='date'");
			?>
			<?=$t->row(array("tanggal",$txt_tanggal));?>
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
	if(@$_GET["txt_tanggal"]!="") $whereclause .= "(tanggal LIKE '".$_GET["txt_tanggal"]."') AND ";
	
	$db->addtable("day_off");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("day_off");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$day_offs = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='day_off_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('tanggal');\">Tanggal</div>",
						"<div onclick=\"sorting('keterangan');\">Keterangan</div>",
						"<div onclick=\"sorting('is_leave');\">Cuti Bersama</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($day_offs as $no => $day_off){ ?>
		<?php
			$actions = "<a href=\"day_off_edit.php?id=".$day_off["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$day_off["id"]."';}\">Delete</a>
						";
		?>
		<?=$t->row(
					[$no+$start+1,
					 format_tanggal($day_off["tanggal"],"dMY"),
					 $day_off["keterangan"],
					 ($day_off["is_leave"] == 1) ? "Ya":"Bukan",
					 format_tanggal($day_off["created_at"],"dMY"),
					 $day_off["created_by"],
					 $actions],
					["align='right' valign='top'",""]
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>