<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$jurnal_id = $db->fetch_single_data("jurnals","id",array("transaction_id" => $_GET["deleting"]));
		$db->addtable("jurnals");			$db->where("id",$jurnal_id); $db->delete_();
		$db->addtable("jurnal_details");	$db->where("jurnal_id",$jurnal_id); $db->delete_();
		$db->addtable("transactions");		$db->where("id",$_GET["deleting"]); $db->delete_();
		?> <script> window.location="?";</script> <?php
	}
	
	if($_POST["save_trx"] || $_POST["edit_trx"]){
		$db->addtable("transactions");
		$db->addfield("trx_date");		$db->addvalue($_POST["trx_date"]);
		$db->addfield("description");	$db->addvalue($_POST["description"]);
		$db->addfield("currency_id");	$db->addvalue($_POST["currency_id"]);
		$db->addfield("debit");			$db->addvalue($_POST["debit"]);
		$db->addfield("kredit");		$db->addvalue($_POST["kredit"]);
		$db->addfield("bank_id");		$db->addvalue($_POST["bank_id"]);
		$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");	$db->addvalue($__username);
		$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		if($_POST["edit_trx"]) {
			$db->where("id",$_POST["id"]);
			$inserting = $db->update();
		} else {
			$inserting = $db->insert();
		}
		if($inserting["affected_rows"] > 0){
			if($_POST["edit_trx"]) {
				$transaction_id = $_POST["id"];
				$jurnal_id = $db->fetch_single_data("jurnals","id",array("transaction_id" => $transaction_id));
				$db->addtable("jurnals");			$db->where("id",$jurnal_id); $db->delete_();
				$db->addtable("jurnal_details");	$db->where("jurnal_id",$jurnal_id); $db->delete_();
				$db->addtable("transactions");		$db->where("id",$_GET["deleting"]); $db->delete_();
			} else {
				$transaction_id = $inserting["insert_id"];
			}
			$db->addtable("jurnals");
			$db->addfield("tanggal");			$db->addvalue($_POST["trx_date"]);
			$db->addfield("transaction_id");	$db->addvalue($transaction_id);
			$db->addfield("description");		$db->addvalue($_POST["description"]);
			$db->addfield("currency_id");		$db->addvalue($_POST["currency_id"]);
			$db->addfield("bank_id");			$db->addvalue($_POST["bank_id"]);
			$db->addfield("status");			$db->addvalue(1);
			$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("created_by");		$db->addvalue($__username);
			$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");		$db->addvalue($__username);
			$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$inserting = $db->insert();
			if($inserting["affected_rows"] > 0){
				$jurnal_id = $inserting["insert_id"];
				$db->addtable("jurnal_details");
				$db->addfield("jurnal_id");		$db->addvalue($jurnal_id);
				$db->addfield("description");	$db->addvalue($_POST["description"]);
				$db->addfield("debit");			$db->addvalue($_POST["debit"]);
				$db->addfield("kredit");		$db->addvalue($_POST["kredit"]);
				$db->insert();
			}
			$error_messages = "<font style='color:green;'><b><h3>Transaction Saved</h3></b></font>";
			$_GET["mode"] = "";
		}
		
	}
