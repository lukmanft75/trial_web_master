<?php include_once "head.php";?>
<?php
	
	if($_POST["edit_trx"]){
		foreach($_POST["saldo"] as $bank_id => $saldo){
			$db->addtable("banks_histories");
			$db->addfield("kurs");				$db->addvalue($_POST["kurs"][$bank_id]);
			$db->addfield("saldo");				$db->addvalue($saldo);
			$db->addfield("saldo_effective");	$db->addvalue($_POST["saldo_effective"][$bank_id]);
			$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
			$db->addfield("updated_by");		$db->addvalue($__username);
			$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
			$db->where("history_date",$_GET["txt_history_date"]);
			$db->where("bank_id",$bank_id);
			$db->update();
		}
		$error_messages = "<font style='color:green;'><b><h3>Bank Histories Saved</h3></b></font>";
		$_GET["mode"] = "";
	}
	if($_GET["txt_history_date"] == ""){
		$_GET["txt_history_date"] = $db->fetch_single_data("banks_histories","history_date",array("saldo" => "0:>"),array("history_date DESC"));
	}
	
	$last_history_date = $db->fetch_single_data("banks_histories","history_date",array("saldo" => "0:>","history_date" => $_GET["txt_history_date"].":<>"),array("history_date DESC"));
?>
<div class="bo_title">Bank Histories</div>
<?=$error_messages;?>
<div id="bo_expand" onclick="toogle_bo_filter();">[+] View Filter</div>
<div id="bo_filter">
	<div id="bo_filter_container">
		<?=$f->start("filter","GET");?>
			<?=$t->start();?>
			<?php
                $txt_history_date = $f->input("txt_history_date",$_GET["txt_history_date"],"type='date'");
			?>
			<?=$t->row(array("History Date",$txt_history_date));?>
			<?=$t->end();?>
			<?=$f->input("page","1","type='hidden'");?>
			<?=$f->input("sort",@$_GET["sort"],"type='hidden'");?>
			<?=$f->input("do_filter","Load","type='submit'");?>
			<?=$f->input("reset","Reset","type='button' onclick=\"window.location='?';\"");?>
		<?=$f->end();?>
	</div>
</div>
<script> bo_filter_container.style.display = "block"; </script>

<?php
	if(isset($_GET["txt_history_date"]) && $_GET["txt_history_date"] != ""){ 
		if($db->fetch_single_data("banks_histories","concat(count(0))",array("history_date"=>$_GET["txt_history_date"])) <= 0){//belum ada
			$db->addtable("banks");$db->order("is_debt,id");$banks = $db->fetch_data(true);
			foreach($banks as $bank){
				$db->addtable("banks_histories");
				$db->addfield("history_date");		$db->addvalue($_GET["txt_history_date"]);
				$db->addfield("bank_id");			$db->addvalue($bank["id"]);
				$db->addfield("kurs");				$db->addvalue($bank["kurs"]);
				$db->addfield("saldo");				$db->addvalue(0);
				$db->addfield("saldo_effective");	$db->addvalue(0);		
				$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
				$db->addfield("updated_by");		$db->addvalue($__username);
				$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
				$db->insert();
			}
		}
	}
	
	$whereclause = "";
	if(@$_GET["txt_history_date"]!="") $whereclause .= "(banks_histories.history_date LIKE '".$_GET["txt_history_date"]."') AND ";
	else $whereclause .= "0 AND ";
	
	$db->addtable("banks_histories");
	$db->addtable("banks");
	$db->addfield("id",0);
	$db->addfield("history_date",0);
	$db->addfield("bank_id",0);
	$db->addfield("kurs",0);
	$db->addfield("saldo",0);
	$db->addfield("saldo_effective",0);
	$db->addfield("is_debt",1);
	$db->addfield("id",1);
	$db->joiner(0,"bank_id",1,"id");
	if($whereclause != "") $db->awhere(substr($whereclause,0,-4));
	$db->order("is_debt",1);
	$db->order("id",1);
	$banks_histories = $db->fetch_data(true);
