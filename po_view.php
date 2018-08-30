<?php include_once "head.php";

	$db->addtable("po");$db->where("id",$_GET["id"]);$db->limit(1);$po = $db->fetch_data();
    $db->addtable("po_detail");$db->where("id",$_GET["id"]);$db->limit(1);$po_detail = $db->fetch_data();

?>
<style>
.t1{border-top:1px solid black;}
</style>
<div class="bo_title">View Purchase Order <?=$po["num"];?></div>    
<div class="bo_title">NOKIA</div>
<table frame='box'>
<tr><td>
<?php
    echo
    $t->start("width='800' cellpadding='3' style='padding: 10px;'","po").
        $t->row(array("Purchase Doc. Number:",$po["num"],"Supplier Name:","PT. INDO HUMAN RESOURCE")).
        $t->row(array("Doc. Date:",$po["doc_date"],"Supplier Number:","0000821932")).
        $t->row(array("Delivery Address:","{delivery_address}","Supplier Contact:","{supplier_contact}")).
        $t->row(array("Terms of Delivery:","{terms_of_delivery}","NSN Contact:","{nsn_contact}")).
        $t->row(array("Payment Terms:","{payment_terms}","Phone Number","{phone_number}")).
        $t->row(array("","","Email:","{email}")).
        $t->row(array("","","Total Value:",$po["total"])).
        $t->row(array("","","Mark By:","{mark_by}")).
        $t->row(array("","","Mode:","{mode}")).
        $t->row(array("Invoicing details:",$po["description"]),array("","colspan='3'")).
        $t->row(array("Free Text:","{free_text}"),array("","colspan='3'")).
    $t->end();
    
?>
</td></tr>
<tr><td>
<?php
    echo
    $t->start("width='800' border='0' cellpadding='3' style='padding: 10px;","po").
        $t->row(array("&nbsp;"),array("colspan='9' style='border-bottom:1px solid black';")).
        $t->row(array("Line Status","Line","Material Code","Description","Plant","Supplier Material Code"),array("","","","colspan='2' align='center'","colspan='2' align='center'","colspan='2' align='center'")).
        $t->row(array("PCI","Deliv. Date","Qty","Unit","Net Price","Per","Unit","Total Price","Currency")).
        $t->row(array("&nbsp;"),array("colspan='9' style='border-top:1px solid black';")).
        $t->row(array("{line_status}","{line}","{material_code}","{description}","{plant}","{supplier_material_code}"),array("","","","colspan='2' align='center'","colspan='2' align='center'","colspan='2' align='center'")).
        $t->row(array("{pci}","{deliv_date}","{qty}","{unit}","{net_price}","{per}","{unit}","{total_price}","{currency}")).        
    $t->end();
?>
</td></tr>
</table>
<br />
<?=$f->input("back","Back","type='button' onclick=\"window.location='".str_replace("_view","_list",$_SERVER["PHP_SELF"])."';\"");?>
&nbsp;
<?=$f->input("edit","Edit","type='button' onclick=\"window.location='".str_replace("_view","_edit",$_SERVER["PHP_SELF"])."?id=".$_GET["id"]."';\"");?>
<?php include_once "footer.php";?>
