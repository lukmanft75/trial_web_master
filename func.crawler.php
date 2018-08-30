<?php
	$postdata['ctl00$ctl00$ctl00$MainContent$MainContent$MainContent$LoginControl$UserName'] = "billings@corphr.com";
	$postdata['ctl00$ctl00$ctl00$MainContent$MainContent$MainContent$LoginControl$Password'] = "bismillaahi123";
	$postdata['ctl00$ctl00$ctl00$MainContent$MainContent$MainContent$LoginControl$LoginButton'] = "Sign in";
	$postdata['__VIEWSTATE'] = "c+QDH/toHJPRAWqMww/sk5GWZOIUt+pyu2dM2CjOhldyu5PGx5VtxDNowLcr3gWryTyBtrA5MbjioDDAE3wSXCyqNCppbagIPBygReEOwg1MbeErGoP9FezUC4QX4Imfg2CncWm5rD6iv9T9iJ8hYH5dockONK0DG+03L33GkoRl0Wqjv3VhtFVIPLxxBUEsQIhhYupTiZ35IiHj2LNgW987qzRDaTOSukHz3WHrO+EIbGsXgOMAiN4QfVuVaayBQkhizkSX1I1eN42Fxj53o43YCM4Fk8rGqfS6QH/eGwh/uM0bjKUxnnV5IBGqcn5NEx5Sd04tequcVS7P0qPu7sa3lZMDW8lkXKzW1j8i+IEYmG4DyN6BU7aBeUBNOD83xX+2RbKriv9UKJHKyZsVY2K3NtxRT1/mLA3hmNSyMGWIUuqWI9SKiey4wZcQpR3L0A3HD4rE9AV0VT2i8yGDfmPiIb6GoRIsjx1Irkig4I+CJzsZ";
	$postdata['__EVENTVALIDATION'] = "RF9X1eSW/kiz5wRk+bJe5VBdMzB+UlksU5CR6uy0uekcZBrCmPwIj9jOaqWMZYrx7suq3omU9oW8RIkH0F2a4CUmhG8xGSMXrgNbvPb5HNuKiGE+cW1IX1IKFKGriEEZaMi9UJ7C5p+IXL6GihOzidB5uDtWsDsPzxKDxB2q6GKgpR7vLJIZN2g8hRNHmxcjKqCfOQ==";
	 
	$url="https://apportal.nokia.com/APPortalExt/Login.aspx"; 
	$cookie="cookie.txt"; 

	$postdata = http_build_query($postdata);

	$ch = curl_init(); 
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie); 
	curl_setopt ($ch, CURLOPT_REFERER, $url); 
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
	curl_setopt ($ch, CURLOPT_POST, 1); 
	$result = curl_exec ($ch); 
	
	$url="https://apportal.nokia.com/APPortalExt/SelectCorporation.aspx";
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
	// curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
	// curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie); 
	curl_setopt ($ch, CURLOPT_REFERER, "https://apportal.nokia.com/APPortalExt/Login.aspx"); 
	$result = curl_exec ($ch); 
	
	
	$url="https://apportal.nokia.com/APPortalExt/Frontpage.aspx";
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
	// curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
	// curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie); 
	curl_setopt ($ch, CURLOPT_REFERER, "https://apportal.nokia.com/APPortalExt/SelectCorporation.aspx"); 
	$result = curl_exec ($ch); 

/* 	$url="https://apportal.nokia.com/APPortalExt/pos/";
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); 
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie); 
	curl_setopt ($ch, CURLOPT_REFERER, $url); 

	curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
	curl_setopt ($ch, CURLOPT_POST, 1); 
	$result = curl_exec ($ch);  */
	
	echo $result;  
	curl_close($ch);
?>
