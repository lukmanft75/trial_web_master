<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("cost_centers");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Cost Centers</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$name = $f->input("name",@$_GET["name"]);
				$projects = $f->select("project",$db->fetch_select_data("indottech_projects","id","name",[],["id"],"",true),@$_GET["project"],"style='height:20px;'");
				$scopes = $f->select("scope",$db->fetch_select_data("indottech_scopes","id","name",[],["id"],"",true),@$_GET["scope"],"style='height:20px;'");
				$regions = $f->select("region",$db->fetch_select_data("indottech_regions","id","name",[],["id"],"",true),@$_GET["region"],"style='height:20px;'");
                
			?>
                 <?=$t->row(array("Name",$name));?>
                 <?=$t->row(array("Project",$projects));?>
                 <?=$t->row(array("Scopes",$scopes));?>
                 <?=$t->row(array("Regions",$regions));?>
           
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
	if(@$_GET["project"]!="") $whereclause .= "(project_id = '".$_GET["project"]."') AND ";
	if(@$_GET["scope"]!="") $whereclause .= "(scope_id = '".$_GET["scope"]."') AND ";
	if(@$_GET["region"]!="") $whereclause .= "(region_id = '".$_GET["region"]."') AND ";
   	
	$db->addtable("cost_centers");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("cost_centers");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$cost_centers = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='cost_centers_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('code');\">Code</div>",
						"<div onclick=\"sorting('departement');\">Departement</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('project_id');\">Project</div>",
						"<div onclick=\"sorting('scope_id');\">Scope</div>",
						"<div onclick=\"sorting('region_id');\">Region</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($cost_centers as $no => $cost_center){ ?>
		<?php
			$actions = "<a href=\"cost_centers_edit.php?id=".$cost_center["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$cost_center["id"]."';}\">Delete</a>
						";
            $regions = "";
			foreach(pipetoarray($cost_center["region_ids"]) as $region_id){ $regions .= $db->fetch_single_data("indottech_regions","name",["id"=>$region_id]).","; }
			$regions = substr($regions,0,-1);
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"cost_centers_edit.php?id=".$cost_center["id"]."\">".$cost_center["id"]."</a>",
                        $cost_center["code"],
                        $cost_center["departement"],
                        $cost_center["name"],
						$db->fetch_single_data("indottech_projects","name",["id"=>$cost_center["project_id"]]),
						$db->fetch_single_data("indottech_scopes","name",["id"=>$cost_center["scope_id"]]),
						$regions,
						format_tanggal($cost_center["created_at"],"dMY"),
						$cost_center["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>