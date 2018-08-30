<?php	
	set_time_limit(0);
	ini_set('memory_limit', '-1');
?>
<?php include_once "head.php";?>
<?php include_once "classes/simplexlsx.class.php";?>
<?php include_once "func.convert_number_to_words.php";?>
<?php	
	if(isset($_POST["process"])) {
		$file_name = $_POST["file_name"];
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
		$contents = $xlsx->rows($_POST["sheet"]);
		foreach($contents as $key => $rowdata){
			if($key > 0){
				//echo "<br>".$rowdata[0];
				if($rowdata[0] == "") break;
				
				$db->addtable("po"); $db->where("num",$rowdata[0]); $db->delete_();
				$db->addtable("po_detail"); $db->where("po_num",$rowdata[0]); $db->delete_();
				$client_id = $_POST["client_id"];
				$currency_id = $db->fetch_single_data("currencies","id",array("id"=>"%".$rowdata[2]."%:LIKE"));
				
				$last_po_no = $rowdata[0];
				$db->addtable("po");
				$db->addfield("num");				$db->addvalue($rowdata[0]);
				if(xls_date($rowdata[4]) == "" && $rowdata[4] != ""){
					$db->addfield("doc_date");			$db->addvalue($rowdata[4]." 00:00:00");
				} else if(xls_date($rowdata[4]) != ""){
					$db->addfield("doc_date");			$db->addvalue(xls_date($rowdata[4])." 00:00:00");
				}
				$db->addfield("client_id");			$db->addvalue($client_id);
				$db->addfield("description");		$db->addvalue($rowdata[17]." ".$rowdata[18]);
				$db->addfield("currency_id");		$db->addvalue($currency_id);
				$db->addfield("total");				$db->addvalue($rowdata[1]);
				$db->addfield("vat");				$db->addvalue($rowdata[7]);
				$db->addfield("created_at");		$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("created_by");		$db->addvalue($__username);
				$db->addfield("created_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");		$db->addvalue($__username);
				$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$inserting = $db->insert();
				if($inserting["affected_rows"] > 0){
					$po_id = $inserting["insert_id"];
					//insert po_detail
					$db->addtable("po_detail");
					$db->addfield("po_id");$db->addvalue($po_id);
					$db->addfield("po_num");$db->addvalue($rowdata[0]);
					$db->addfield("description_detail");$db->addvalue($rowdata[17]." ".$rowdata[18]);
					$db->addfield("qty");$db->addvalue(1);
					$db->addfield("currency_id");$db->addvalue($currency_id);
					$db->addfield("price");$db->addvalue($rowdata[1]);
					$db->addfield("total_price");$db->addvalue($rowdata[1]);
					$db->insert();
					
					//wcc
					if($rowdata[12] != ""){
						$wcc_id = $db->fetch_single_data("wcc","id",array("wcc_no" => $rowdata[12].":LIKE"));
						$db->addtable("wcc");
						$db->addfield("wcc_no");	$db->addvalue($rowdata[12]);
						$db->addfield("po_no");		$db->addvalue($rowdata[0]);
						$db->addfield("created_at");$db->addvalue(date("Y-m-d H:i:s"));
						$db->addfield("created_by");$db->addvalue($__username);
						$db->addfield("created_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
						$db->addfield("updated_at");$db->addvalue(date("Y-m-d H:i:s"));
						$db->addfield("updated_by");$db->addvalue($__username);
						$db->addfield("updated_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
						if($wcc_id > 0){
							$db->where("id",$wcc_id); 
							$db->update();
							$num_wcc_old++;
						} else {
							$db->insert();
							$num_wcc_new++;
						}
					}
				}
			}
		}
		?> <script> alert("Data Uploaded [<?=$last_po_no;?>]"); </script><?php
		unlink("upload_files/".$file_name);
	}
	if(isset($_POST["upload"])) {
		$file_name = "po_".date("YmdHis").".xlsx";
		move_uploaded_file($_FILES['xlsx']['tmp_name'],"upload_files/".$file_name);
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
?>
	<table width="100%"><tr><td align="center">
		<table width="100"><tr><td nowrap>
			<?=$f->start();?>
				<fieldset>
					Choose Sheet: <?=$f->select("sheet",$xlsx->sheetNames());?><br><br>
					Client : <?=$f->select("client_id",$db->fetch_select_data("clients","id","name",array(),array(),"",true));?><br><br>
					<?=$f->input("process","Process","type='submit'","btn_sign");?>
				</fieldset>
				<?=$f->input("file_name",$file_name,"type='hidden'");?>
			<?=$f->end();?>
		</td></tr></table>	
	</td></tr></table>	
<?php	
	}
?>

<?php if(!isset($_POST["upload"])) { ?>
	<table width="100%"><tr><td align="center">
		<table width="100"><tr><td nowrap>
		<?=$f->start("","POST","","enctype=\"multipart/form-data\"");?>
			Choose File for Upload : <?=$f->input("xlsx","","type='file' accept='.xlsx'");?>
			<br><br>
			<?=$f->input("upload","Upload","type='submit'","btn_sign");?>
		<?=$f->end();?>
		</td></tr></table>	
	</td></tr></table>	
<?php } ?>
<?php include_once "footer.php";?>