<?php
error_reporting(-1);
ini_set('display_errors', 'On');


require 'functions.php';

$authed = wordcamper_auth_user_esi();

if($authed) {
	$result = wordcamper_esi_get_api_response('logout_form');
	if(isset($result->logout_form)) {
		echo $result->logout_form;
	}
}
else {
	$result = wordcamper_esi_get_api_response('login_form');
	if(isset($result->login_form)) {
		echo $result->login_form;
	}
}

echo rand(0,200);
