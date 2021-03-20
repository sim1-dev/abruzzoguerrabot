<?php 

require_once("db.php");
require_once("Settings.php");

//APP VARS

$db_driver = getenv("DB_DRIVER");
$db_host = getenv("DB_HOST");
$db_port = getenv("DB_PORT");
$db_user = getenv("DB_USER");
$db_password = getenv("DB_PASSWORD");
$db_name = getenv("DB_NAME");

global $username, $settings, $active_setting;

$settings = new Settings($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);

$active_setting = $settings->getActiveSetting();

print_r($active_setting);

$response = '';
$channel_id = getenv("CHANNEL_ID");
$bot_token = getenv("BOT_TOKEN");

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
        sendGETMessage($active_setting);
    } else {
        sendGETMessage("test");
    }
}

if(strpos($text, "/broadcast") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        sendGETMessageToChannel(str_replace("/broadcast ","",$text));
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}


if(strpos($text, "/avvia") === 0)
{
    global $username, $active_setting, $settings;
    $app_running = $active_setting["app_running"];
    if($username == "TeamBallo") {
        if($app_running == 0) {
            sendGETMessage($active_setting[0]);
            print_r($active_setting);
            $settings->updateSettingRunning(1, 1);
            sendGETMessage("app_running: ".$app_running);
            sendGETMessage("app_id: ".$active_setting["id"]);
            sendGETMessage("[OK] Guerra avviata!");
        } else {
            sendGETMessage("[NO] Guerra già in esecuzione!");
        }
    } else {
        sendGETMessage("[ER] Non hai accesso a questo comando.");
    }
}

if(strpos($text, "/env") === 0)
{
    global $username, $settings;
    sendGETMessage(getenv("CHANNEL_ID"));
    sendGETMessage(getenv("BOT_TOKEN"));
    sendGETMessage($settings->getActiveSetting()["app_running"]);
}


?>

