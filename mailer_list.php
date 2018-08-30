<?php include_once "head.php";?>
<div class="bo_title">Mailer</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_subject = $f->input("txt_subject",@$_GET["txt_subject"]);
				$txt_body = $f->input("txt_body",@$_GET["txt_body"]);
				$arrstatus = array("0"=>"Unsend","1"=>"Progress","2"=>"Sent");
				$sel_status = $f->select("sel_status",$arrstatus,@$_GET["sel_status"],"style='height:20px;'");
			?>
			<?=$t->row(array("Subject",$txt_subject));?>
			<?=$t->row(array("Body",$txt_body));?>
			<?=$t->row(array("Status",$sel_status));?>
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
	if(@$_GET["txt_subject"]!="") $whereclause .= "subject LIKE '"."%".str_replace(" ","%",@$_GET["txt_subject"])."%"."' AND ";
	if(@$_GET["txt_body"]!="") $whereclause .= "body LIKE '"."%".str_replace(" ","%",@$_GET["txt_body"])."%"."' AND ";
	if(@$_GET["sel_status"]!="") $whereclause .= "status = '".@$_GET["sel_status"]."' AND ";
	
	$db->addtable("mailer");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("mailer");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order(@$_GET["sort"]);
	$mailers = $db->fetch_data(true);
?>
	<?=$f->input("add","Add","type='button' onclick=\"window.location='mailer_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('subject');\">Subject</div>",
						"<div onclick=\"sorting('isdebug');\">Is Debug</div>",
						"<div onclick=\"sorting('exec_time');\">Execute Time</div>",
						"<div onclick=\"sorting('xtimestamp');\">Timestamp</div>",
						"<div onclick=\"sorting('progressed');\">Progressed</div>",
						"<div onclick=\"sorting('status');\">Status</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						"<div onclick=\"sorting('updated_at');\">Updated At</div>",
						"<div onclick=\"sorting('updated_by');\">Updated By</div>",
						""));?>
	<?php if(count($mailers) > 0) { ?>
		<?php foreach($mailers as $no => $mailer){ ?>
			<?php
				$actions = "<a href=\"mailer_view.php?id=".$mailer["id"]."\">View</a> | 
							<a href=\"mailer_edit.php?id=".$mailer["id"]."\">Edit</a>
							";
			?>
			<?php
				if($mailer["status"]==0) $status = "Unsend";
				if($mailer["status"]==1) $status = "Progress";
				if($mailer["status"]==2) $status = "Sent";
			
				if($mailer["isdebug"]==0) $isdebug = "Ya";
				if($mailer["isdebug"]==1) $isdebug = "Tidak";
				$mailer["progressed"] = explode("|",$mailer["progressed"]);
				$mailer["progressed"] = $mailer["progressed"][0];
			?>
			<?=$t->row(
						array(
							$no+$start+1,
							"<a href=\"mailer_view.php?id=".$mailer["id"]."\">".$mailer["subject"]."</a>",
							$isdebug,
							format_tanggal($mailer["exec_time"],"dmY",true),
							format_tanggal($mailer["xtimestamp"],"dmY",true),
							$mailer["progressed"],
							$status,
							format_tanggal($mailer["created_at"],"dmY",true),
							$mailer["created_by"],
							format_tanggal($mailer["updated_at"],"dmY",true),
							$mailer["updated_by"],
							$actions
						),
						array("align='right' valign='top'","")
					);?>
		<?php } ?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
	
<?php include_once "footer.php";?>