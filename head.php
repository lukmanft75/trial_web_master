<?php include_once "common.php"; ?>
<?php include_once "log_action.php"; ?>
<?php $_rowperpage = 200; ?>
<?php $_max_counting = 100000; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php if(!$_isexport){ ?>
	<html>
		<head>
			<meta property="og:image" content="images/corphr.png">
			<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
			<meta http-equiv="X-UA-Compatible" content="IE=8;" />
			<link rel="Shortcut Icon" href="favicon.ico">
			<title id="titleid"><?=$__appstitle;?></title>
			
			<script src="scripts/jquery-1.10.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.fancybox.js"></script>
			<script type="text/javascript" src="calendar/calendar.js"></script>
			<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
			<script type="text/javascript" src="calendar/calendar-setup.js"></script>
			<script src="scripts/bootstrap.min.js"></script>
			<script src="scripts/toastr.min.js"></script>
			<script src="scripts/bootstrap-slider.js"></script>

			<link rel="stylesheet" type="text/css" href="styles/style.css">
			<link rel="stylesheet" type="text/css" href="backoffice.css">
			<link rel="stylesheet" type="text/css" href="calendar/calendar-win2k-cold-1.css">
			<link rel="stylesheet" type="text/css" href="styles/jquery.fancybox.css" media="screen" />
			<link rel="stylesheet" type="text/css" href="font/font.css">
			<link rel="stylesheet" type="text/css" href="styles/bootstrap.min.css">
			<link rel="stylesheet" type="text/css" href="styles/bootstrap-slider.css">
			<link rel="stylesheet" type="text/css" href="styles/animate.css">
			<link rel="stylesheet" type="text/css" href="styles/toastr.min.css">
			<link rel="stylesheet" type="text/css" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
			<script>
				Number.prototype.formatMoney = function(c, d, t){
				var n = this, 
					c = isNaN(c = Math.abs(c)) ? 2 : c, 
					d = d == undefined ? "." : d, 
					t = t == undefined ? "," : t, 
					s = n < 0 ? "-" : "", 
					i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
					j = (j = i.length) > 3 ? j % 3 : 0;
				   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
				 };
				var global_respon = new Array();
				var toastroptions = {
					"closeButton": true,
					"debug": false,
					"newestOnTop": false,
					"progressBar": true,
					"positionClass": "toast-bottom-right",
					"preventDuplicates": false,
					"showDuration": "300",
					"hideDuration": "1000",
					"timeOut": "5000",
					"extendedTimeOut": "1000",
					"showEasing": "swing",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut"
				}
				
				var toastrMessage = {
					"closeButton": true,
					"debug": false,
					"newestOnTop": false,
					"progressBar": true,
					"positionClass": "toast-message-icon",
					"preventDuplicates": false,
					"showDuration": "300",
					"hideDuration": "1000",
					"timeOut": "15000",
					"extendedTimeOut": "1000",
					"showEasing": "swing",
					"hideEasing": "linear",
					"showMethod": "fadeIn",
					"hideMethod": "fadeOut"
				}
				
				function adding_row(detail_area,firstrow_area){
					var elm_detail_area = document.getElementById(detail_area);
					var elm_firstrow_area = document.getElementById(firstrow_area+"0").innerHTML;
					var numrow = elm_detail_area.childElementCount;
					var number = numrow + 1;
					while(true){
						elm_firstrow_area = elm_firstrow_area.replace("[0]","["+ numrow +"]"); 
						if(elm_firstrow_area.indexOf("[0]") <= 0) break;
					}
					elm_firstrow_area = elm_firstrow_area.replace("<div id=\"firstno\">1</div>","<div id=\"firstno\">"+ number +"</div>");
					
					//grab all value
					var values = [[]];
					var checkeds = [[]];
					var inputtype = ["input","select","textarea"];
					for(ii = 0; ii < inputtype.length; ii++){
						var inputs = document.getElementById(detail_area).getElementsByTagName(inputtype[ii]);
						for (var index = 0; index < inputs.length; index++) { 
							if (!values[inputtype[ii]]) values[inputtype[ii]] = [];
							if(inputs[index].type != "checkbox"){
								values[inputtype[ii]][index] = inputs[index].value;
							} else {
								values[inputs[index].id] = inputs[index].value;
								checkeds[inputs[index].id] = inputs[index].checked;
							}
						}
					}
					
					elm_detail_area.innerHTML += "<tr id='"+firstrow_area+ numrow +"'>"+elm_firstrow_area+"</tr>";
					
					//fill value
					for(ii=0;ii<inputtype.length;ii++){
						var inputs = document.getElementById(detail_area).getElementsByTagName(inputtype[ii]);
						for (index = 0; index < inputs.length; index++) {
							if(inputs[index].type != "checkbox"){
								inputs[index].value = values[inputtype[ii]][index] || "";
							} else {
								inputs[index].value = values[inputs[index].id] || "1";
								inputs[index].checked = checkeds[inputs[index].id];
							}
						}
					}
				}
				
				function substract_row(detail_area,firstrow_area){
					var elm_detail_area = document.getElementById(detail_area);
					var numrow = elm_detail_area.childElementCount - 1;
					try { var elm_firstrow_area = "<tr id=\""+firstrow_area+ numrow +"\">" + document.getElementById(firstrow_area+numrow).innerHTML + "</tr>"; } catch(ex){}
					try { var elm_firstrow_area = "<tbody><tr id=\""+firstrow_area+ numrow +"\">" + document.getElementById(firstrow_area+numrow).innerHTML + "</tr></tbody>"; } catch(ex){}
					
					//grab all value
					var values = [[]];
					var checkeds = [[]];
					var inputtype = ["input","select","textarea"];
					for(ii = 0; ii < inputtype.length; ++ii){
						var inputs = document.getElementById(detail_area).getElementsByTagName(inputtype[ii]);
						for (var index = 0; index < inputs.length; ++index) {
							if(inputs[index].type != "checkbox"){
								if (!values[inputtype[ii]]) values[inputtype[ii]] = []
								values[inputtype[ii]][index] = inputs[index].value;
							} else {
								values[inputs[index].id] = inputs[index].value;
								checkeds[inputs[index].id] = inputs[index].checked;
							}
						}
					}
					
					elm_detail_area.innerHTML = elm_detail_area.innerHTML.replace(elm_firstrow_area,"");
					
					//fill value
					for(ii=0;ii<inputtype.length;ii++){
						var inputs = document.getElementById(detail_area).getElementsByTagName(inputtype[ii]);
						for (index = 0; index < inputs.length; ++index) {
							if(inputs[index].type != "checkbox"){
								inputs[index].value = values[inputtype[ii]][index] || "";
							} else {
								if (values[inputs[index].id] === undefined) values[inputs[index].id] = "1";
								if (checkeds[inputs[index].id] === undefined) values[inputs[index].id] = true;
								inputs[index].value = values[inputs[index].id];
								inputs[index].checked = checkeds[inputs[index].id];
							}
						}
					}
				}
				
				function get_ajax(x_url,target_elm,done_function){
					$( document ).ready(function() {
						$.ajax({url: x_url, success: function(result){
							try{ $("#"+target_elm).html(result); } catch(e){}
							try{ $("#"+target_elm).val(result); } catch(e){}
							try{ global_respon[target_elm] = result; } catch(e){}
							try{ eval(done_function || ""); } catch(e){}
						}});
					});
				}
				
				function popup_message(message,mode,actionAfterClose){
					mode = mode || "";
					actionAfterClose = actionAfterClose || "";
					$.fancybox.open({
						content:"<div style='overflow:auto;'><table class='popup_message "+mode+"'><tr><td>"+message+"</td></tr></table></div>",
						afterClose: function(){ try{ eval(actionAfterClose); } catch(e){} }
					});
				}
				
				function toogle_bo_filter(){
					var bo_filter_container = document.getElementById('bo_filter_container'),
					style = window.getComputedStyle(bo_filter_container),
					bo_filter_container_display = style.getPropertyValue('display');
					if(bo_filter_container_display == "none") {
						bo_filter_container.style.display="block";
						bo_expand.innerHTML="[-] Hide Filter";
					}
					if(bo_filter_container_display == "block") {
						bo_filter_container.style.display="none";
						bo_expand.innerHTML="[+] View Filter";
					}
				}
				
				function changepage(numpage){
					page.value = numpage;
					filter.submit();
				}

				function sorting(field){
					var current_sort = sort.value;
					current_sort = current_sort.replace(" DESC","");
					if((current_sort == field && sort.value.indexOf(" DESC") > 0) || current_sort != field){
						sort.value = field;
					} else {
						sort.value = field + " DESC";
					}
					filter.submit();
				}
				
				function openwindow(url){ window.open(url,"","width=1100,height=800,scrollbars=yes"); }
				
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
				
				function loadNotifMessageCount(count){
					try{
						if(count != 0){
							$("#notifNavCount").html(count);
							$("#notifNavCount").attr("style","visibility:visible");
							$("#titleid").html("<?=substr($__appstitle,0,9)."...";?> ("+count+")");
						} else {
							$("#notifNavCount").html("");
							$("#notifNavCount").attr("style","visibility:hidden");
							$("#titleid").html("<?=$__appstitle;?>");
						}
					 } catch(e){}
				}
				
				function showToastrMessage(){
					<?php if($__phpself != "messages.php"){ ?>
					toastr.success("<b>Klik icon biru di atas (sebelah kanan nama Anda), atau pilih menu 'General' -> 'Message' untuk melihat Pesan</b>","",toastrMessage);
					<?php } ?>
				}
			</script>
		</head>
		<body id="bodyid" style="margin:0px;">
			<?php include_once "menu.php";?>
			<?php
				if($__isloggedin){
			?>
				<script> $("body").css({"background-color":"white"});</script>
				<div style="width:95%;margin-top:20px;margin-left:20px;margin-right:20px;">
				<?php if(!$__is_allowed){ ?>
					Forbidden Page!
				<?php 
						include_once "footer.php";
						exit(); 
					} 
				?>
			<?php 
				} else {
					?>
					<script> $("body").css({"background-color":"white"});</script>
					<div style="width:95%;margin-top:20px;margin-left:20px;margin-right:20px;">
						<?php include_once "login_page.php";?>
					</div>
					<?php
					include_once "footer.php";
					exit();
				}  
			?>
			
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content" style="top:100px;">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title" id="modalTitle"></h4>
						</div>
						<div class="modal-body" id="modalBody"></div>
						<div class="modal-footer" id="modalFooter"></div>
					</div>
				</div>
			</div>
<?php } ?>