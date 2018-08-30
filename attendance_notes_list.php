<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("attendance_notes");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Catatan Kehadiran</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_tanggal = $f->input("txt_tanggal",@$_GET["txt_tanggal"],"type='date'");
				$sel_attendance_id = $f->select("sel_attendance_id",$db->fetch_select_data("users","attendance_id","name",["group_id" => "1,2,3,4,5,6,7,8,9,10,19:IN"],["name"],"",true),@$_GET["sel_attendance_id"],"style='height:25px'");
			?>
			<?=$t->row(array("tanggal",$txt_tanggal));?>
            <?=$t->row(array("Nama",$sel_attendance_id));?>
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
    if(@$_GET["sel_attendance_id"]!="") $whereclause .= "attendance_id = '".$_GET["sel_attendance_id"]."' AND ";
	
	$db->addtable("attendance_notes");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("attendance_notes");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$attendance_notes = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='attendance_notes_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
                        "<div onclick=\"sorting('attendance_id');\">Name</div>",
						"<div onclick=\"sorting('tanggal');\">Tanggal</div>",
						"<div onclick=\"sorting('notes');\">Keterangan</div>",
						"<div onclick=\"sorting('attended');\">Dianggap Hadir</div>",
						"<div onclick=\"sorting('surat_dokter');\">Ada Surat Dokter</div>",
						"<div onclick=\"sorting('cuti');\">Cuti</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($attendance_notes as $no => $attendance_note){ ?>
		<?php
			$actions = "<a href=\"attendance_notes_edit.php?id=".$attendance_note["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$attendance_note["id"]."';}\">Delete</a>
						";
			$name = $db->fetch_single_data("users","name",["attendance_id" => $attendance_note["attendance_id"]]);
		?>
		<?=$t->row(
					[$no+$start+1,
					 $name,
					 format_tanggal($attendance_note["tanggal"],"dMY"),
					 $attendance_note["notes"],
					 ($attendance_note["attended"] == "1") ? "Ya" : "Tidak",
					 ($attendance_note["surat_dokter"] == "1") ? "Ada" : "Tidak",
					 ($attendance_note["cuti"] == "1") ? "Cuti" : "&nbsp;",
					 format_tanggal($attendance_note["created_at"],"dMY"),
					 $attendance_note["created_by"],
					 $actions],
					["align='right' valign='top'",""]
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>