?>

	<?php 
	if(isset($_GET["txt_history_date"]) && $_GET["txt_history_date"] != ""){ 
		//echo $f->input("print","Print","type='button' onclick=\"window.open('purchases_print.php?txt_history_date=".$_GET["txt_history_date"]."');\"");
		echo $f->input("edit","Edit","type='button' onclick=\"window.location='?txt_history_date=".$_GET["txt_history_date"]."&mode=edit';\"");
		?>
		<table>
			<tr><td>Day</td><td> : </td><td><?=format_tanggal($_GET["txt_history_date"],"l");?></td></tr>
			<tr><td>Date</td><td> : </td><td><?=format_tanggal($_GET["txt_history_date"]);?></td></tr>
		</table>
		<?=$f->start();?>
		<?=$t->start("","data_content");?>
		<?=$t->header(array("No","Bank","Saldo"));?>
		
		<?php 
			$total_row_showed = false;
			$total = 0;
			foreach($banks_histories as $no => $banks_history){ ?>
				<?php
					$bank 			= $db->fetch_single_data("banks","name",array("id" => $banks_history["bank_id"]));
					$no_rek 		= $db->fetch_single_data("banks","no_rek",array("id" => $banks_history["bank_id"]));
					$description 	= $db->fetch_single_data("banks","description",array("id" => $banks_history["bank_id"]));
					$currency_id 	= $db->fetch_single_data("banks","currency_id",array("id" => $banks_history["bank_id"]));
					$is_debt 		= $db->fetch_single_data("banks","is_debt",array("id" => $banks_history["bank_id"]));
					if($currency_id != "IDR"){
						$bank .= " [".$currency_id."] ";
					}
					if($no_rek != "") 		$bank .= " (".$no_rek.") ";
					if($currency_id != "IDR"){
						$saldo 	= $banks_history["saldo"];
						$kurs 	= $banks_history["kurs"];
						$saldo_2 = 0;
						if($saldo > 0) $saldo_2 = $saldo / $kurs;
						$bank .= $saldo_2." kurs ".format_amount($kurs);
					}
					if($description != "") 	$bank .= " ".$description;
					if($is_debt == 1) 		$bank .= " Hutang Bank";
					if($banks_history["saldo_effective"] > 0) $bank .= " ; <u>Saldo Efektif ".format_amount($banks_history["saldo_effective"],2)."</u>";
					if($is_debt <=0) $total += $banks_history["saldo"];
					
					$saldo = format_amount($banks_history["saldo"],2);
					if($_GET["mode"] == "edit"){
						$banks_history["saldo"] = ($banks_history["saldo"] == 0)?$db->fetch_single_data("banks_histories","saldo",array("history_date" => $last_history_date,"bank_id" => $banks_history["bank_id"])):$banks_history["saldo"];
						$banks_history["saldo_effective"] = ($banks_history["saldo_effective"] == 0)?$db->fetch_single_data("banks_histories","saldo_effective",array("history_date" => $last_history_date,"bank_id" => $banks_history["bank_id"])):$banks_history["saldo_effective"];
						$saldo = $f->input("saldo[".$banks_history["bank_id"]."]",$banks_history["saldo"],"type='number' step='0.01'");
						$saldo .= " Efektif : ".$f->input("saldo_effective[".$banks_history["bank_id"]."]",$banks_history["saldo_effective"],"type='number' step='0.01'");
						$saldo .= " Kurs : ".$f->input("kurs[".$banks_history["bank_id"]."]",$banks_history["kurs"],"type='number' step='0.01'");
					}
					
					if($is_debt > 0 && !$total_row_showed){
						$total_row_showed = true;
						echo $t->row(
							array("<b>TOTAL</b>","","<b>".format_amount($total,2)."</b>"),
							array("align='center'","","align='right'")
						);
					}
				?>
			<?=$t->row(
						array($no+$start+1, $bank, $saldo),
						array("align='center' valign='top'","","align='right'")
					);?>
		<?php } ?>
		<?=$t->end();?>
		<?php if($_GET["mode"] == "edit"){ ?>
		<table width="100%"><tr><td align="right">
			<?=$f->input("edit_trx","Save","type='submit'");?>
			<?=$f->input("cancel","Cancel","type='button' onclick=\"window.location='?txt_history_date=".$_GET["txt_history_date"]."'\"");?>
		</td></tr></table>
		<?php } ?>
		<?=$f->end();?>
	<?php
	}
	?>
<?php include_once "footer.php";?>