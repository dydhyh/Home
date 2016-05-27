<?php  
	require_once('./conf/curl_xunlei.php');
	$account = new thunder();
	$account->get_account();
	$account->show_account();
?>