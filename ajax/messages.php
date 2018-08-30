<?php 
	include_once "../common.php";
	function getSenderInfo($sender_id){
		global $db;
		if($sender_id > 0){
			$sender = $db->fetch_single_data("users","name",["id"=>$sender_id]);
			$senderMail = $db->fetch_single_data("users","email",["id"=>$sender_id]);
			$arrreturn["name"] = $sender;
			$arrreturn["email"] = $senderMail;
		} else {
			$arrreturn["name"] = "CHR DASHBOARDS SYSTEM";
			$arrreturn["email"] = "";
		}
		return $arrreturn;
	}
	
	$mode = $_GET["mode"];
	if($mode == "checkMessageCount"){
		echo $db->fetch_single_data("messages","count(0)",["user_id2" => $__user_id,"status" => "0"]);
	}
	
	if($mode == "sendMessage"){
		$sender_id = $_GET["sender_id"];
		$message = sanitasi($_GET["message"]);
		if($message != ""){
			$db->addtable("messages");
			$db->addfield("user_id");	$db->addvalue($__user_id);
			$db->addfield("user_id2");	$db->addvalue($sender_id);
			$db->addfield("message");	$db->addvalue($message);
			$db->addfield("created_at");$db->addvalue($__now);
			$db->addfield("created_by");$db->addvalue($__username);
			$db->addfield("created_ip");$db->addvalue($__remoteaddr);
			$db->addfield("status");	$db->addvalue(0);
			$inserting = $db->insert();
			$_GET["id"] = $inserting["insert_id"];
		}
		$mode = "loaddetail";
	}
	
	if($mode == "loadList"){
		$messages = $db->fetch_all_data("messages",[],"(user_id = '".$__user_id."' OR user_id2 = '".$__user_id."') AND id IN (SELECT MAX(id) FROM messages GROUP BY concat(user_id,':',user_id2))","id DESC","1000");
		if(count($messages) > 0){
			$messagesList = array();
			foreach($messages as $message){
				if($message["user_id"] == $__user_id) $sender_id = $message["user_id2"];
				else $sender_id = $message["user_id"];
				if(!$opposites[$sender_id]){
					$opposites[$sender_id] = 1;
					$arrsender = getSenderInfo($sender_id);
					$messagesList[$message["id"]]["id"] = $message["id"];
					$messagesList[$message["id"]]["sender_id"] = $sender_id;
					$messagesList[$message["id"]]["sender_name"] = $arrsender["name"];
					$messagesList[$message["id"]]["sender_email"] = $arrsender["email"];
					$messagesList[$message["id"]]["message"] = $message["message"];
					$messagesList[$message["id"]]["created_at"] = $message["created_at"];
					if($db->fetch_single_data("messages","id",["user_id" => $sender_id,"user_id2" => $__user_id,"status" => "0"]) > 0) $messagesList[$message["id"]]["message"] = "<b><i>".$message["message"]."</i></b>";
				}
			}
				
			foreach($messagesList as $message){
				echo "<table width='100%'>";
		?>
			<tr>
				<td>
					<div class="col-sm-12" style="cursor:pointer;" onclick="window.location='?id=<?=$message["id"];?>';">
						<div class="row equal-heights">
							<div class="col-sm-12">
								<div>
									<b><?=$message["sender_name"];?></b> <?php if($message["sender_id"] > 0){ echo "(".$message["sender_email"].")"; } ?>
									<div style="position:relative;float:right;color:grey;font-size:10px;"><?=format_tanggal($message["created_at"],"dmY",true);?></div>
								</div>
								<div>&nbsp;&nbsp;&nbsp;<?=$message["message"];?> </div>
							</div>
						</div>
					</div>
					<div class="col-sm-12" style="padding-bottom:10px;border-bottom:1px solid #aaa;width:100%;"></div>
				</td>
			</tr>
		<?php
			}
			echo "</table>";
		} else {
			echo "<span class='col-sm-12 well' style='color:red;'>".v("message_not_found")."</span>";
		}
	}
	
	if($mode == "loadconversations"){
		$opposite_id = $_GET["opposite_id"];
		$messages = $db->fetch_all_data("messages",[],"((user_id='$__user_id' AND user_id2='$opposite_id') OR (user_id='$opposite_id' AND user_id2='$__user_id'))","created_at DESC","1000");
		foreach($messages as $message){
			if($message["user_id2"] == $__user_id){
				$class = "bs-callout-success bs-callout-left";
				if($message["status"] == "0"){
					$db->addtable("messages"); $db->where("id",$message["id"]);
					$db->addfield("status");	$db->addvalue(1);
					$db->update();
				}
			} else {
				$class = "bs-callout-info bs-callout-right";
			}
			?>
			<div class="bs-callout <?=$class;?>">
				<div style="position:relative;float:right;color:grey;font-size:10px;top:0px;"><?=format_tanggal($message["created_at"],"dmY",true);?></div>
				<?=$message["message"];?>
			</div>
			<?php
		}
	}
	
	if($mode == "loaddetail"){
		$id = $_GET["id"];
		if($id){
			$message = $db->fetch_all_data("messages",[],"id = '".$id."'")[0];
			if($message["user_id"] == $__user_id) $sender_id = $message["user_id2"];
			else $sender_id = $message["user_id"];
		} else {
			$sender_id = $_GET["sender_id"];
		}
		if($sender_id != ""){
			$arrsender = getSenderInfo($sender_id);
			?>
			<div class="col-sm-12 well">
				<b style="font-size:24px;"><?=$arrsender["name"];?></b>
				<div style="float:right"><input type="button" class="btn btn-warning" value="back" onclick="window.location='<?=$__phpself;?>'"></div>
			</div>
			<br>
			<?php if($sender_id > 0){ ?>
				<div class="row">
					<div class="form-group">
						<div class="col-sm-10">
							<input type="text" class="form-control" id="txtmessage" onkeyup="if(event.keyCode == '13'){ sendMessage('<?=$sender_id;?>',txtmessage.value); }">
						</div>
						<div class="col-sm-1">
							<button class="btn info" onclick="sendMessage('<?=$sender_id;?>',txtmessage.value);">Send</button>
						</div>
						<div class="col-sm-1"></div>
					</div>
				</div>
			<?php } else { ?>
				<br><br>
			<?php } ?>
			<div id="conversations"></div>
			<script>
				function refreshMessage(){
					loadConversations('<?=$sender_id;?>');
					setTimeout(function(){ refreshMessage(); }, 1000);
				}
				setTimeout(function(){ refreshMessage(); }, 1000);
			</script>
			<?php
		} else {
			?> <script> loadMessages(); </script> <?php
		}
	}
?>