<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("finance_report_template");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		
		$db->addtable("finance_report_template_detail");
		$db->where("finance_report_template_id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Finance Report Templates</div>
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
   	
	$db->addtable("finance_report_template");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("finance_report_template");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$finance_report_templates = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='finance_report_template_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($finance_report_templates as $no => $finance_report_template){ ?>
		<?php
			$actions = "<a href=\"finance_report_template_edit.php?id=".$finance_report_template["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$finance_report_template["id"]."';}\">Delete</a>
						";
            
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"finance_report_template_edit.php?id=".$finance_report_template["id"]."\">".$finance_report_template["id"]."</a>",
                        $finance_report_template["name"],
						format_tanggal($finance_report_template["created_at"],"dMY"),
						$finance_report_template["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>