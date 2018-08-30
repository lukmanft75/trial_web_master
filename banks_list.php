<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("banks");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Banks</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
                $txt_code = $f->input("txt_code",@$_GET["txt_code"]);
                $sel_coa = $f->select("sel_coa",$db->fetch_select_data("coa","coa","concat(coa,' - ',description) as coa_desc"),@$_GET["sel_coa"],"style='height:20px;'");
				$txt_name = $f->input("txt_name",@$_GET["txt_name"]);
                $txt_rek = $f->input("txt_rek",@$_GET["txt_rek"]);
                $sel_currency_id = $f->select("sel_currency_id",$db->fetch_select_data("currencies","id","concat(id) as id2",array(),array(),"",true),$_GET["sel_currency_id"],"style='height:20px;'");
			?>
            <?=$t->row(array("Code",$txt_code));?>
            <?=$t->row(array("COA",$sel_coa));?>
			<?=$t->row(array("Nama Bank",$txt_name));?>
            <?=$t->row(array("No. Rek",$txt_rek));?>
            <?=$t->row(array("Currency",$sel_currency_id));?>
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
	if(@$_GET["txt_code"]!="") $whereclause .= "(code LIKE '%".$_GET["txt_code"]."%') AND ";
	if(@$_GET["sel_coa"]!="") $whereclause .= "(coa LIKE '%".$_GET["sel_coa"]."%') AND ";
	if(@$_GET["txt_name"]!="") $whereclause .= "(name LIKE '%".$_GET["txt_name"]."%') AND ";
    if(@$_GET["txt_rek"]!="") $whereclause .= "(no_rek LIKE '%".$_GET["txt_rek"]."%') AND ";
    if(@$_GET["sel_currency_id"]!="") $whereclause .= "(currency_id ='".$_GET["sel_currency_id"]."') AND ";
	
	$db->addtable("banks");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("banks");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$banks = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='banks_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('code');\">Code</div>",
						"<div onclick=\"sorting('coa');\">COA</div>",
                        "<div onclick=\"sorting('name');\">Name</div>",
                        "<div onclick=\"sorting('no_rek');\">No Rek</div>",
						"<div onclick=\"sorting('currency_id');\">Currency ID</div>",
						"<div onclick=\"sorting('kurs');\">Kurs</div>",
						"<div onclick=\"sorting('is_debt');\">Is Debt</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($banks as $no => $bank){ ?>
		<?php
			$actions = "<a href=\"banks_edit.php?id=".$bank["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$bank["id"]."';}\">Delete</a>
						";
			$bank["is_debt"] = ($bank["is_debt"] == 1)?"Yes":"No";
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"banks_view.php?id=".$bank["id"]."\">".$bank["id"]."</a>",
						$bank["code"],
						$bank["coa"]." -- ".$db->fetch_single_data("coa","description",["coa" => $bank["coa"]]),
						$bank["name"],
                        $bank["no_rek"],
                        $bank["currency_id"],
                        format_amount($bank["kurs"]),
                        $bank["is_debt"],
                        $bank["description"],
						format_tanggal($bank["created_at"],"dMY"),
						$bank["created_by"],
						$actions),
					array("align='right' valign='top'","","","","","","align='right'")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>