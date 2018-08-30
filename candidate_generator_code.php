<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<link rel="stylesheet" type="text/css" href="styles/style.css">
<link rel="stylesheet" type="text/css" href="backoffice.css">
<script>
	function parent_load(elm_id,value){
		try{ parent.document.getElementById(elm_id).value = value; } catch (ex){}
		try{ parent.document.getElementById(elm_id).innerHTML = value; } catch (ex){}
		parent.$.fancybox.close();
	}
</script>
<?php
	include_once "common.php";
	$candidate_month = ($_GET["candidate_month"] != "")?$_GET["candidate_month"]:date("Y-m");
	$prefix = "OS".substr($candidate_month,2,2).substr($candidate_month,5,2);
	$db->addtable("candidates");$db->addfield("code");$db->awhere("code like '".$prefix."%'");$db->order("code DESC");$db->limit(1);
	$candidate_code = $db->fetch_data();
	$candidate_code = $candidate_code["code"];
	if($candidate_code == ""){
		$candidate_code = $prefix."001";
	} else {
		$candidate_code_now = (str_replace($prefix,"",$candidate_code) * 1) + 1;
		$candidate_code = $prefix.substr("000",0,3-strlen($candidate_code_now)).$candidate_code_now;
	}
	if($_GET["apply"] == 1){
		if($_GET["updating_table"] == "true"){
			$db->addtable("candidates");$db->addfield("code");$db->addvalue($candidate_code);$db->where("id",$_GET["candidate_id"]);
			$updating = $db->update();
			if($updating["affected_rows"] > 0){
				?> <script> parent_load('<?=$_GET["elm_return"];?>','<?=$candidate_code;?>'); </script> <?php
			}
		} else {
			?> <script> parent_load('<?=$_GET["elm_return"];?>','<?=$candidate_code;?>'); </script> <?php
		}
	}
?>
<table width="200">
	<tr><td nowrap>Month</td><td>:</td><td><input type="month" id="candidate_month" value="<?=$candidate_month;?>" onchange="reload.click();"></td></tr>
	<tr><td nowrap>Candidate Code</td><td>:</td><td><input type="text" name="text" value="<?=$candidate_code;?>" readonly></td></tr>
	<tr><td colspan = "3">
		<input type="button" value="Reload" id="reload" onclick="window.location='?candidate_id=<?=$_GET["candidate_id"];?>&elm_return=<?=$_GET["elm_return"];?>&updating_table=<?=$_GET["updating_table"];?>&candidate_month='+candidate_month.value;">
		<input type="button" value="Apply" onclick="window.location='?apply=1&candidate_id=<?=$_GET["candidate_id"];?>&elm_return=<?=$_GET["elm_return"];?>&updating_table=<?=$_GET["updating_table"];?>&candidate_month='+candidate_month.value;">
	</td></tr>
</table>