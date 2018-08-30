<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("allowances");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Allowances</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$name = $f->input("name",@$_GET["name"]);
				$description = $f->input("description",@$_GET["description"]);
                
			?>
                 <?=$t->row(array("Name",$name));?>
                 <?=$t->row(array("Description",$description));?>
           
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
	if(@$_GET["description"]!="") $whereclause .= "(description LIKE '%".$_GET["description"]."%') AND ";
   	
	$db->addtable("allowances");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("allowances");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$allowances = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='allowances_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($allowances as $no => $allowance){ ?>
		<?php
			$actions = /* "<a href=\"allowances_view.php?id=".$allowance["id"]."\">View</a> |  */
						"<a href=\"allowances_edit.php?id=".$allowance["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$allowance["id"]."';}\">Delete</a>
						";
            
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"allowances_edit.php?id=".$allowance["id"]."\">".$allowance["id"]."</a>",
                        $allowance["name"],
                        $allowance["description"],
						format_tanggal($allowance["created_at"],"dMY"),
						$allowance["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>