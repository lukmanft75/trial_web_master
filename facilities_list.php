<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("facilities");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Facilities</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_name = $f->select("txt_name",$db->fetch_select_data("facilities","concat(name) as id","name",array(),array(),"",true),@$_GET["txt_name"],"style='height:25px'");
				$category = $f->select("category",$db->fetch_select_data("facilities","concat(category) as id","category",array(),array(),"",true),@$_GET["category"],"style='height:25px'");
			?>
			<?=$t->row(array("Facility Name",$txt_name));?>
			<?=$t->row(array("Category",$category));?>
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
	if(@$_GET["txt_name"]!="") $whereclause .= "name LIKE '"."%".str_replace(" ","%",$_GET["txt_name"])."%"."' AND ";
	if(@$_GET["category"]!="") $whereclause .= "category LIKE '"."%".str_replace(" ","%",$_GET["category"])."%"."' AND ";
	
	$db->addtable("facilities");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("facilities");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$facilities = $db->fetch_data(true);
?>
	<?=$f->input("add","Add","type='button' onclick=\"window.location='facilities_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('category');\">Categories</div>",
						"<div onclick=\"sorting('name');\">Facilities Name</div>",
						""));?>
	<?php foreach($facilities as $no => $facility){ ?>
		<?php
			
			$actions = 	"<a href=\"facilities_edit.php?id=".$facility["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$facility["id"]."';}\">Delete</a>";
		?>
		<?=$t->row(
					array($no+$start+1,
					"<a href=\"facilities_edit.php?id=".$facility["id"]."\">".$facility["category"]."</a>",
					"<a href=\"facilities_edit.php?id=".$facility["id"]."\">".$facility["name"]."</a>",
					$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
	
<?php include_once "footer.php";?>