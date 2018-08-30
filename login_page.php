<?php include_once "head.php";?>

<table width="100%"><tr><td align="center">
<fieldset style="width:300px;height:180px;padding-top:20px" class="frontRegisterArea">
<div class="bo_title">Login</div>
<?php
	$txt_username = $f->input("username");
	$txt_password = $f->input("password","","type='password'");
?>
<?=$f->start();?>
	<div class="whitecard" style="margin:10px;">
	<?=$t->start("","","login_form_area");?>
		<?=$t->row(array("Username",$txt_username),array("id='td1'"));?>
		<?=$t->row(array("Password",$txt_password),array("id='td1'"));?>
	<?=$t->end();?>
	</div>
	<?=$f->input("login_action","Login","type='submit'","btn_sign");?>
<?=$f->end();?>

</fieldset>
</td></tr></table>

<?php include_once "footer.php";?>