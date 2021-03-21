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

global $username, $settings, $active_setting, $regno_id;

$settings = new Settings($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);
$municipalities = new Municipalities($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);

$active_setting = $settings->getActiveSetting();

$response = '';
$channel_id = (string)getenv("CHANNEL_ID");
$regno_id = (string)getenv("REGNO_ID"); //your custom group
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
        sendGETMessage("We creatò!");
    } else {
        sendGETMessage("Segui lo scontro sul canale ufficiale: @AbruzzoGuerra");
    }
}

if(strpos($text, "/comunerandom") === 0) //fun command
{
    global $username, $municipalities;
        $random = $municipalities->getAnyRandomMunicipality();
        sendGETMessage($random["name"]);
}

if(strpos($text, "/broadcast") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        sendGETMessageToChannel(str_replace("/broadcast ","",$text));
        sleep(1);
        sendMessageToRegno(str_replace("/broadcast ","",$text));
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}

if(strpos($text, "/avvia") === 0)
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

if(strpos($text, "/territori") === 0)
{
    global $username, $municipalities;
    if($app_running == 1) {
        $topweights = $municipalities->getWeightsHighscore();
        sendGETMessage("Comuni con più territori:"); 
        sleep(1);
        sendGETMessage("1) <b> ".$topweights[0]['name']." </b> - <b>".$topweights[0]['weight']."</b> 🥇");
        sleep(1);
        sendGETMessage("2) <b> ".$topweights[1]['name']." </b>- <b>".$topweights[1]['weight']."</b> 🥈");
        sleep(1);
        sendGETMessage("3) <b> ".$topweights[2]['name']." </b>- <b>".$topweights[2]['weight']."</b> 🥉");
        sleep(1);
        sendGETMessage("4) <b> ".$topweights[3]['name']." </b>- <b>".$topweights[3]['weight']."</b>");
        sleep(1);
        sendGETMessage("5) <b> ".$topweights[4]['name']." </b>- <b>".$topweights[4]['weight']."</b>");
    } else {
        sendGETMessage("[ER] Guerra non attiva!");
    }
}

if(strpos($text, "/uccisioni") === 0)
{
    global $username, $municipalities;
    if($app_running == 1) {
        $topkills = $municipalities->getKillsHighscore();
        sendGETMessage("Comuni con più uccisioni:"); 
        sleep(1);
        sendGETMessage("1) <b> ".$topkills[0]['name']." </b> - <b>".$topkills[0]['kills']."</b> 🥇");
        sleep(1);
        sendGETMessage("2) <b> ".$topkills[1]['name']." </b>- <b>".$topkills[1]['kills']."</b> 🥈");
        sleep(1);
        sendGETMessage("3) <b> ".$topkills[2]['name']." </b>- <b>".$topkills[2]['kills']."</b> 🥉");
        sleep(1);
        sendGETMessage("4) <b> ".$topkills[3]['name']." </b>- <b>".$topkills[3]['kills']."</b>");
        sleep(1);
        sendGETMessage("5) <b> ".$topkills[4]['name']." </b>- <b>".$topkills[4]['kills']."</b>");
    } else {
        sendGETMessage("[ER] Guerra non attiva!");
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
                if(($realSize) > 1) {
                    //START WEIGHT VALUES HANDLING
                    for($i = 0; $i < $realSize; $i++) {
                        $alive[$i]["realweight"] = $alive[$i]["weight"];
                        while($alive[$i]["weight"] > 1) {
                            $extraelement[$i] = $alive[$i];
                            $extraelement[$i]["weight"] = 1;
                            array_push($alive, $extraelement[$i]);
                            $alive[$i]["weight"] = $alive[$i]["weight"] - 1;
                        }
                    }
                    //END WEIGHT VALUES HANDLING
                    $w = $alive[rand(0,sizeof($alive)-1)];
                    $l = $alive[rand(0,sizeof($alive)-1)];
                    while ($w['name'] == $l['name'])
                    {
                        $l = $alive[rand(0,sizeof($alive)-1)];		
                    }
                    if ($l["realweight"] > 1) {
                        sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha colpito il comune di <b>".$l['name']."</b> (".$l['realweight'].") !");
                        sendMessageToRegno("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha colpito il comune di <b>".$l['name']."</b> (".$l['realweight'].") !");
                    } else {
                        sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha sconfitto il comune di <b>".$l['name']."</b> !");
                        sendMessageToRegno("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha sconfitto il comune di <b>".$l['name']."</b> !");
                        sleep(1);
                        sendGETMessageToChannel("<b>".($realSize - 1)."</b> comuni rimanenti.");
                        sendMessageToRegno("<b>".($realSize - 1)."</b> comuni rimanenti.");
                        sleep(1);
                        $municipalities->addKill($w["id"]);
                    }
                    //DECREASE LOOSER WEIGHT
                    $municipalities->updateMunicipalityWeight($l["id"], $l["realweight"] - 1);
                    //INCREASE WINNER WEIGHT
                    $municipalities->updateMunicipalityWeight($w["id"], $w["realweight"] + 1);
                    unset($alive);
                } else {
                    //TODO IMPLEMENT STABLE METHOD GET SINGLE ALIVE MUNICIPALITY
                    $champion = $municipalities->getRandomMunicipality();
                    sendGETMessageToChannel("👑 Il comune di <b>".$champion['name']."</b> ha vinto la sfida tra comuni! 👑");
                    sendMessageToRegno("👑 Il comune di <b>".$champion['name']."</b> ha vinto la sfida tra comuni! 👑");
                    sleep(1);
                    $topkillers = $municipalities->getKillsHighscore();
                    sendGETMessageToChannel("Comuni con più uccisioni:"); 
                    sendMessageToRegno("Comuni con più uccisioni:"); 
                    sleep(1);
                    sendGETMessageToChannel("1) <b> ".$topkillers[0]['name']." </b> - <b>".$topkillers[0]['kills']."</b> ⭐⭐⭐");
                    sendMessageToRegno("1) <b> ".$topkillers[0]['name']." </b> - <b>".$topkillers[0]['kills']."</b> ⭐⭐⭐");
                    sleep(1);
                    sendGETMessageToChannel("2) <b> ".$topkillers[1]['name']." </b>- <b>".$topkillers[1]['kills']."</b> ⭐⭐");
                    sendMessageToRegno("2) <b> ".$topkillers[1]['name']." </b>- <b>".$topkillers[1]['kills']."</b> ⭐⭐");
                    sleep(1);
                    sendGETMessageToChannel("3) <b> ".$topkillers[2]['name']." </b>- <b>".$topkillers[2]['kills']."</b> ⭐");
                    sendMessageToRegno("3) <b> ".$topkillers[2]['name']." </b>- <b>".$topkillers[2]['kills']."</b> ⭐");
                    sleep(1);
                    sendGETMessageToChannel("4) <b> ".$topkillers[3]['name']." </b>- <b>".$topkillers[3]['kills']."</b>");
                    sendMessageToRegno("4) <b> ".$topkillers[3]['name']." </b>- <b>".$topkillers[3]['kills']."</b>");
                    sleep(1);
                    sendGETMessageToChannel("5) <b> ".$topkillers[4]['name']." </b>- <b>".$topkillers[4]['kills']."</b>");
                    sendMessageToRegno("5) <b> ".$topkillers[4]['name']." </b>- <b>".$topkillers[4]['kills']."</b>");
                    initGuerra(0);
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
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chatId&text=$message&parse_mode=html";
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

function sendMessageToRegno($message) {
	global $bot_token, $regno_id;
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$regno_id&text=$message&parse_mode=html";
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
