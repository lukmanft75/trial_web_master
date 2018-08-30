<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("insurance_plan");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Insurance Plans</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$corp = $f->input("corp",@$_GET["corp"]);
				$plan = $f->input("plan",@$_GET["plan"]);
				$price = $f->input("price",@$_GET["price"]);
                
			?>
                 <?=$t->row(array("Insurance Coorporation",$corp));?>
                 <?=$t->row(array("Plan",$plan));?>
                 <?=$t->row(array("Price",$price));?>
           
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
	if(@$_GET["corp"]!="") $whereclause .= "(insurance_corp LIKE '%".$_GET["corp"]."%') AND ";
	if(@$_GET["plan"]!="") $whereclause .= "(plan LIKE '%".$_GET["plan"]."%') AND ";
	if(@$_GET["price"]!="") $whereclause .= "(price = '".$_GET["price"]."') AND ";
   	
	$db->addtable("insurance_plan");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("insurance_plan");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$insurance_plan = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='insurance_plan_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('insurance_corp');\">Insurance Coorporation</div>",
						"<div onclick=\"sorting('plan');\">Plan</div>",
						"<div onclick=\"sorting('price');\">Price</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($insurance_plan as $no => $insurance_plan_){ ?>
		<?php
			$actions = /* "<a href=\"insurance_plan_view.php?id=".$insurance_plan_["id"]."\">View</a> |  */
						"<a href=\"insurance_plan_edit.php?id=".$insurance_plan_["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$insurance_plan_["id"]."';}\">Delete</a>
						";
            
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"insurance_plan_view.php?id=".$insurance_plan_["id"]."\">".$insurance_plan_["id"]."</a>",
                        $insurance_plan_["insurance_corp"],
                        $insurance_plan_["plan"],
                        format_amount($insurance_plan_["price"]),
						format_tanggal($insurance_plan_["created_at"],"dMY"),
						$insurance_plan_["created_by"],
						$actions),
					array("align='right' valign='top'","","","","align='right'")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>