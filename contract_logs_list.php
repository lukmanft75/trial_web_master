<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("contract_logs");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Contract Logs</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$name 					= $f->input("name",@$_GET["name"]);
				$storage_position 		= $f->input("storage_position",@$_GET["storage_position"]);
				$distributed_at 		= $f->input("distributed_at",@$_GET["distributed_at"],"type='date'");
				$signed_by_employee_at	= $f->input("signed_by_employee_at",@$_GET["signed_by_employee_at"],"type='date'");
				$received_by_indohr_at	= $f->input("received_by_indohr_at",@$_GET["received_by_indohr_at"],"type='date'");
				$created_at				= $f->input("created_at",@$_GET["created_at"],"type='date'");
                
			?>
			     <?=$t->row(array("Contract Name",$name));?>
                 <?=$t->row(array("Contract Storage Position/Code",$storage_position));?>
                 <?=$t->row(array("Distributed At",$distributed_at));?>
                 <?=$t->row(array("Signed by employee At",$signed_by_employee_at));?>
                 <?=$t->row(array("Received At",$received_by_indohr_at));?>
                 <?=$t->row(array("Created At",$created_at));?>
           
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
	if(@$_GET["name"]!="") 					$whereclause .= "(name LIKE '%".$_GET["name"]."%') AND ";
	if(@$_GET["storage_position"]!="") 		$whereclause .= "(storage_position LIKE '%".$_GET["storage_position"]."%') AND ";
    if(@$_GET["distributed_at"]!="") 		$whereclause .= "(distributed_at LIKE '".$_GET["distributed_at"]."') AND ";
    if(@$_GET["signed_by_employee_at"]!="") $whereclause .= "(signed_by_employee_at LIKE '".$_GET["signed_by_employee_at"]."') AND ";
    if(@$_GET["received_by_indohr_at"]!="") $whereclause .= "(received_by_indohr_at LIKE '".$_GET["received_by_indohr_at"]."') AND ";
    if(@$_GET["created_at"]!="") 			$whereclause .= "(created_at LIKE '".$_GET["created_at"]."') AND ";
   	
	$db->addtable("contract_logs");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("contract_logs");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$contract_logs = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='contract_logs_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('storage_position');\">Contract Storage Position/Code</div>",
						"Softcopy File",
						"<div onclick=\"sorting('distributed_at');\">Distributed At</div>",
						"<div onclick=\"sorting('signed_by_employee_at');\">Signed At</div>",
						"<div onclick=\"sorting('received_by_indohr_at');\">Received At</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						""));?>
	<?php foreach($contract_logs as $no => $contract_log){ ?>
		<?php
			$actions = "<a href=\"contract_logs_edit.php?id=".$contract_log["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$contract_log["id"]."';}\">Delete</a>
						";
                        
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"contract_logs_edit.php?id=".$contract_log["id"]."\">".$contract_log["id"]."</a>",
                        "<a href=\"contract_logs_edit.php?id=".$contract_log["id"]."\">".$contract_log["name"]."</a>",
                        $contract_log["storage_position"],
                        "<a href=\"files_contracts/".$contract_log["softcopy"]."\" target=\"_BLANK\">".$contract_log["softcopy"]."</a>",
                        format_tanggal($contract_log["distributed_at"],"d M Y"),
                        format_tanggal($contract_log["signed_by_employee_at"],"d M Y"),
                        format_tanggal($contract_log["received_by_indohr_at"],"d M Y"),
                        format_tanggal($contract_log["created_at"],"d M Y"),
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>