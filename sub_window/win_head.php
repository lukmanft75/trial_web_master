<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<link rel="stylesheet" type="text/css" href="../styles/style.css">
<link rel="stylesheet" type="text/css" href="../backoffice.css">
	
<script>
	function parent_load(name,id,value){
		parent.document.getElementById("sw_caption_"+name).innerHTML = value;
		parent.document.getElementById(name).value = id;
		parent.$.fancybox.close();
	}
	function formatNumber(number){
		number = number + '';
		x = number.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
</script>
<?php
	include_once "../common.php";
	$_name = $_GET["name"];
	$_value = $_GET["value"];
	$_tablename = $_GET["tablename"];
	$_id_field = $_GET["id_field"];
	$_caption_field = $_GET["caption_field"];
	$_title = $_GET["title"];
?>