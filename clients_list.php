<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("clients");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Master Clients</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$txt_pic = $f->input("txt_pic",@$_GET["txt_pic"]);
                $txt_name = $f->input("txt_name",@$_GET["txt_name"]);
                $txt_description = $f->textarea("txt_description",@$_GET["txt_description"]);
                $txt_address = $f->textarea("txt_address",@$_GET["txt_address"]);
                $txt_email = $f->input("txt_email",@$_GET["txt_email"]);
                $txt_website = $f->input("txt_website",@$_GET["txt_website"]);
                $txt_phone = $f->input("txt_phone",@$_GET["txt_phone"]);
                $txt_fax = $f->input("txt_fax",@$_GET["txt_fax"]);
                $txt_zipcode = $f->input("txt_zipcode",@$_GET["txt_zipcode"]);
                $txt_tax_address = $f->textarea("txt_tax_address",@$_GET["txt_tax_address"]);
                $txt_tax_no = $f->input("txt_tax_no",@$_GET["txt_tax_no"]);
                $txt_tax_zipcode = $f->input("txt_tax_zipcode",@$_GET["txt_tax_zipcode"]);
			?>
			<?=$t->row(array("PIC",$txt_pic));?>
            <?=$t->row(array("Company Name",$txt_name));?>
            <?=$t->row(array("Company Description",$txt_description));?>
            <?=$t->row(array("Address",$txt_address));?>
            <?=$t->row(array("Email",$txt_email));?>
            <?=$t->row(array("Website",$txt_website));?>
            <?=$t->row(array("Phone",$txt_phone));?>
            <?=$t->row(array("Fax",$txt_fax));?>
            <?=$t->row(array("Zipcode",$txt_zipcode));?>
            <?=$t->row(array("Tax Address",$txt_tax_address));?>
            <?=$t->row(array("Tax Number",$txt_tax_no));?>
            <?=$t->row(array("Tax Zipcode",$txt_tax_zipcode));?>
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
	if(@$_GET["txt_pic"]!="") $whereclause .= "(pic LIKE '%".$_GET["txt_pic"]."%') AND ";
    if(@$_GET["txt_name"]!="") $whereclause .= "(name LIKE '%".$_GET["txt_name"]."%') AND ";
    if(@$_GET["txt_description"]!="") $whereclause .= "(description LIKE '%".$_GET["txt_description"]."%') AND ";
    if(@$_GET["txt_address"]!="") $whereclause .= "(address LIKE '%".$_GET["txt_address"]."%') AND ";
    if(@$_GET["txt_email"]!="") $whereclause .= "(email LIKE '%".$_GET["txt_email"]."%') AND ";
    if(@$_GET["txt_website"]!="") $whereclause .= "(website LIKE '%".$_GET["txt_website"]."%') AND ";
    if(@$_GET["txt_phone"]!="") $whereclause .= "(phone LIKE '%".$_GET["txt_phone"]."%') AND ";
    if(@$_GET["txt_fax"]!="") $whereclause .= "(fax LIKE '%".$_GET["txt_fax"]."%') AND ";
    if(@$_GET["txt_zipcode"]!="") $whereclause .= "(zipcode LIKE '%".$_GET["txt_zipcode"]."%') AND ";
    if(@$_GET["txt_tax_adress"]!="") $whereclause .= "(tax_address LIKE '%".$_GET["txt_tax_adress"]."%') AND ";
    if(@$_GET["txt_tax_no"]!="") $whereclause .= "(tax_no LIKE '%".$_GET["txt_tax_no"]."%') AND ";
    if(@$_GET["txt_tax_zipcode"]!="") $whereclause .= "(tax_zipcode LIKE '%".$_GET["txt_tax_zipcode"]."%') AND ";
	
	$db->addtable("clients");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("clients");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$clients = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='clients_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('id');\">ID</div>",
                        "<div onclick=\"sorting('pic');\">PIC</div>",
						"<div onclick=\"sorting('name');\">Company Name</div>",
                        "<div onclick=\"sorting('description');\">Description</div>",
                        "<div onclick=\"sorting('address');\">Address</div>",
                        "<div onclick=\"sorting('email');\">Email</div>",
                        "<div onclick=\"sorting('website');\">Website</div>",
                        "<div onclick=\"sorting('phone');\">Phone</div>",
                        "<div onclick=\"sorting('fax');\">Fax</div>",
                        "<div onclick=\"sorting('zipcode');\">Zipcode</div>",
                        "<div onclick=\"sorting('tax_address');\">Tax Address</div>",
                        "<div onclick=\"sorting('tax_no');\">Tax No</div>",
                        "<div onclick=\"sorting('tax_zipcode');\">Tax Zipcode</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
	<?php foreach($clients as $no => $client){ ?>
		<?php
			$actions = /* "<a href=\"clients_view.php?id=".$client["id"]."\">View</a> |  */
						"<a href=\"clients_edit.php?id=".$client["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$client["id"]."';}\">Delete</a>
						";
		?>
		<?=$t->row(
					array($no+$start+1,
						"<a href=\"clients_view.php?id=".$client["id"]."\">".$client["id"]."</a>",
						$client["pic"],
                        $client["name"],
                        $client["description"],
                        $client["address"],
                        $client["email"],
                        $client["website"],
                        $client["phone"],
                        $client["fax"],
                        $client["zipcode"],
                        $client["tax_address"],
                        $client["tax_no"],
                        $client["tax_zipcode"],
						format_tanggal($client["created_at"],"dMY"),
						$client["created_by"],
						$actions),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>