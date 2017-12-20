<?php
/*
  * Sms class pour envoyer des SMS
  */
 
class Sms{


 private static function encodeURL($phone,$msg,$channel){
	$URL = $channel['telcoEndpoint'];
	$URL = str_replace('{USER}',$channel['channelUser'],$URL);
	$URL = str_replace('{PASS}',$channel['channelPass'],$URL);
	$URL = str_replace('{CODE}',$channel['shortCode'],$URL);
	$URL = str_replace('{PHONE}',$phone,$URL);
	$URL = str_replace('{MSG}',rawurlencode($msg),$URL);
	//echo "GET ".$URL."&charset=UTF-8 HTTP/1.0\n";
	return "GET ".$URL."&charset=UTF-8 HTTP/1.0\n";
 }	
	
 private static function readSocket($so){
 	self::$result = '';
 	while(!feof($so)) 
     {
        self::$result .= fread($so,1);
     }
	 //echo self::$result;
     return self::$result;
 }
 
 public static function sent($soResult,$ok){
	//echo $soResult;
 	if(preg_match($ok,$soResult))
 	return true;
 	else
 	return false;
 }
 
 public static function getResponse(){
	
 	return self::$result;;
 }


 public static function SendSMS ($phoneNoRecip, $msgText, $channel) {

     if(!isset($channel['telcoIp']) || !isset($channel['telcoPort'])) return false;

     $fp = fsockopen($channel['telcoIp'], $channel['telcoPort'], $errno, $errstr);
     if (!$fp) 
     {echo 'connected';
        echo "errno: $errno \n";
        echo "errstr: $errstr\n";
        //return $result;
     }
     fwrite($fp, self::encodeURL(trim($phoneNoRecip,"+"),$msgText, $channel));
     fwrite($fp, "\n");
     
     $result = self::readSocket($fp);
     fclose($fp);
	echo $result;
     //return $result;
  }

  private static $result;
}
//$x = Sms::sendSMS('22891911307','timtim');
