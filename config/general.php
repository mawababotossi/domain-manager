<?php
//$RACINE   = "http://". $_SERVER['HTTP_HOST'] ."/soo/prefix";
$RACINE   = "http://". $_SERVER['REMOTE_ADDR'] ."/promotg";
$TEMPLATE = "urban";
$ZIPCODE = isset($_REQUEST['zipcode'])? $_REQUEST['zipcode'] : '228';

$CHANNEL_SMS256 = array(
	'channelUser' => 'abiyo27',
	'channelPass' => '74r7udas7u7ud@sx',
	'shortCode' => '4001',
	'telcoIp' => '193.105.74.59',
	'telcoPort' => '80',
	'telcoSentResponse' => '/[0-9]{10,}/',
	'telcoEndpoint' => '/api/sendsms/plain?user={USER}&password={PASS}&sender={CODE}&SMSText={MSG}&GSM={PHONE}'
);

$CHANNEL_NEXMO = array(
	'channelUser' => '',
	'channelPass' => '',
	'channelMethod' => 'GET',
	'shortCode' => 'ActiveTexto.com',
	'telcoIp' => 'http://rest.nexmo.com',
	'telcoPort' => '80',
	'telcoSentResponse' => '/\"status\":\"0\"/',
	'telcoEndpoint' => '/sms/json?api_key=9fa77929&api_secret=a87c702c&text={MSG}&to={PHONE}&from={CODE}'
);

$CHANNEL = $CHANNEL_NEXMO;
?>
