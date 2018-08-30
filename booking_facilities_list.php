<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$_created_by = $db->fetch_single_data("booking_facilities","created_by",array("id" => $_GET["deleting"]));
		if($_created_by == $__username || $__user_id == 1){
			$db->addtable("booking_facilities");
			$db->where("id",$_GET["deleting"]);
			$db->delete_();
			?> <script> window.location="?";</script> <?php
		} else {
				javascript("alert('You cannot delete this data, please ask $_created_by!');");
		}
	}
?>
<div class="bo_title">Booking Facilities</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$user = $f->select("user",$db->fetch_select_data("users","id","name",array(),array(),"",true),@$_GET["user"],"style='height:25px'");
				$facility = $f->select("facility",$db->fetch_select_data("facilities","id","name",array(),array(),"",true),@$_GET["facility"],"style='height:25px'");
				$category = $f->select("category",$db->fetch_select_data("facilities","concat(category) as id","category",array(),array(),"",true),@$_GET["category"],"style='height:25px'");
                $start_at = $f->input("start_at",@$_GET["start_at"],"type='datetime-local'");
				$end_at = $f->input("end_at",@$_GET["end_at"],"type='datetime-local'");
				$txt_description = $f->input("txt_description",@$_GET["txt_description"]);
			?>
			<?=$t->row(array("User Name",$user));?>
			<?=$t->row(array("Facility Name",$facility));?>
			<?=$t->row(array("Category",$category));?>
			<?=$t->row(array("Start",$start_at));?>
			<?=$t->row(array("End",$end_at));?>
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
	if(@$_GET["user"]!="") $whereclause .= "user_id = '".$_GET["user"]."' AND ";
	if(@$_GET["facility"]!="") $whereclause .= "facility_id = '".$_GET["facility"]."' AND ";
	if(@$_GET["category"]!="") $whereclause .= "facility_id IN (SELECT id FROM facilities WHERE category LIKE '".$_GET["category"]."') AND ";
    if(@$_GET["start_at"]!="") $whereclause .= "(start_at = '".$_GET["start_at"]."') AND ";
    if(@$_GET["end_at"]!="") $whereclause .= "(end_at = '".$_GET["end_at"]."') AND ";
	if(@$_GET["txt_description"]!="") $whereclause .= "description LIKE '"."%".str_replace(" ","%",$_GET["txt_description"])."%"."' AND ";
	
	$db->addtable("booking_facilities");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("booking_facilities");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$booking_facilities = $db->fetch_data(true);
?>
	<?=$f->input("add","Add","type='button' onclick=\"window.location='booking_facilities_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('user_id');\">User Name</div>",
						"<div onclick=\"sorting('facility_id');\">Facility Name</div>",
						"Category",
						"<div onclick=\"sorting('start_at');\">Start</div>",
						"<div onclick=\"sorting('end_at');\">End</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						""));?>
	<?php foreach($booking_facilities as $booking_facility){ ?>
		<?php
			
			$actions ="<a href=\"booking_facilities_edit.php?id=".$booking_facility["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$booking_facility["id"]."';}\">Delete</a>";
			
			$user_id = $db->fetch_single_data("users","name",array("id"=>$booking_facility["user_id"]));
			$facility_id = $db->fetch_single_data("facilities","name",array("id"=>$booking_facility["facility_id"]));
			$category = $db->fetch_single_data("facilities","category",array("id"=>$booking_facility["facility_id"]));
			
		?>
		<?=$t->row(
					array($no+$start+1,"<a href=\"booking_facilities_edit.php?id=".$booking_facility["id"]."\">".$user_id."</a>",
					$facility_id,
					$category,
					$booking_facility["start_at"],
					$booking_facility["end_at"],
					$booking_facility["description"],
					$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
	
<?php include_once "footer.php";?>