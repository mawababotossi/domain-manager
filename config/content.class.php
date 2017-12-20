<?php

class Content{
 
 public static $map;
 public static $modulesPath = 'modules/';
 
 
 public static function init(){
  $toLoad = null;
if(0===strpos(self::getRequestSection(),'_')){
   $toLoad = '_map'.self::getRequestSection();
  }else{
   $toLoad = 'map'.self::getRequestSection();
  }
  self::loadMap($toLoad);
 }
 
 public static function loadMap($_map){
 $defaultMap = self::getDefaultMap();
 $diff       = self::$_map();
 
 foreach($defaultMap as $position=>$module){
    if(isset($diff[$position]) ) $defaultMap[$position] = $diff[$position];//&& $diff[$position]!=''
   }
 
    self::$map = $defaultMap;
	//print_r($defaultMap);
 }
 
 
 public static function getModule($_position){
  if(isset(self::$map[$_position]) && self::$map[$_position]!='')  return self::$modulesPath.self::$modules[self::$map[$_position]];
  else return null;
    
 }
 
 public static function isOn($_position){
  if(isset(self::$map[$_position] ) && self::$map[$_position]!='')  return true;
  else return false;
 }
 
 public static function getRequestSection(){
    if(isset($_GET['Qp'])) 
	   return $_GET['Qp'];
	else
       return 'Accueil';	
 }
 
 public function __call($method, $args){echo $method; return;}
 
 public static $modules = array(
    	'topbar'		=>'topbar.php',
    	'user_menu'       	=>'user_menu.php',
	'splash'       		=>'splash.php',
	'signup'       		=>'signup.php',
	'app_sendsms'       	=>'app_sendsms.php',
	'app_addfriend'       	=>'app_addfriend.php',
	'myaccount'       	=>'myaccount.php',
		
 );
 
 /**
  * DEFAULT MAP
  */
  
 public static function getDefaultMap(){
 
 return array(
 	'topbar'		=>'topbar',
 	'first'			=>'',
 	'left1'			=>'',
 	'right1'		=>'',
 	'right2'		=>'',
	);
}
	
 public static function mapAccueil(){
 return array(
	'mapName'		=>'Accueil',
	'first'			=>'splash',
	);
}
	
 public static function mapMe(){
 return	array(
	'mapName'		=>'MonCompte',
 	'left1'			=>'user_menu',
	'right1'		=>'app_sendsms',
	'right2'		=>'app_addfriend',
	);
}

public static function mapSignup(){
 return array(
	'mapName'		=>'Signup',
	'first'			=>'signup',
	);
}

public static function mapMyAccount(){
 return array(
	'mapName'		=>'Myaccount',
	'first'			=>'myaccount',
	);
}
	

}

?>