?>
<div class="bo_title">Daily Transactions</div>
<?=$error_messages;?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
                $trx_date = $f->input("trx_date",@$_GET["trx_date"],"type='date'");
				$description = $f->input("description",@$_GET["description"]);
			?>
			<?=$t->row(array("Transaction Date",$trx_date));?>
			<?=$t->row(array("Description",$description));?>
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
	if(@$_GET["trx_date"]!="") $whereclause .= "(trx_date LIKE '%".$_GET["trx_date"]."%') AND ";
	if(@$_GET["description"]!="") $whereclause .= "(description LIKE '%".$_GET["description"]."%') AND ";
	
	$db->addtable("transactions");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($_max_counting);
	$maxrow = count($db->fetch_data(true));
	$start = getStartRow(@$_GET["page"],$_rowperpage);
	$paging = paging($_rowperpage,$maxrow,@$_GET["page"],"paging");
	
	$db->addtable("transactions");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));$db->limit($start.",".$_rowperpage);
	if(@$_GET["sort"] == "") $_GET["sort"] = "trx_date";
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$transactions = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='transactions_list.php?mode=add';\"");?>
	<?=$f->input("upoader","Upload From Xlsx","type='button' onclick=\"window.location='transactions_uploader.php';\"");?>
	<?=$paging;?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
                        "<div onclick=\"sorting('trx_date');\">Trx Date</div>",
						"<div onclick=\"sorting('description');\">Description</div>",
						"<div onclick=\"sorting('currency_id');\">Curr</div>",
						"Debit",
						"Credit",
						"Bank",
						"<div onclick=\"sorting('created_at');\">Created At</div>",
						"<div onclick=\"sorting('created_by');\">Created By</div>",
						""));?>
						
	<?php 
		if($_GET["mode"] == "add" || $_GET["mode"] == "edit"){
			$_trx_date = date("Y-m-d");
			$_description = "";
			$_currency_id = "IDR";
			$_debit = 0;
			$_kredit = 0;
			$_save_button = "save_trx";
			if($_GET["mode"] == "edit"){
				$db->addtable("transactions");$db->where("id",$_GET["id"]);$db->limit(0);
				$data = $db->fetch_data();
				$_trx_date = $data["trx_date"];
				$_description = $data["description"];
				$_currency_id = $data["currency_id"];
				$_debit = $data["debit"];
				$_kredit = $data["kredit"];
				$_bank_id = $data["bank_id"];
				$_save_button = "edit_trx";
			}
			
			$txt_trx_date = $f->input("trx_date",$_trx_date,"type='date'");
			$txt_description = $f->input("description",$_description,"style='width:350px;'");
			$sel_currencies = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as name"),$_currency_id);
			$txt_debit = $f->input("debit",$_debit,"type='number'");
			$txt_kredit = $f->input("kredit",$_kredit,"type='number'");
			$sel_bank = $f->select("bank_id",$db->fetch_select_data("banks","id","concat(name,' (',no_rek,')') as bank",array(),array("name,no_rek"),"",true),$_bank_id);
			$btn_save = $f->input($_save_button,"Save","type='submit'");
			$btn_save .= $f->input("cancel","Cancel","type='button' onclick=\"window.location='?'\"");
			if($_GET["mode"] == "edit"){
				$btn_save .= $f->input("id",$_GET["id"],"type='hidden'");
			}
	?>
		<?=$f->start();?>
			<?=$t->row(
				array("",
					$txt_trx_date,
					$txt_description,
					$sel_currencies,
					$txt_debit,
					$txt_kredit,
					$sel_bank,
					"","",
					$btn_save),
				array("align='right' valign='top'","","","","","","","")
			);?>
		<?=$f->end();?>
	<?php } ?>
	
	<?php foreach($transactions as $no => $transaction){ ?>
		<?php
			$actions = "<a href=\"transactions_list.php?mode=edit&id=".$transaction["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$transaction["id"]."';}\">Delete</a>
						";
			$a = strpos($transaction["description"],"{prf_id:");
			if($a > 0){
				$b = strpos($transaction["description"],"}",$a)-1;
				$pattern = "{".substr($transaction["description"],$a+1,$b-$a)."}";
				$prf_id = explode(":",substr($transaction["description"],$a+1,$b-$a))[1];
				$link = "<a target='_BLANK' href='prf_view.php?id=".$prf_id."'>".$pattern."</a>";
				$transaction["description"] = str_replace($pattern,$link,$transaction["description"]);
			}
		?>
		<?=$t->row(
					array($no+$start+1,
                        format_tanggal($transaction["trx_date"],"dMY"),
						$transaction["description"],
						$transaction["currency_id"],
						format_amount($transaction["debit"]),
						format_amount($transaction["kredit"]),
						$db->fetch_single_data("banks","concat(name,' (',no_rek,')') as bank",array("id" => $transaction["bank_id"])),
						format_tanggal($transaction["created_at"],"dMY"),
						$transaction["created_by"],
						$actions),
					array("align='right' valign='top'",""," ","","align='right'","align='right'","","","")
				);?>
	<?php } ?>
	<?=$t->end();?>
	<?=$paging;?>
<?php include_once "footer.php";?>