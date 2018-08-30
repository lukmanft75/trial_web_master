<?php include_once "head.php";?>
<div class="bo_title">Edit Finance Report Template</div>
<?php
	if(isset($_POST["save"])){
		$db->addtable("finance_report_template"); $db->where("id",$_GET["id"]);
		$db->addfield("name");				$db->addvalue($_POST["name"]);
		$db->addfield("updated_at");		$db->addvalue(date("Y-m-d H:i:s"));
		$db->addfield("updated_by");		$db->addvalue($__username);
		$db->addfield("updated_ip");		$db->addvalue($_SERVER["REMOTE_ADDR"]);
		$inserting = $db->update();
		if($inserting["affected_rows"] >= 0){
			$finance_report_template_id = $_GET["id"];
			$db->addtable("finance_report_template_detail");$db->where("finance_report_template_id",$finance_report_template_id);$db->delete_();
			foreach($_POST["finance_report_template"] as $sayap => $finance_report_templates){
				foreach($finance_report_templates as $seqno => $finance_report_template){
					$db->addtable("finance_report_template_detail");
					$db->addfield("finance_report_template_id");	$db->addvalue($finance_report_template_id);
					$db->addfield("sayap");							$db->addvalue($sayap);
					$db->addfield("seqno");							$db->addvalue($seqno);
					$db->addfield("caption");						$db->addvalue($finance_report_template["caption"]);
					$db->addfield("coa");							$db->addvalue($finance_report_template["coa"]);
					$db->addfield("formula");						$db->addvalue($finance_report_template["formula"]);
					$db->insert();
				}
			}
			javascript("alert('Data Saved');");
		} else {
			javascript("alert('Saving data failed');");
		}
	}
	if(!isset($_GET["not_firstload"])){
		$_POST["name"] = $db->fetch_single_data("finance_report_template","name",["id"=>$_GET["id"]]);
		$details = $db->fetch_all_data("finance_report_template_detail",[],"finance_report_template_id = ".$_GET["id"],"sayap,seqno");
		foreach($details as $key => $detail){
			$_POST["finance_report_template"][$detail["sayap"]][$detail["seqno"]][caption] = $detail["caption"];
			$_POST["finance_report_template"][$detail["sayap"]][$detail["seqno"]][coa] = $detail["coa"];
			$_POST["finance_report_template"][$detail["sayap"]][$detail["seqno"]][formula] = $detail["formula"];
		}
	}
	if(isset($_POST["up"])){
		$move_id = $_POST["move_id"];
		$move_sayap = $_POST["move_sayap"];
		$temp = $_POST["finance_report_template"][$move_sayap][$move_id];
		$_POST["finance_report_template"][$move_sayap][$move_id] = $_POST["finance_report_template"][$move_sayap][$move_id - 1];
		$_POST["finance_report_template"][$move_sayap][$move_id - 1] = $temp;
	}
	if(isset($_POST["down"])){
		$move_id = $_POST["move_id"];
		$move_sayap = $_POST["move_sayap"];
		$temp = $_POST["finance_report_template"][$move_sayap][$move_id];
		$_POST["finance_report_template"][$move_sayap][$move_id] = $_POST["finance_report_template"][$move_sayap][$move_id + 1];
		$_POST["finance_report_template"][$move_sayap][$move_id + 1] = $temp;
	}
	
	
	$txt_name = $f->input("name",$_POST["name"]);
	$coas = $db->fetch_select_data("coa","coa","concat(coa, ' -- ',description)",[],["coa"],"",true);
?>
<?=$f->start("","POST","?not_firstload=1&id=".$_GET["id"],"enctype='multipart/form-data'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(array("Report Name",$txt_name));?>
	<?=$t->end();?>
	<br>
	<?php
		echo $f->input("move_id","0","type='hidden'");
		echo $f->input("move_sayap","0","type='hidden'");
		$sayaps = ["kiri","kanan"];
		for($sayap=0;$sayap<2;$sayap++){ 
	?>
		<b><?=strtoupper($sayaps[$sayap]);?><b>
		<table>
			<tr>
				<td valign="top">
					<?php
						$plusmin[$sayap] = $f->input("inc[".$sayap."]","+","type='submit'");
						$plusmin[$sayap] .= $f->input("dec[".$sayap."]","-","type='submit'");
						if(isset($_POST["inc"][$sayap])){
							$_POST["finance_report_template"][$sayap][count($_POST["finance_report_template"][$sayap])] = "";
						}
						if(isset($_POST["dec"][$sayap])){
							unset($_POST["finance_report_template"][$sayap][count($_POST["finance_report_template"][$sayap])-1]);
						}
						if(count($_POST["finance_report_template"][$sayap]) == 0){
							$_POST["finance_report_template"][$sayap][0] = "";
						}
					?>
					<?=$t->start("","editor_content");?>
						<?=$t->header(["SeqNo".$plusmin[$sayap],"Caption","Coa","Formula"]);?>
						<?php
							for($seqno = 0;$seqno < count($_POST["finance_report_template"][$sayap]); $seqno++){
								$btn_up_down = "";
								if($seqno > 0){
									$btn_up_down = $f->input("up","&#9650;","type='submit' onclick=\"document.getElementById('move_id').value=".$seqno.";document.getElementById('move_sayap').value=".$sayap.";return true;\"");
								}
								$btn_up_down .= $f->input("down","&#9660;","type='submit' onclick=\"document.getElementById('move_id').value=".$seqno.";document.getElementById('move_sayap').value=".$sayap.";return true;\"");
								$txt_caption = $f->input("finance_report_template[".$sayap."][".$seqno."][caption]",$_POST["finance_report_template"][$sayap][$seqno][caption],"style='width:300px;'");
								$sel_coa = $f->select("finance_report_template[".$sayap."][".$seqno."][coa]",$coas,$_POST["finance_report_template"][$sayap][$seqno][coa],"style='height:20px;'");
								$txt_formula = $f->input("finance_report_template[".$sayap."][".$seqno."][formula]",$_POST["finance_report_template"][$sayap][$seqno][formula],"style='width:300px;'");
								echo $t->row([$seqno.$btn_up_down,$txt_caption,$sel_coa,$txt_formula]);
							}
						?>
						
					<?=$t->end();?>
				</td>
			</tr>
		</table>
	<?php } ?>
	<br>
	<?=$f->input("save","Save","type='submit'");?> <?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_edit","_list",$_SERVER["PHP_SELF"])."';\"");?>
<?=$f->end();?>
<?php include_once "footer.php";?>