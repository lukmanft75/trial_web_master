<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("coa");
		$db->where("coa",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master COA</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_coa = $f->input("txt_coa",@$_GET["txt_coa"]);
				$txt_prf_code = $f->input("txt_prf_code",@$_GET["txt_prf_code"]);
				$txt_parent = $f->input("txt_parent",@$_GET["txt_parent"]);
                $txt_description = $f->input("txt_description",@$_GET["txt_description"]);
			?>
			<?=$t->row(array("COA",$txt_coa));?>
			<?=$t->row(array("Parent",$txt_parent));?>
            <?=$t->row(array("Description",$txt_description));?>
			<?=$t->row(array("PRF CODE",$txt_prf_code));?>
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
	if(@$_GET["txt_coa"]!="") $whereclause .= "(coa LIKE '%".$_GET["txt_coa"]."%') AND ";
	if(@$_GET["txt_parent"]!="") $whereclause .= "(parent LIKE '%".$_GET["txt_parent"]."%') AND ";
    if(@$_GET["txt_description"]!="") $whereclause .= "(description LIKE '%".$_GET["txt_description"]."%') AND ";
	if(@$_GET["txt_prf_code"]!="") $whereclause .= "(prf_code LIKE '%".$_GET["txt_prf_code"]."%') AND ";
	
	$db->addtable("coa");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("coa");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$coas = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='coa_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('coa');\">COA</div>",
                        "<div onclick=\"sorting('description');\">Description</div>",
						"<div onclick=\"sorting('parent');\">Parent</div>",
						"<div onclick=\"sorting('prf_code');\">PRF CODE</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($coas as $no => $coa){ ?>
		<?php
			$actions = "<a href=\"coa_edit.php?coa=".$coa["coa"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$coa["coa"]."';}\">Delete</a>
						";
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"coa_edit.php?coa=".$coa["coa"]."\">".$coa["coa"]."</a>",
                        $coa["description"],
                        $coa["parent"]." ".$db->fetch_single_data("coa","description",array("coa"=>$coa["parent"])),
                        $coa["prf_code"],
						format_tanggal($coa["created_at"],"dMY"),
						$coa["created_by"],
						$actions),
					array("align='right' valign='top'","","","","","align='right'","align='right'")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>