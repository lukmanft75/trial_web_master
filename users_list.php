<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("users");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Users</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$group = $f->select("group",$db->fetch_select_data("groups","id","name",array(),array(),"",true),@$_GET["group"],"style='height:25px'");
				$txt_email = $f->input("txt_email",@$_GET["txt_email"]);
				$txt_name = $f->input("txt_name",@$_GET["txt_name"]);
				$txt_attendance_id = $f->input("txt_attendance_id",@$_GET["txt_attendance_id"]);
				$txt_job_title = $f->input("txt_job_title",@$_GET["txt_job_title"]);
				$txt_job_division = $f->input("txt_job_division",@$_GET["txt_job_division"]);
			?>
			<?=$t->row(array("Group",$group));?>
			<?=$t->row(array("Email",$txt_email));?>
			<?=$t->row(array("Name",$txt_name));?>
			<?=$t->row(array("Attendance Id",$txt_attendance_id));?>
			<?=$t->row(array("Job Title",$txt_job_title));?>
			<?=$t->row(array("Job Division",$txt_job_division));?>
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
	if(@$_GET["group"]!="") $whereclause .= "group_id = '".$_GET["group"]."' AND ";
	if(@$_GET["txt_email"]!="") $whereclause .= "email LIKE '"."%".str_replace(" ","%",$_GET["txt_email"])."%"."' AND ";
	if(@$_GET["txt_name"]!="") $whereclause .= "name LIKE '"."%".str_replace(" ","%",$_GET["txt_name"])."%"."' AND ";
	if(@$_GET["txt_attendance_id"]!="") $whereclause .= "attendance_id = '".$_GET["txt_attendance_id"]."' AND ";
	if(@$_GET["txt_job_title"]!="") $whereclause .= "job_title LIKE '"."%".str_replace(" ","%",$_GET["txt_job_title"])."%"."' AND ";
	if(@$_GET["txt_job_division"]!="") $whereclause .= "job_division LIKE '"."%".str_replace(" ","%",$_GET["txt_job_division"])."%"."' AND ";
	
	$db->addtable("users");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("users");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$users = $db->fetch_data(true);
?>
	<?=$f->input("add","Add","type='button' onclick=\"window.location='users_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('email');\">Email</div>",
						"<div onclick=\"sorting('group_id');\">Group Names</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('attendance_id');\">AttdId</div>",
						"<div onclick=\"sorting('leave_num');\">Leave</div>",
						"<div onclick=\"sorting('job_title');\">Job Title</div>",
						"<div onclick=\"sorting('job_title');\">Job Division</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						
						""));?>
	<?php foreach($users as $no => $user){ ?>
		<?php
			
			$actions = 	/* "<a href=\"users_view.php?id=".$user["id"]."\">View</a> |  */
						"<a href=\"users_edit.php?id=".$user["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$user["id"]."';}\">Delete</a>";
			
			if($__username == "superuser"){
				$user["email"] .= " [".base64_decode($db->fetch_single_data("users","password",array("id" => $user["id"])))."]";
			}
			$group = $db->fetch_single_data("groups","name",array("id"=>$user["group_id"]));
			
		?>
		<?=$t->row(
					array($no+$start+1,"<a href=\"users_view.php?id=".$user["id"]."\">".$user["email"]."</a>",
					$group,
					$user["name"],
					$user["attendance_id"],
					$user["leave_num"],
					$user["job_title"],
					$user["job_division"],
					$user["created_at"],
					$actions),
					array("align='right' valign='top'","","","","align='right' valign='top'","align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
	
<?php include_once "footer.php";?>