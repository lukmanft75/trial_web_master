<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("pph23");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">PPh 23</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$client_id 				= $f->select("client_id",$db->fetch_select_data("clients","id","name",array(),array("name"),"",true),@$_GET["client_id"],"style='height:25px;width:400px;'");
				$no_invoices			= $f->input("no_invoices",@$_GET["no_invoices"]);
				$tgl_potong				= $f->input("tgl_potong1",@$_GET["tgl_potong1"],"type='date' style='width:150px;'");
				$tgl_potong				.= " - ".$f->input("tgl_potong2",@$_GET["tgl_potong2"],"type='date' style='width:150px;'");
				$tahun_potong			= $f->input("tahun_potong",@$_GET["tahun_potong"],"type='number' style='width:80px;'");
				$no_potong				= $f->input("no_potong",@$_GET["no_potong"]);
				$created_at				= $f->input("created_at",@$_GET["created_at"],"type='date'");
                
			?>
			     <?=$t->row(array("Client",$client_id));?>
                 <?=$t->row(array("Invoice No",$no_invoices));?>
                 <?=$t->row(array("Tgl Bukti Potong",$tgl_potong));?>
                 <?=$t->row(array("Tahun Bukti Potong",$tahun_potong));?>
                 <?=$t->row(array("No Bukti Potong",$no_potong));?>
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
	if(@$_GET["client_id"]!="") 			$whereclause .= "client_id = '".$_GET["client_id"]."' AND ";
	if(@$_GET["no_invoices"]!="") 			$whereclause .= "no_invoices LIKE '%".$_GET["no_invoices"]."%' AND ";
    if(@$_GET["tgl_potong1"]!="") 			$whereclause .= "tgl_potong >= '".$_GET["tgl_potong1"]."' AND ";
    if(@$_GET["tgl_potong2"]!="") 			$whereclause .= "tgl_potong <= '".$_GET["tgl_potong2"]."' AND ";
    if(@$_GET["tahun_potong"]!="") 			$whereclause .= "tgl_potong LIKE '".$_GET["tahun_potong"]."%' AND ";
	if(@$_GET["no_potong"]!="") 			$whereclause .= "no_potong LIKE '%".$_GET["no_potong"]."%' AND ";
    if(@$_GET["created_at"]!="") 			$whereclause .= "created_at LIKE '".$_GET["created_at"]."' AND ";
   	
	$db->addtable("pph23");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	if($whereclause != "") $TOTAL = $db->fetch_all_data("pph23",["concat(sum(nominal)) as total"],substr($whereclause,0,-4))[0][0];
	else $TOTAL = $db->fetch_all_data("pph23",["concat(sum(nominal)) as total"])[0][0];
	
	$db->addtable("pph23");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$pph23s = $db->fetch_data(true);
	$total = 0;
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='pph23_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('client_id');\">Client</div>",
						"<div onclick=\"sorting('no_invoices');\">Invoices No</div>",
						"<div onclick=\"sorting('tgl_potong');\">Tgl Bukti Potong</div>",
						"<div onclick=\"sorting('no_potong');\">No Bukti Potong</div>",
						"<div onclick=\"sorting('nominal');\">Nominal</div>",
						"Softcopy File",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						""));?>
	<?php foreach($pph23s as $no => $pph23){ ?>
		<?php
			$actions = "<a href=\"pph23_edit.php?id=".$pph23["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$pph23["id"]."';}\">Delete</a>
						";
			$total += $pph23["nominal"];
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"pph23_edit.php?id=".$pph23["id"]."\">".$pph23["id"]."</a>",
						"<a href=\"pph23_edit.php?id=".$pph23["id"]."\">".$db->fetch_single_data("clients","name",array("id"=>$pph23["client_id"]))."</a>",
                        "<a href=\"pph23_edit.php?id=".$pph23["id"]."\">".substr($pph23["no_invoices"],0,50)."</a>",
                        format_tanggal($pph23["tgl_potong"],"d M Y"),
						$pph23["no_potong"],
						format_amount($pph23["nominal"]),
                        "<a href=\"files_pph23/".$pph23["softcopy"]."\" target=\"_BLANK\">".$pph23["softcopy"]."</a>",
                        format_tanggal($pph23["created_at"],"d M Y"),
						$actions),
					array("align='right' valign='top'",
							"valign='top'",
							"valign='top'",
							"valign='top'",
							"valign='top'",
							"valign='top'",
							"align='right' valign='top'",
							"valign='top'",""
						)
				);?>
	<?php } ?>
		<?php if($maxrow > $_rowperpage){ ?>
			<?=$t->row(["<b>Total per page</b>","<b>".format_amount($total)."</b>","","",""],["align='center' colspan='6' valign='top'","align='right' valign='top'",""]); ?>
		<?php } ?>
		<?=$t->row(["<b>Grand Total</b>","<b>".format_amount($TOTAL)."</b>","","",""],["align='center' colspan='6' valign='top'","align='right' valign='top'",""]); ?>
	
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>