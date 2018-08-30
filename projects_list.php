<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("projects");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Projects</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$name = $f->input("name",@$_GET["name"]);
                $client = $f->select("client",$db->fetch_select_data("clients","id","name",array(),array(),"",true),@$_GET["client"],"style='height:25px;'");
			?>
                 <?=$t->row(array("Name",$name));?>
                 <?=$t->row(array("Client",$client));?>
           
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
	if(@$_GET["client"]!="") $whereclause .= "client_id = '".$_GET["client"]."') AND ";
   	
	$db->addtable("projects");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("projects");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$projects = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='projects_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('client_id');\">Client</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($projects as $no => $project){ ?>
		<?php
			$actions = "<a href=\"projects_edit.php?id=".$project["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$project["id"]."';}\">Delete</a>
						";
            
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"projects_edit.php?id=".$project["id"]."\">".$project["id"]."</a>",
                        $project["name"],
                        $db->fetch_single_data("clients","name",array("id" => $project["client_id"])),
						format_tanggal($project["created_at"],"dMY"),
						$project["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>