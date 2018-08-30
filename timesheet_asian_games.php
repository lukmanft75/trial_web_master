<?php
	if($_GET["export"]){
		$_exportname = "TimeSheetAsianGames.xls";
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$_exportname);
		header("Pragma: no-cache");
		header("Expires: 0");
		$_GET["do_filter"]="Load";
		$_isexport = true;
	}
	include_once "head.php";
?>
<div class="bo_title">Time Sheet Asian Games</div>
<?=$error_messages;?>
<iframe id="myiframe" onload="resizeIframe(this)" src="http://103.253.113.201/indohr_attendance/attendance_list.php" style="border:0px;width:100%;height:600px;" gesture="media"  allow="encrypted-media" allowfullscreen></iframe>
<?php include_once "footer.php"; ?>