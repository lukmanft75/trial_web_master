<?php 
	include_once "common.php"; 
	
	$exp = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
	$emails = $db->fetch_all_data("candidates",["email"],"email LIKE '%@%.%' group by email order by email");
	foreach($emails as $email){
		$email = trim($email[0]);
		$email = str_replace(" ","",$email);
		if(strpos(" ".$email,"/") > 0){
			$_email = explode("/",$email);
			foreach($_email as $key => $email){
				if(preg_match($exp,$email)) echo $email."<br>";
			}
		} else if(strpos(" ".$email,",") > 0){
			$_email = explode(",",$email);
			foreach($_email as $key => $email){
				if(preg_match($exp,$email)) echo $email."<br>";
			}
		} else if(strpos(" ".$email,";") > 0){
			$_email = explode(";",$email);
			foreach($_email as $key => $email){
				if(preg_match($exp,$email)) echo $email."<br>";
			}
		} else {
			if(preg_match($exp,$email)) echo $email."<br>";
		}
	}
?>
