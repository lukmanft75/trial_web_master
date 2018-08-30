<?php include_once "head.php";?>
<script>
	function toggle_dashboard(id){
		try{ document.getElementById("dashboard_1").style.display = "none"; } catch(e){}
		try{ document.getElementById("dashboard_2").style.display = "none"; } catch(e){}
		try{ document.getElementById("dashboard_" + id).style.display = "block"; } catch(e){}
	}
</script>
<?php
	$_GET["startdate"] = ($_GET["startdate"] == "") ? date("Y")."-01" : $_GET["startdate"];
	$_GET["enddate"] = ($_GET["enddate"] == "") ? date("Y")."-12" : $_GET["enddate"];
	if($_GET["enddate"] < $_GET["startdate"]){
		$temp = $_GET["enddate"];
		$_GET["enddate"] = $_GET["startdate"];
		$_GET["startdate"] = $temp;
	}
	$startdate = $_GET["startdate"]; $enddate = $_GET["enddate"];
?>
<table width="100%">
	<tr>
		<td align="center">
			<table width="100%">
				<tr>
					<td>
						<?=$f->start("","GET");?>
							Periode : <?=$f->input("startdate",$_GET["startdate"],"type='month'");?> - <?=$f->input("enddate",$_GET["enddate"],"type='month'");?>
							<?=$f->input("load","Load","type='submit'","btn_sign");?>
						<?=$f->end();?>
						<br>
						<nav id="primary_nav_wrap" style="margin-bottom:50px;">
							<ul>
								<li class="bo_menu"><a onclick="toggle_dashboard(2);">Charts</a></li><li>&nbsp;</li>
								<li class="bo_menu"><a onclick="toggle_dashboard(1);">Statistics</a></li><li>&nbsp;</li>
							</ul>
						</nav>
						<br>
						<div id="dashboard_1" style="display:none;"> <?php include_once "dashboard_1.php";?> </div>
						<div id="dashboard_2"> <?php include_once "dashboard_2.php";?> </div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include_once "footer.php";?>