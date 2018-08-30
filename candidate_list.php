<?php include_once "head.php";?>
<?php include_once "scripts/candidates_js.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("candidates");
		$db->where("id",$_GET["deleting"]);
		$db->delete_();
		?> <script> window.location="?";</script> <?php
	}
?>
<div class="bo_title">Candidates</div>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
				$code = $f->input("code",@$_GET["code"]);
				$name = $f->input("name",@$_GET["name"]);
				$birthdate = $f->input("birthdate",@$_GET["birthdate"],"type='date'");
				$sex = $f->select("sex",array(""=>"","M" => "M", "F" => "F"),@$_GET["sex"],"style='height:25px'");
				$status_id = $f->select("status_id",$db->fetch_select_data("statuses","id","name",array(),array(),"",true),@$_GET["status_id"],"style='height:25px'");
				$address = $f->textarea("address",@$_GET["address"]);
				$phone = $f->input("phone",@$_GET["phone"]);
				$bank_name = $f->input("bank_name",@$_GET["bank_name"]);
				$bank_account = $f->input("bank_account",@$_GET["bank_account"]);
				$ktp = $f->input("ktp",@$_GET["ktp"]);
				$npwp = $f->input("npwp",@$_GET["npwp"]);
				$email = $f->input("email",@$_GET["email"]);
				$attendance_id = $f->input("attendance_id",@$_GET["attendance_id"]);
				$join_indohr_at = $f->input("join_indohr_at",@$_GET["join_indohr_at"],"type='date'");
                
			?>
			     <?=$t->row(array("Code",$code));?>
                 <?=$t->row(array("Name",$name));?>
                 <?=$t->row(array("Birthdate",$birthdate));?>
                 <?=$t->row(array("Sex",$sex));?>
                 <?=$t->row(array("Status",$status_id));?>
                 <?=$t->row(array("Address",$address));?>
                 <?=$t->row(array("Phone",$phone));?>
                 <?=$t->row(array("Bank Name",$bank_name));?>
                 <?=$t->row(array("Bank Account",$bank_account));?>
                 <?=$t->row(array("KTP",$ktp));?>
                 <?=$t->row(array("NPWP",$npwp));?>
                 <?=$t->row(array("Email",$email));?>
                 <?=$t->row(array("Attendance Id",$attendance_id));?>
                 <?=$t->row(array("Join IndoHR At",$join_indohr_at));?>
           
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
    if(@$_GET["code"]!="") $whereclause .= "(code LIKE'%".$_GET["code"]."%') AND ";
	if(@$_GET["name"]!="") $whereclause .= "(name LIKE '%".$_GET["name"]."%') AND ";
    if(@$_GET["birthdate"]!="") $whereclause .= "(birthdate ='".$_GET["birthdate"]."') AND ";
    if(@$_GET["sex"]!="") $whereclause .= "(sex ='".$_GET["sex"]."') AND ";
    if(@$_GET["status_id"]!="") $whereclause .= "(status_id ='".$_GET["status_id"]."') AND ";
    if(@$_GET["address"]!="") $whereclause .= "(address LIKE '%".$_GET["address"]."%') AND ";
    if(@$_GET["phone"]!="") $whereclause .= "(phone LIKE '%".$_GET["phone"]."%') AND ";
    if(@$_GET["bank_name"]!="") $whereclause .= "(bank_name LIKE '%".$_GET["bank_name"]."%') AND ";
    if(@$_GET["bank_account"]!="") $whereclause .= "(bank_account LIKE '%".$_GET["bank_account"]."%') AND ";
    if(@$_GET["ktp"]!="") $whereclause .= "(ktp LIKE '%".$_GET["ktp"]."%') AND ";
    if(@$_GET["npwp"]!="") $whereclause .= "(npwp LIKE '%".$_GET["npwp"]."%') AND ";
    if(@$_GET["email"]!="") $whereclause .= "(email LIKE '%".$_GET["email"]."%') AND ";
    if(@$_GET["attendance_id"]!="") $whereclause .= "(attendance_id = '".$_GET["attendance_id"]."') AND ";
    if(@$_GET["join_indohr_at"]!="") $whereclause .= "(join_indohr_at LIKE '".$_GET["join_indohr_at"]."') AND ";
   	
	$db->addtable("candidates");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("candidates");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$candidates = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='candidate_add.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"",
						"<div onclick=\"sorting('id');\">ID</div>",
						"<div onclick=\"sorting('code');\">Code</div>",
						"<div onclick=\"sorting('name');\">Name</div>",
						"<div onclick=\"sorting('birthdate');\">Birthdate</div>",
						"<div onclick=\"sorting('sex');\">Sex</div>",
						"<div onclick=\"sorting('status_id');\">Status</div>",
						"<div onclick=\"sorting('address');\">Address</div>",
						"<div onclick=\"sorting('phone');\">Phone</div>",
						"<div onclick=\"sorting('bank');\">Bank</div>",
						"<div onclick=\"sorting('bank_account');\">Bank Account</div>",
						"<div onclick=\"sorting('ktp');\">KTP</div>",
						"<div onclick=\"sorting('npwp');\">NPWP</div>",
						"<div onclick=\"sorting('bpjs_kesehatan');\">BPJS kesehatan</div>",
						"<div onclick=\"sorting('bpjs_ketenagakerjaan');\">BPJS ketenagakerjaan</div>",
                        "<div onclick=\"sorting('email');\">Email</div>",
                        "<div onclick=\"sorting('attendance_id');\">Attendance Id</div>",
                        "<div onclick=\"sorting('join_indohr_at');\">Join IndoHR At</div>",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>"));?>
	<?php foreach($candidates as $no => $candidate){ ?>
		<?php
			$actions = "<a href=\"candidate_view.php?id=".$candidate["id"]."\">View</a> | 
						<a href=\"candidate_edit.php?id=".$candidate["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$candidate["id"]."';}\">Delete</a>
						";
                        
			$status = $db->fetch_single_data("statuses","name",array("id"=>$candidate["status_id"]));
            $bpjskesehatan = $db->fetch_single_data("bpjs","bpjs_id",array("candidate_id"=>$candidate["id"],"bpjs_type" => '1',"pisa" => "peserta"));
			$bpjskesehatan .= "<img src='icons/search_window.png' style='float:right;position:relative' onclick='$.fancybox.open({ href: \"sub_window/win_bpjs_list.php?candidate_id=".$candidate["id"]."&bpjs_type=1&pisa=peserta\", height: \"80%\", type: \"iframe\" });'>";
			$bpjsketenagakerjaan = $db->fetch_single_data("bpjs","bpjs_id",array("candidate_id"=>$candidate["id"],"bpjs_type" => '2',"pisa" => "peserta"));
			$bpjsketenagakerjaan .= "<img src='icons/search_window.png' style='float:right;position:relative' onclick='$.fancybox.open({ href: \"sub_window/win_bpjs_list.php?candidate_id=".$candidate["id"]."&bpjs_type=2&pisa=peserta\", height: \"80%\", type: \"iframe\" });'>";
			if($candidate["code"] == ""){
				$candidate["code"] = "<div id='candidate_code_".$candidate["id"]."'>".$f->input("btn_generate_code","Generate","type='button' onclick=\"generate_code('".$candidate["id"]."','candidate_code_".$candidate["id"]."');\"")."</div>";
			} else {
				$candidate["code"] = "<a href=\"candidate_view.php?id=".$candidate["id"]."\">".$candidate["code"]."</a>";
			}
		?>
		<?=$t->row(
					array($no+$start+1,
						$actions,
						"<a href=\"candidate_view.php?id=".$candidate["id"]."\">".$candidate["id"]."</a>",
										$candidate["code"],
                        "<a href=\"candidate_view.php?id=".$candidate["id"]."\">".$candidate["name"]."</a>",
                        $candidate["birthdate"],
                        $candidate["sex"],
                        $status,
                        $candidate["address"],
                        $candidate["phone"],
                        $candidate["bank_name"],
                        $candidate["bank_account"],
                        $candidate["ktp"],
                        $candidate["npwp"],
                        $bpjskesehatan,
                        $bpjsketenagakerjaan,
                        $candidate["email"],
                        $candidate["attendance_id"],
                        format_tanggal($candidate["join_indohr_at"],"dMY"),
                        format_tanggal($candidate["created_at"],"dMY"),
						$candidate["created_by"]),
					array("align='right' valign='top'","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>