<?php
	session_start();
	session_unset();
	session_destroy();
	
	setcookie('Authorization', null, -1, '/'); 
	setcookie('Token', null, -1, '/'); 
	setcookie('JSESSIONID', null, -1, '/'); 

	Header("Location: index.html");
	
?>
