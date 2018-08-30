<?php include_once "head.php"; ?>
<?php
	if(isset($_POST["save"])){
		$db->addtable("trial_balance");
		$db->where("id","0","i",">");
		$db->delete_();
		$coas = $db->fetch_all_data("coa",[],"coa LIKE '1%' ORDER BY coa");
		foreach($coas as $coa){
			$db->addtable("trial_balance");
			$db->addfield("periode");	$db->addvalue($_POST["periode"]);
			$db->addfield("coa");		$db->addvalue($coa["coa"]);
			$db->addfield("debit");		$db->addvalue($_POST["debit"]["'".$coa["coa"]."'"]);
			$db->addfield("credit");	$db->addvalue($_POST["credit"]["'".$coa["coa"]."'"]);
			$db->addfield("created_at");$db->addvalue($__now);	
			$db->addfield("created_by");$db->addvalue($__username);	
			$db->addfield("created_ip");$db->addvalue($__remoteaddr);
			$db->insert();
		}
	}
?>
<div class="bo_title">TRIAL BALANCE</div>
<?php
	$periode = $db->fetch_single_data("trial_balance","periode",[],["periode DESC"]);
	if(!$periode) $periode = substr($__now,0,10);
?>
<?=$f->start("","POST");?>
	<br><b>Periode : </b><?=$f->input("periode",$periode,"type='date'");?><br>
	<?=$t->start("","data_content");?>
	<?=$t->header(["COA","Description","Debit","Credit"]);?>
	<?php
		$coas = $db->fetch_all_data("coa",[],"coa LIKE '1%' ORDER BY coa");
		foreach($coas as $coa){
			$debit = $db->fetch_single_data("trial_balance","debit",["periode" => $periode,"coa" => $coa["coa"]],["id DESC"]);
			$debit = $f->input("debit['".$coa["coa"]."']",$debit,"type='number' step='1'");
			$credit = $db->fetch_single_data("trial_balance","credit",["periode" => $periode,"coa" => $coa["coa"]],["id DESC"]);
			$credit = $f->input("credit['".$coa["coa"]."']",$credit,"type='number' step='1'");
			echo $t->row([$coa["coa"],$coa["description"],$debit,$credit]);
		}
	?>
	<?=$t->end();?>
	<?=$f->input("save","Save","type='submit'","btn btn-info");?>
<?=$f->end();?>
<?php include_once "footer.php";?>