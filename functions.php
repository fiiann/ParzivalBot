<?php
function sendMessage ($chatId, $message) {
	$url = $GLOBALS['website']."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
	file_get_contents($url);
}
function sendKeyboard($chatId, $message, $keyboard){
	$url = $GLOBALS['website']."/sendMessage";
	$postfields = array(
	'chat_id' 		=> "$chatId",
	'text' 			=> "$message",
	'reply_markup' 	=> json_encode($keyboard));

	if (!$curld = curl_init()) {exit;};

	curl_setopt($curld, CURLOPT_POST, true);
	curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($curld, CURLOPT_URL,$url);
	curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);

	$output = curl_exec($curld);
	curl_close ($curld);
}

function isInArray($key, $array){
	foreach($array as $key1=>$value){
		if($key1==$key) return true;
	}
	return false;
}
?>