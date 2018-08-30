<?php include_once "head.php";?>
<div class="bo_title">REPORTS</div>
<?=$f->start("","POST","finance_report_view.php","target='_blank'");?>
	<?=$t->start("","editor_content");?>
        <?=$t->row(["Template",$f->select("template_id",$db->fetch_select_data("finance_report_template","id","name"))]);?>
        <?=$t->row(["Periode",$f->input("periode_1",date("Y-01-01"),"type='date'")." - ".$f->input("periode_2",date("Y-12-31"),"type='date'")]);?>
        <!--<?=$t->row(["Periode Count",$f->select("periode_count",["1"=>"1","2"=>"2","3"=>"3"])]);?>
        <?=$t->row(["Periode Loop",$f->select("periode_loop",["yearly"=>"Yearly","monthly"=>"Monthly"])]);?>-->
	<?=$t->end();?>
	<?=$f->input("load","Load","type='submit'");?>
<?=$f->end();?>
<?php include_once "footer.php";?>