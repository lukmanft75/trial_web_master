<?php include_once "head.php";

	$db->addtable("invoice");$db->where("id",$_GET["id"]);$db->limit(1);$invoice = $db->fetch_data();
    $client = $db->fetch_single_data("clients","name",array("id"=>$invoice["client_id"]));
?>

<div class="bo_title">View Invoice <?=$invoice["num"];?></div>    

<?php
$profil = "PT. Indo Human Resource<br />
        Epicentrum Walk OFfice Suites 7th Floor, Unit 0709A<br />
        Komplek Rasuna Epicentrum<br />
        Jl. HR. Rasuna Said - Kuningan, Jakarta, 12940, Indonesia<br />
        Phone   : 021-2994 1058, 2994 1059<br />
        Fax     : 021-2994 1055<br />
        Website : www.corphr.com<br />";
$datebox = 
$t->start("style='border-spacing:10px 30px'").
    $t->row(array("Date",$invoice["issue_at"])).
    $t->row(array("Invoice No.",$invoice["num"])).
    $t->row(array("Payment due date"),$invoice["due_date"]).
$t->end();

$descbox =
$t->start("frame='box' width='100%' border='0' cellpadding='10' style='border-collapse:collapse;'").
    $t->row(array("Description","Amount"),array("style='border:1px solid;'"),"align='center' style='background-color: #e5e5e5;'","bo_title").
    $t->row(array($invoice["description"],format_amount($invoice["total"])),array("style='border:1px solid;'","align='right' style='border:1px solid;'")).
    $t->row(array("TOTAL",format_amount($invoice["total"])),array("align='right' style='border-right:1px solid'","align='right' style='border:1px solid;'"),"","bo_title").
$t->end();

$bankinfo = 
$t->start("cellspacing='5' border='0' width='100%'").
    $t->row(array("<b><i>Please transfer to:</i></b> PT. Indo Human Resource","For and on behalf of"),array("colspan='2' width='60%'","align='center' style='font-size:14px'")).
    $t->row(array("<b>IDR Account :</b>","- Bank BNI, Cabang Menteng","PT. Indo Human Resource"),array("valign='bottom'","valign='bottom'","align='center' style='font-size:18px'")).
    $t->row(array("","&nbsp;&nbsp;<b>A/C. 216.082.0050<b>")).
    $t->row(array("","- Bank Mandiri, Cabang Krakatau Steel")).
    $t->row(array("","&nbsp;&nbsp;<b>A/C. 070.000555.9807<b>")).
    $t->row(array("","- Bank BCA, Cabang Epicentrum Walk")).
    $t->row(array("","&nbsp;&nbsp;<b>A/C. 505.5016.515<b>")).
    $t->row(array("","- Bank Muamalat, Cabang Tanjung Priok")).
    $t->row(array("","&nbsp;&nbsp;<b>A/C. 000.182.0564<b>")).
    $t->row(array("","- Bank CIMB Niaga Unit Syariah")).
    $t->row(array("","&nbsp;&nbsp;<b>A/C. 502.01.00158.009<b>")).
    $t->row(array("USD $ Account :","- Bank Mandiri, Jakarta Rasuna Epicentrum Branch")).
    $t->row(array("","&nbsp;&nbsp;<b>A/C. 124-00-0330033-3<b>","Finance & Accounting Department"),array("","","align='center' style='font-size:14px'")).
$t->end();

echo
$t->start("frame='box' width='800' style='margin-bottom:10px;padding:15px' cellspacing='0'","inv").
    $t->row(array("<img src='images/corphr.png' height='100'/>",$profil),array("align='center' style='padding: 30px 0;'","style='padding: 30px 0'")).
    $t->row(array("NPWP : 02.436.412.7-011.000"),array("colspan='2' style='padding: 15px 0;'"),"","bo_title").
    $t->row(array("INVOICE",""),array("style='border-top:1px solid;border-right:1px solid;'","style='border-bottom: 1px solid black;'"),"","bo_title").
    $t->row(array($client,$datebox),array("width=400' style='padding-left: 50px;'","")).
    $t->row(array($descbox),array("colspan='2'")).
    $t->row(array("&nbsp")).
    $t->row(array($invoice["inwords"]),array("colspan='2' align='middle' style='border:1px solid;padding:30px;background-color: #e5e5e5'"),"").
    $t->row(array("&nbsp")).
    $t->row(array($bankinfo),array("colspan='2'")).
$t->end();
?>
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>