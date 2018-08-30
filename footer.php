<?php if(!$_isexport){ ?>
	</div>
			<br>
			<br>
			<br>
			<div class="col-sm-12 footer_area" id="footer_area">
				<div>
					<br>
					<?=$t->start("width='900'");?>
						<?=$t->row(array("COPYRIGHT &copy; ".date("Y")." Indo Human Resource. ALL RIGHTS RESERVED."),array("align='middle' style='color:#333;'"));?>
					<?=$t->end();?>
					<br>
				</div>
			</div>
			<script>
			<?php if($__isloggedin){ ?>
				function checkMessageCount(){
					$.ajax({url: "ajax/messages.php?mode=checkMessageCount", success: function(result){
						loadNotifMessageCount(result);
					}});
					setTimeout(function(){ checkMessageCount(); }, 1000); 
				}
				checkMessageCount();
				// try{ setTimeout(function(){ if($("#notifNavCount").css("visibility") == "visible") showToastrMessage(); }, 3000); } catch(e){} 
			<?php } ?>
			
			function session_checker(){
				$.ajax({url: "ajax/session_checker.php", success: function(result){
					if(result != "<?=$__isloggedin;?>"){
						window.location="index.php";
					}
				}});
				setTimeout(function(){ session_checker(); }, 20000);
			}
			$( document ).ready(function() { 
				setTimeout(function(){ session_checker(); }, 20000); 
			});

			<?php if(isset($_SESSION["message"]) && $_SESSION["message"] != ""){ ?> 
				toastr.success("<?=$_SESSION["message"];?>","",toastroptions);
				<?php $_SESSION["message"] = ""; ?>
			<?php } ?>
			
			<?php if(isset($_SESSION["errormessage"]) && $_SESSION["errormessage"] != ""){ ?> 
				toastr.warning("<?=$_SESSION["errormessage"];?>","",toastroptions);
				<?php $_SESSION["errormessage"] = ""; ?>
			<?php } ?>
		</script>
		</body>
	</html>
<?php } ?>