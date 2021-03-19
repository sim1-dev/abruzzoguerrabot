<?php 

//APP VARS

global $username;
$response = '';
$channel_id = "-1001136654503";
$bot_token = "1717660927:AAH-mr5L77Ae2WbHdWDySmabO2hrunsAyLc";

$content = file_get_contents("php://input");
$update = json_decode($content, true);
if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$username = $message["from"]["username"];
$text = isset($message['text']) ? $message['text'] : "";
$text = trim($text);
$text = strtolower($text);
header("Content-Type: application/json");

function sendGETMessage($message) {
	global $bot_token, $chatId;
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chatId&text=$message";
    $options = array(
        'http'=>array(
            'method'=>"POST",
            'header'=>"Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n" .
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
	    )
	);
	$context = stream_context_create($options);
	file_get_contents($url, false, $context);
}

function sendGETMessageToChannel($message) {
	global $bot_token, $channel_id;
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$channel_id&text=$message";
    $options = array(
        'http'=>array(
            'method'=>"POST",
            'header'=>"Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n" .
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
	    )
	);
	$context = stream_context_create($options);
	file_get_contents($url, false, $context);
}


//ADMIN COMMANDS SECTION

if(strpos($text, "/start") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        sendGETMessageToChannel("admin test");
    } else {
        sendGETMessage("test");
    }
}

if(strpos($text, "/avvia") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        if($_ENV["APP_RUNNING"] == 0) {
            sendGETMessage("[OK] Guerra avviata!");
        } else {
            sendGETMessage("[NO] Guerra già in esecuzione!");
        }
    } else {
        sendGETMessage("[ER] Non hai accesso a questo comando.");
    }
}

?>

