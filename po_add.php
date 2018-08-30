<?php include_once "head.php";?>
<div class="bo_title">Add Purchase Order</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("po");
		$db->addfield("num");				$db->addvalue($_POST["num"]);
        $db->addfield("doc_date");			$db->addvalue($_POST["doc_date"]);
        $db->addfield("client_id");			$db->addvalue($_POST["client_id"]);
        $db->addfield("description");		$db->addvalue($_POST["description"]);
        $db->addfield("currency_id");		$db->addvalue($_POST["currency_id"]);
        $db->addfield("total");				$db->addvalue($_POST["total"]);
        $db->addfield("vat");				$db->addvalue($_POST["vat"]);
		$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("created_by");		$db->addvalue($__username);
		$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->insert();
		if($inserting["affected_rows"] >= 0){
			$po_id = $inserting["insert_id"];
			foreach($_POST["price"] as $key => $arr_material_code){
				$db->addtable("po_detail");
				$db->addfield("po_id");					$db->addvalue($po_id);
				$db->addfield("po_num");				$db->addvalue($_POST["num"]);
				$db->addfield("material_code");			$db->addvalue($_POST["material_code"][$key]);
				$db->addfield("description_detail");	$db->addvalue($_POST["description_detail"][$key]);
				$db->addfield("delivery_date");			$db->addvalue($_POST["delivery_date"][$key]);
				$db->addfield("qty");					$db->addvalue($_POST["qty"][$key]);
				$db->addfield("unit_id");				$db->addvalue($_POST["unit_id"][$key]);
				$db->addfield("currency_id");			$db->addvalue($_POST["detail_currency_id"][$key]);
				$db->addfield("price");					$db->addvalue($_POST["price"][$key]);
				$db->addfield("total_price");			$db->addvalue($_POST["total_price"][$key]);
				$inserting = $db->insert();
			}
			javascript("alert('Data Saved');");
			javascript("window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	
	$sel_client = $f->select("client_id",$db->fetch_select_data("clients","id","name",null,array("name")));
	$txt_num = $f->input("num",$_POST["num"]);
	$cal_doc_date = $f->input("doc_date",$_POST["doc_date"],"type='date'");
    $txt_desc = $f->textarea("description",$_POST["description"]);
	$sel_currency_id = $f->select("currency_id",$db->fetch_select_data("currencies","id","concat(id) as id2"));
    $txt_total = $sel_currency_id ."&nbsp;&nbsp;". $f->input("total","","type='number'");
	$plusminbutton = $f->input("addrow","+","type='button' style='width:25px' onclick=\"adding_row('detail_area','row_detail_');\"")."&nbsp;";
	$plusminbutton .= $f->input("subrow","-","type='button' style='width:25px' onclick=\"substract_row('detail_area','row_detail_');\"");
    
	$txt_material_code = $f->input("material_code[0]","");
    $txt_desc_detail = $f->input("description_detail[0]","","style='width:300px;'");
	$cal_dev_date = $f->input("delivery_date[0]","","type='date'");
	$txt_qty = $f->input("qty[0]","","type='number'");
	$sel_unit = $f->select("unit_id[0]",$db->fetch_select_data("units","id","name"));
	$sel_detail_currency_id = $f->select("detail_currency_id[0]",$db->fetch_select_data("currencies","id","concat(id) as id2"));
	$txt_price = $sel_detail_currency_id . $f->input("price[0]","","type='number' step='0.01'");
	$txt_total_price = $f->input("total_price[0]","","type='number' step='0.01'");
	
?>
<?=$f->start("","POST","","enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Client",$sel_client));?>
        <?=$t->row(array("No. PO",$txt_num));?>
        <?=$t->row(array("Doc. Date",$cal_doc_date));?>
        <?=$t->row(array("Description",$txt_desc));?>
        <?=$t->row(array("Total",$txt_total));?>
        <?=$t->row(array("&nbsp;"));?>
	<?=$t->end();?>
	
	<?=$t->start("width='100%'","detail_area","editor_content_2");?>
        <?=$t->row(array($plusminbutton."<br>No.","Material Code","Desc","Deliv. Date","Qty","Unit","Price","Total"),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"));?>
		<?=$t->row(array("<div id=\"firstno\">1</div>",$txt_material_code,$txt_desc_detail,$cal_dev_date,$txt_qty,$sel_unit,$txt_price,$txt_total_price),array("nowrap style='font-weight:bold;font-size:14px;text-align:center;'"),"id=\"row_detail_0\"");?>
	<?=$t->end();?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_add","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>