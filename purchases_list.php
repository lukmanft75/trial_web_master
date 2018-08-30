<?php include_once "head.php";?>
<script>
	function totalsum(){
		var qty = document.getElementById("qty").value * 1;
		var price = document.getElementById("price").value * 1;
		document.getElementById("total").value = qty * price;
	}
</script>
<?php
	if($_GET["deleting"]){
		$db->addtable("purchases"); $db->where("id",$_GET["deleting"]); $db->delete_();
		?> <script> window.location="?trx_periode=<?=$_GET["trx_periode"];?>";</script> <?php
	}
	
	if($_POST["save_trx"] || $_POST["edit_trx"]){
		$_POST["periode"] = $_POST["periode"]."-01";
		$db->addtable("purchases");
		$db->addfield("periode");		$db->addvalue($_POST["periode"]);
		$db->addfield("item");			$db->addvalue($_POST["item"]);
		$db->addfield("esiro");			$db->addvalue($_POST["esiro"]);
		$db->addfield("qty");			$db->addvalue($_POST["qty"]);
		$db->addfield("unit");			$db->addvalue($_POST["unit"]);
		$db->addfield("price");			$db->addvalue($_POST["price"]);
		$db->addfield("total");			$db->addvalue($_POST["total"]);
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
			$error_messages = "<font style='color:green;'><b><h3>Purchasing Requisition Saved</h3></b></font>";
			$_GET["mode"] = "";
			$_GET["trx_periode"] = $_POST["periode"];
		}
		
	}
?>
<div class="bo_title">Purchasing Requisition</div>
<?=$error_messages;?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
                $trx_periode = $f->input("trx_periode",substr(@$_GET["trx_periode"],0,7),"type='month'");
                $trx_created_by = $f->select("trx_created_by",$db->fetch_select_data("users","email","name",[],["name"],"",true),$_GET["trx_created_by"],"style='height:20px'");
			?>
			<?=$t->row(array("Periode",$trx_periode));?>
			<?=$t->row(array("Create By",$trx_created_by));?>
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
	if(@$_GET["trx_periode"]!=""){
		$whereclause .= "(periode LIKE '%".$_GET["trx_periode"]."%') AND ";
		if(@$_GET["trx_created_by"]!="") $whereclause .= "created_by = '".$_GET["trx_created_by"]."' AND ";
	} else $whereclause .= "0 AND ";
	
	$db->addtable("purchases");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	if(@$_GET["sort"] == "") $_GET["sort"] = "id";
	if(@$_GET["sort"] != "") $db->order($_GET["sort"]);
	$purchases = $db->fetch_data(true);
?>

	<?=$f->input("add","Add","type='button' onclick=\"window.location='purchases_list.php?trx_periode=".$_GET["trx_periode"]."&mode=add';\"");?>
	<?php 
		if(isset($_GET["trx_periode"]) && $_GET["trx_periode"] != ""){ 
			echo $f->input("print","Print","type='button' onclick=\"window.open('purchases_print.php?trx_periode=".$_GET["trx_periode"]."');\"");
		}
	?>
	<?=$t->start("","data_content");?>
	<?=$t->header(array("No",
						"Periode",
						"<div onclick=\"sorting('item');\">Item</div>",
						"<div onclick=\"sorting('esiro');\">ESIRO</div>",
						"<div onclick=\"sorting('created_by');\">Request By</div>",
						"Qty",
						"Unit",
						"Price",
						"Total",
						""));?>
	<?php 
		if($_GET["mode"] == "add" || $_GET["mode"] == "edit"){
			$_periode = date("Y-m");
			$_item = "";
			$_esiro = "";
			$_qty = 0;
			$_unit = "";
			$_price = 0;
			$_total = 0;
			$_save_button = "save_trx";
			if($_GET["mode"] == "edit"){
				$db->addtable("purchases");$db->where("id",$_GET["id"]);$db->limit(1);
				$data = $db->fetch_data();
				$_periode = substr($data["periode"],0,-3);
				$_item = $data["item"];
				$_esiro = $data["esiro"];
				$_qty = $data["qty"];
				$_unit = $data["unit"];
				$_price = $data["price"];
				$_total = $data["total"];
				$_save_button = "edit_trx";
			}
			
			$txt_periode = $f->input("periode",$_periode,"type='month'");
			$txt_item = $f->input("item",$_item,"style='width:300px;'");
			$sel_esiro = $f->select("esiro",array(""=>"","e"=>"Empty","s"=>"Stock","i"=>"Inventory","r"=>"Replacement","o"=>"Other"),$_esiro);
			$txt_qty = $f->input("qty",$_qty,"type='number' onkeyup='totalsum();'");
			$txt_unit = $f->input("unit",$_unit);
			$txt_price = $f->input("price",$_price,"type='number' onkeyup='totalsum();'");
			$txt_total = $f->input("total",$_total,"readonly");
			$btn_save = $f->input($_save_button,"Save","type='submit'");
			if($_GET["mode"] == "edit"){
				$btn_save .= $f->input("id",$_GET["id"],"type='hidden'");
			}
	?>
		<?=$f->start();?>
			<?=$t->row(
				array("",
					$txt_periode,
					$txt_item,
					$sel_esiro,
					"",
					$txt_qty,
					$txt_unit,
					$txt_price,
					$txt_total,
					$btn_save),
				array("align='center' valign='top'","","","","","","","","")
			);?>
		<?=$f->end();?>
	<?php } ?>
	
	<?php 
		$total = 0;
		foreach($purchases as $no => $purchase){ ?>
		<?php
			$actions = "<a href=\"purchases_list.php?trx_periode=".$_GET["trx_periode"]."&mode=edit&id=".$purchase["id"]."\">Edit</a> |
						<a href='#' onclick=\"if(confirm('Are You sure to delete this data?')){window.location='?trx_periode=".$_GET["trx_periode"]."&deleting=".$purchase["id"]."';}\">Delete</a>
						";
			$esiro = "";
			if($purchase["esiro"] == "e") $esiro = "Empty";
			if($purchase["esiro"] == "s") $esiro = "Stock";
			if($purchase["esiro"] == "i") $esiro = "Inventory";
			if($purchase["esiro"] == "r") $esiro = "Replacement";
			if($purchase["esiro"] == "o") $esiro = "Other";
			$total += $purchase["total"];
		?>	
		<?=$t->row(
					array($no+$start+1,
						format_tanggal($purchase["periode"],"M Y"),
						$purchase["item"],
						$esiro,
						$purchase["created_by"],
						$purchase["qty"],
						$purchase["unit"],
						format_amount($purchase["price"]),
						format_amount($purchase["total"]),
						$actions),
					array("align='center' valign='top'","","","","","align='right'","","align='right'","align='right'","")
				);?>
	<?php } ?>
	<?=$t->row(
				array("<b>TOTAL CASH NEEDED</b>","","<b>".format_amount($total)."</b>",""),
				array("align='center' colspan='4'","colspan='4'","align='right'")
			);?>
	<?=$t->end();?>
<?php include_once "footer.php";?>