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
		$num_insert = 0;
		$num_update = 0;
		$client_ids = "";
		$arr_xlsx = array();
		/* $return = "<table border='1'><tr><td><b>DATA XLSX</b></td><td><b>DB EXISTED</b></td></tr>"; */
		foreach($contents as $key => $rowdata){
			if($key > 0){
				//echo "<br>".$rowdata[0];
				if($rowdata[1] == "") break;
				$npwp = $rowdata[0];
				$name = $rowdata[1];
				$address = $rowdata[2];
				
				
				$db->addtable("clients");
				$db->addfield("id");
				$client_name = trim(str_ireplace(array("pt","tbk",".",",","Servis"),"",$name));
				$db->awhere("name LIKE '%".str_replace(" ","%",$client_name)."%'");
				$db->limit(1);
				$arrtemp = $db->fetch_data();
				$client_id = $arrtemp["id"];
				
				/* $return .= "<tr><td>$name</td><td>";
				
				$db->addtable("clients");
				$client_name = trim(str_ireplace(array("pt","tbk",".",",","Servis"),"%",$name));
				// $arr_name = explode(" ",$client_name);
				// $client_name = $arr_name[0];
				$client_name = str_replace(" ","%",$client_name);
				$db->awhere("name LIKE '%".$client_name."%'");
				foreach($db->fetch_data(true) as $arrclient){
					$client_ids .= $arrclient["id"].",";
					$return .= $arrclient["name"]."<br>";
				}
				$return .= "</td></tr>"; */
				
				
				$db->addtable("clients");
				$db->addfield("name"); 		$db->addvalue($name);
				$db->addfield("address"); 	$db->addvalue($address);
				$db->addfield("tax_no"); 	$db->addvalue($npwp);
				
				$db->addfield("created_at");$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("created_by");$db->addvalue($__username);
				$db->addfield("created_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->addfield("updated_at");$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");$db->addvalue($__username);
				$db->addfield("updated_ip");$db->addvalue($_SERVER["REMOTE_ADDR"]);
				if($client_id <= 0){ // belum pernah ada
					$inserting = $db->insert();
					if($inserting["affected_rows"] > 0) $num_insert++;
				} else {
					$db->where("id",$client_id);
					$inserting = $db->update();
					if($inserting["affected_rows"] > 0) $num_update++;
				} 
			}
		}
		/* $return .= "</table><br>";
		echo $return; 
		$db->addtable("clients");
		$db->awhere("id NOT IN (".$client_ids."0)");
		foreach($db->fetch_data(true) as $arrclient){
			echo $arrclient["name"]."<br>";
		}
		*/
		
		unlink("upload_files/".$file_name);
		echo "<b>UPDATED : $num_update</b><br>";
		echo "<b>INSERTED: $num_insert</b><br>";
	}
	if(isset($_POST["upload"])) {
		$file_name = "client_".date("YmdHis").".xlsx";
		move_uploaded_file($_FILES['xlsx']['tmp_name'],"upload_files/".$file_name);
		$xlsx = new SimpleXLSX("upload_files/".$file_name);
?>
	<table width="100%"><tr><td align="center">
		<table width="100"><tr><td nowrap>
			<?=$f->start();?>
				<fieldset>
					Choose Sheet: <?=$f->select("sheet",$xlsx->sheetNames());?><br><br>
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