<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("currencies");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Currencies</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_id = $f->input("txt_id",@$_GET["txt_id"]);
				$txt_name = $f->input("txt_name",@$_GET["txt_name"]);
			?>
			<?=$t->row(array("Currency ID",$txt_id));?>
			<?=$t->row(array("Currency Name",$txt_name));?>
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
	if(@$_GET["txt_id"]!="") $whereclause .= "(id LIKE '%".$_GET["txt_id"]."%') AND ";
	if(@$_GET["txt_name"]!="") $whereclause .= "(name LIKE '%".$_GET["txt_name"]."%') AND ";
	
	$db->addtable("currencies");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("currencies");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$currencies = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='currencies_add.php';\"");?>
	<?=$f->input("history","Kurs History","type='button' onclick=\"window.location='currencies_history.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('kurs');\">Kurs</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($currencies as $no => $currency){ ?>
		<?php
			$actions = /* "<a href=\"currencies_view.php?id=".$currency["id"]."\">View</a> |  */
						"<a href=\"currencies_edit.php?id=".$currency["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$currency["id"]."';}\">Delete</a>
						";
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"currencies_view.php?id=".$currency["id"]."\">".$currency["id"]."</a>",
						$currency["name"],
                        format_amount($currency["kurs"]),
						format_tanggal($currency["created_at"],"dMY"),
						$currency["created_by"],
						$actions),
					array("align='right' valign='top'","","","align='right'")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>