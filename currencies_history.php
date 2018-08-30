<?php include_once "head.php";?>
<?php
	if($_GET["deleting"]){
		$db->addtable("currencies_history"); $db->where("id",$_GET["deleting"]); $db->delete_();
		?> <script> window.location="?";</script> <?php
	}
	
	if($_POST["save_trx"] || $_POST["edit_trx"]){
		$db->addtable("currencies_history");
		$db->addfield("currency_id");	$db->addvalue($_POST["currency_id"]);
		$db->addfield("kurs");			$db->addvalue($_POST["kurs"]);
		$db->addfield("date1");			$db->addvalue($_POST["date1"]);
		$db->addfield("date2");			$db->addvalue($_POST["date2"]);
		$db->addfield("updated_at");	$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");	$db->addvalue($__username);
		$db->addfield("updated_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
		if($_POST["edit_trx"]) {
			$db->where("id",$_POST["id"]);
			$inserting = $db->update();
		} else {
			$db->addfield("created_at");	$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("created_by");	$db->addvalue($__username);
			$db->addfield("created_ip");	$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$inserting = $db->insert();
		}
		if($inserting["affected_rows"] > 0){
			$error_messages = "<font style='color:green;'><b><h3>Kurs History Saved</h3></b></font>";
			$_GET["mode"] = "";
			$_GET["trx_periode"] = $_POST["periode"];
		}
		
	}
?>
<div class="bo_title">Kurs History</div>
<?=$error_messages;?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
                $srcCurrencies = $f->select("srcCurrencies",$db->fetch_select_data("currencies","id","concat(id) as name"),@$_GET["srcCurrencies"],"style='height:20px'");
			?>
			<?=$t->row(array("Currency",$srcCurrencies));?>
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
	if(@$_GET["srcCurrencies"]!="") $whereclause .= "currency_id = '".$_GET["srcCurrencies"]."' AND ";
	
	$db->addtable("currencies_history");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(@$_GET["sort"] == "") $_GET["sort"] = "id";
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$currencies_histories = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='currencies_history.php?mode=add';\"");?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"<div onclick=\"sorting('currency_id');\">Currency</div>",
						"<div onclick=\"sorting('kurs');\">Kurs</div>",
						"<div onclick=\"sorting('date1');\">From</div>",
						"<div onclick=\"sorting('date2');\">To</div>",
						""));?>
	<?php 
		if($_GET["mode"] == "add" || $_GET["mode"] == "edit"){
			$_currency_id = "";
			$_kurs = "";
			$_date1 = "";
			$_date2 = "";
			$_save_button = "save_trx";
			if($_GET["mode"] == "edit"){
				$db->addtable("currencies_history");$db->where("id",$_GET["id"]);$db->limit(1);
				$data = $db->fetch_data();
				$_currency_id = $data["currency_id"];
				$_kurs = $data["kurs"];
				$_date1 = $data["date1"];
				$_date2 = $data["date2"];
				$_save_button = "edit_trx";
			}
			
			$sel_currencies = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as name"),$_currency_id,"style='height:20px'");
			$txt_kurs = $f->input("kurs",$_kurs,"type='number'");
			$txt_date1 = $f->input("date1",$_date1,"type='date'");
			$txt_date2 = $f->input("date2",$_date2,"type='date'");
			$btn_save = $f->input($_save_button,"Save","type='submit'");
			if($_GET["mode"] == "edit"){
				$btn_save .= $f->input("id",$_GET["id"],"type='hidden'");
			}
	?>
		<?=$f->start();?>
			<?=$t->row(
				array("",
					$sel_currencies,
					$txt_kurs,
					$txt_date1,
					$txt_date2,
					$btn_save),
				array("align='center' valign='top'","","","","","")
			);?>
		<?=$f->end();?>
	<?php } ?>
	
	<?php 
		$total = 0;
		foreach($currencies_histories as $no => $currencies_history){ ?>
		<?php
			$actions = "<a href=\"currencies_history.php?mode=edit&id=".$currencies_history["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?deleting=".$currencies_history["id"]."';}\">Delete</a>
						";
		?>	
		<?=$t->row(
					array($no+$start+1,
						$currencies_history["currency_id"],
						format_amount($currencies_history["kurs"]),
						format_tanggal($currencies_history["date1"]),
						format_tanggal($currencies_history["date2"]),
						$actions),
					array("align='center' valign='top'","","align='right'","","","")
				);?>
	<?php } ?>
	<?=$t->end();?>
<?php include_once "footer.php";?>