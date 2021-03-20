<?php 

require_once("db.php");
require_once("Settings.php");
require_once("Municipalities.php");

//APP VARS

$db_driver = getenv("DB_DRIVER");
$db_host = getenv("DB_HOST");
$db_port = getenv("DB_PORT");
$db_user = getenv("DB_USER");
$db_password = getenv("DB_PASSWORD");
$db_name = getenv("DB_NAME");

global $username, $settings, $active_setting;

$settings = new Settings($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);
$municipalities = new Municipalities($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);

$active_setting = $settings->getActiveSetting();

$response = '';
$channel_id = (string)getenv("CHANNEL_ID");
$bot_token = (string)getenv("BOT_TOKEN");

$content = file_get_contents("php://input");
$update = json_decode($content, true);
if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$username = (string)$message["from"]["username"];
$text = isset($message['text']) ? $message['text'] : "";
$text = trim($text);
$text = strtolower($text);
header("Content-Type: application/json");

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

if(strpos($text, "/comunerandom") === 0) //TEST
{
    global $username, $municipalities;
    if($username == "TeamBallo") {
        $random = $municipalities->getRandomMunicipality();
        sendGETMessage($random["name"]);
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}

/*if(strpos($text, "/morti") === 0) //TEST
{
    global $username, $municipalities;
    if($username == "TeamBallo") {
        $random = sizeOf($municipalities->getDeadMunicipalities());  //EMPTY = INT
        foreach($random as $dead) {
            $message.= $dead["name"]."   ";
        }
        sendGETMessage($message);
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}*/

if(strpos($text, "/broadcast") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        sendGETMessageToChannel(str_replace("/broadcast ","",$text));
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}

if(strpos($text, "/lancia") === 0)
{
    global $username, $active_setting, $settings, $municipalities;
    $app_running = $active_setting["app_running"];
    if($username == "TeamBallo") {
        if($app_running == 0) {
            initGuerra(1);
            sendGETMessage("[OK] Nuova guerra avviata!");
            sendGETMessageToChannel("NUOVA GUERRA AVVIATA, PREPARARSI ALLA BATTAGLIA!");
        } else {
            sendGETMessage("[NO] Guerra già in esecuzione!");
        }
    } else {
        sendGETMessage("[ER] Non hai accesso a questo comando.");
    }
}

if(strpos($text, "/arresta") === 0)
{
    global $username, $active_setting, $settings, $municipalities;
    $app_running = $active_setting["app_running"];
    if($username == "TeamBallo") {
        if($app_running == 1) {
            initGuerra(0);
            sendGETMessage("[OK] Guerra terminata forzatamente!");
            sendGETMessageToChannel("GUERRA TERMINATA FORZATAMENTE DALL'AMMINISTRATORE!");
        } else {
            sendGETMessage("[NO] Guerra non in esecuzione!");
        }
    } else {
        sendGETMessage("[ER] Non hai accesso a questo comando.");
    }
}


if(strpos($text, "/riavvia") === 0)
{
    global $username, $active_setting, $settings;
    $app_running = $active_setting["app_running"];
    if($username == "TeamBallo") {
        if($app_running == 0) {
            $settings->updateSettingRunning($active_setting["id"], 1);
            sendGETMessage("[OK] Guerra avviata!");
        } else {
            sendGETMessage("[NO] Guerra già in esecuzione!");
        }
    } else {
        sendGETMessage("[ER] Non hai accesso a questo comando.");
    }
}

if(strpos($text, "/pausa") === 0)
{
    global $username, $active_setting, $settings;
    $app_running = $active_setting["app_running"];
    if($username == "TeamBallo") {
        if($app_running == 1) {
            $settings->updateSettingRunning($active_setting["id"], 0);
            sendGETMessage("[OK] Guerra fermata!");
        } else {
            sendGETMessage("[NO] Guerra già ferma!");
        }
    } else {
        sendGETMessage("[ER] Non hai accesso a questo comando.");
    }
}

if(strpos($text, "/store") === 0)
{
    global $username, $municipalities;
    if($username == "TeamBallo") {
        $municipalities->storeMunicipalities();
        sendGETMessage("Comuni inseriti");
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}




if(strpos($text, "/forzascontro") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        if($active_setting["app_running"] == 1) {
            //sendGETMessageToChannel("APP RUNNING"); //TODO REMOVE
            $alive = $municipalities->getAliveMunicipalities();	
            $realSize = is_array($alive) ? sizeOf($alive) : 0;
                if(($realSize - 1) > 1) {
                    //START WEIGHT VALUES
                    for($i = 0; $i < $realSize; $i++) {
                        $alive[$i]["realweight"] = $alive[$i]["weight"];
                        while($alive[$i]["weight"] > 1) {
                            $extraelement[$i] = $alive[$i];
                            $extraelement[$i]["weight"] = 1;
                            array_push($alive, $extraelement[$i]);
                            $alive[$i]["weight"] = $alive[$i]["weight"] - 1;
                        }
                    }
                    foreach($alive as $comune) {
                        error_log(implode(",", $comune));
                    }
                    //END WEIGHT VALUES
                    $w = $alive[rand(0,sizeof($alive)-1)];
                    $l = $alive[rand(0,sizeof($alive)-1)];
                    while ($w['name'] == $l['name'])
                    {
                        $l = $alive[rand(0,sizeof($alive)-1)];		
                    }
                    if ($l["realweight"] < 1) {
                        sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha colpito il comune di <b>".$l['name']."</b> (".$l['realweight'].")! ".$realSize." comuni rimanenti.");
                    } else {
                        sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha sconfitto il comune di <b>".$l['name']."</b>! ".($realSize - 1)." comuni rimanenti.");
                    }
                    //DECREASE LOOSER WEIGHT
                    $municipalities->updateMunicipalityWeight($l["id"], $l["realweight"] - 1);
                    //INCREASE WINNER WEIGHT
                    $municipalities->updateMunicipalityWeight($w["id"], $w["realweight"] + 1);
                    unset($alive);
                } else {
                    //TODO IMPLEMENT STABLE METHOD GET SINGLE ALIVE MUNICIPALITY
                    $champion = $municipalities->getRandomMunicipality();
                    initGuerra(0);
                    sendGETMessageToChannel("Il comune di <b>".$champion['name']."</b> ha vinto la sfida tra comuni!");
                }
        } else {
            sendGETMessage("[ER] Guerra non attiva!"); //TODO REMOVE
        }
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}








//FUNCTIONS

function initGuerra($active) {
    global $municipalities, $settings, $active_setting;
    $municipalities->resetGuerra();
    $settings->updateSettingRunning($active_setting["id"], $active);
}

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
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$channel_id&text=$message&parse_mode=html";
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

?>

