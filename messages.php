<?php include_once "head.php";?>
<div class="col-sm-12 well"><b style="font-size:24px;">Messages</b></div>
<?php 
	if($_GET["sender_id"] == "" && $_GET["id"] == ""){ 
		echo "<div class='col-sm-12'>";
		echo "<div class='col-sm-2'>Send Message To : </div>";
		echo "<div class='col-sm-4'>";
		$users = $db->fetch_select_data("users","id","concat(name,' [',email,']')",["id" => "1:>"],["name"],"",true);
		echo $f->select("sender_id",$users,"","onchange=\"window.location='?sender_id='+this.value\"");
		echo "</div>";
		echo "</div>";
		echo "<div class='col-sm-12'><hr></div>";
	}
?>
<div class="col-sm-12">
	<?php if($__isloggedin){ ?>
		<div id="dashboard_messages"></div>
		<script>
			function sendMessage(sender_id,textmessage){
				$.ajax({url: "ajax/messages.php?mode=sendMessage&sender_id="+sender_id+"&message="+textmessage, success: function(result){
					$("#dashboard_messages").html(result);
				}});
			}
			function loadConversations(opposite_id){
				$.ajax({url: "ajax/messages.php?mode=loadconversations&opposite_id="+opposite_id, success: function(result){
					$("#conversations").html(result);
				}});
			}
			function loadDetailMessage(id,sender_id){
				id = id || "";
				sender_id = sender_id || "";
				$.ajax({url: "ajax/messages.php?mode=loaddetail&id="+id+"&sender_id="+sender_id, success: function(result){
					$("#dashboard_messages").html(result);
				}});
			}
			function loadMessages(){
				$.ajax({url: "ajax/messages.php?mode=loadList", success: function(result){
					$("#dashboard_messages").html(result);
					<?php if($_GET["id"] == "" && $_GET["sender_id"] == ""){ ?> setTimeout(function(){ loadMessages(); }, 3000);  <?php } ?>
				}});
			}
			<?php if($_GET["id"] == "" && $_GET["sender_id"] == ""){ ?> loadMessages(); <?php } else { ?> loadDetailMessage("<?=$_GET["id"];?>","<?=$_GET["sender_id"];?>"); <?php } ?>
		</script>
	<?php } ?>
</div>
<br><br>
<?php include_once "footer.php";?>