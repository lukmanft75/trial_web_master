<?php include_once "head.php";?>
<div class="bo_title">Trainings Schedule</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_date = $f->input("txt_date",@$_GET["txt_date"],"type='date' style='width:150px;'");
				$txt_topic = $f->input("txt_topic",@$_GET["txt_topic"]);
				$txt_trainer = $f->input("txt_trainer",@$_GET["txt_trainer"]);
			?>
			<?=$t->row(array("Event Date",$txt_date));?>
			<?=$t->row(array("Topic",$txt_topic));?>
			<?=$t->row(array("Trainer",$txt_trainer));?>
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
	if(@$_GET["txt_date"]!="") $whereclause .= "date(start_date) <= '".$_GET["txt_date"]."' AND date(end_date) >= '".$_GET["txt_date"]."'";
	if(@$_GET["txt_topic"]!="") $whereclause .= "topic LIKE '"."%".str_replace(" ","%",$_GET["txt_topic"])."%"."' AND ";
	if(@$_GET["txt_trainer"]!="") $whereclause .= "trainer LIKE '"."%".str_replace(" ","%",$_GET["txt_trainer"])."%"."' AND ";
	
	$db->addtable("trainings");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("trainings");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$trainings = $db->fetch_data(true);
?>
	<?=$f->input("add","Add","type='button' onclick=\"window.location='trainings_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('start_date');\">Event Date</div>",
						"<div onclick=\"sorting('topic');\">Topic</div>",
						"<div onclick=\"sorting('trainer');\">Trainer</div>",
						"<div onclick=\"sorting('earlybird');\">Early Bird</div>",
						"<div onclick=\"sorting('quota');\">Quota</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						""));?>
	<?php foreach($trainings as $no => $training){ ?>
		<?php
			$actions = "<a href=\"trainings_edit.php?id=".$training["id"]."\">Edit</a>";
		?>
		<?=$t->row(
					array("<a href=\"trainings_edit.php?id=".$training["id"]."\">".($no+$start+1)."</a>",
						"<a href=\"trainings_edit.php?id=".$training["id"]."\">".format_tanggal($training["start_date"],"dMY")." - ".format_tanggal($training["end_date"],"dMY")."</a>",
						"<a href=\"trainings_edit.php?id=".$training["id"]."\">".$training["topic"]."</a>",
						"<a href=\"trainings_edit.php?id=".$training["id"]."\">".$training["trainer"]."</a>",
						number_format($training["earlybird"],0,",","."),
						$training["quota"],
						substr($training["description"],0,100),
						$actions),
					array("align='right' valign='top'","","","","align='right' valign='top'","align='right' valign='top'")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
	
<?php include_once "footer.php";?>