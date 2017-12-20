<?php
function wd_remove_accents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#\&[^;]+\;#', '', $str); // supprime les autres caractères
    $str = str_replace("'"," ", $str);
    return $str;
}
	
function maj($str)
{
	 $rstr = "";
	 $noms = explode(" ", $str);
	 for($i=0; $i<= count($noms)-1; $i++) $rstr .= utf8_encode(strtoupper(substr($noms[$i],0,1)).strtolower(substr($noms[$i],1)))." ";
	 return trim($rstr);

}
	
function majFirst($str){
   return strtoupper(substr($str,0,1)).strtolower(substr($str,1));
}

function validDate($str){
	$mydate = '';
	date("d-m-Y", strtotime($str)) != '01-01-1970' ? $mydate = date("d-m-Y", strtotime($str)) : $mydate =  'non indiqué';
	return $mydate;
}

function chkSession($_user){
	if(!$_user->is_loaded()) echo '<script type="text/javascript">window.location.href="./";</script>';
}

function passwordHash($password){
	//pour l'instant on ne fait rien
	return $password;
}

function getTelco($phone){
	$telco = '';//'<span class="label">Inconnu</span>';
	$togocel = '<span class="label label-warning">Togocel</span>';
	$moov = '<span class="label label-success">Moov</span>';
	switch(substr($phone,3,2)){
		case '92':
		case '91':
		case '90': $telco = $togocel; break;
		case '99':
		case '98': $telco = $moov; break;
	}
	return $telco;
}

 function array_to_CSV($data)
    {
        $outstream = fopen("php://temp", 'r+');
        fputcsv($outstream, $data, ',', '"');
        rewind($outstream);
        $csv = fgets($outstream);
        fclose($outstream);
        return $csv;
    }

function CSV_to_array($data)
    {
        $instream = fopen("php://temp", 'r+');
        fwrite($instream, $data);
        rewind($instream);
        $csv = fgetcsv($instream, 9999999, ',', '"');
        fclose($instream);
        return($csv);
    }

function at_array_to_CSV($data){
	
	// Create a stream opening it with read / write mode
	$stream = fopen('data://text/plain,' . "", 'w+');

	// Iterate over the data, writting each line to the text stream
	foreach ($data as $val) {
	fputcsv($stream, $val);
	}

	// Rewind the stream
	rewind($stream);

	// You can now echo it's content
	echo stream_get_contents($stream);

	// Close the stream 
	fclose($stream);
}

$CHANNEL4 = array(
	'channelUser' => '',
	'channelPass' => '',
	'channelMethod' => 'GET',
	'shortCode' => 'ActiveTexto.com',
	'telcoIp' => 'http://rest.nexmo.com',
	'telcoPort' => '80',
	'telcoSentResponse' => '/\"status\":\"0\"/',
	'telcoEndpoint' => '/sms/json?api_key=9fa77929&api_secret=a87c702c&text={MSG}&to={PHONE}&from={CODE}'
);
?>
