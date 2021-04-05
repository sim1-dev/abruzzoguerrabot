<?php 

require 'vendor/autoload.php';

require_once("db.php");
require_once("Settings.php");
require_once("Municipalities.php");
require_once("functions.php");

//APP VARS

global $username, $settings, $active_setting, $regno_id, $entity;

$entity = new Entity();
$settings = new Settings();
$municipalities = new Municipalities();

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
$username = $message["from"]["username"];
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
    global $username, $municipalities, $app_settings;
    $app_running = $active_setting["app_running"];
    if($username == "TeamBallo") {
        if($app_running == 1) {
            $municipalities->storeMunicipalities();
            sendGETMessage("Comuni inseriti");
        } else {
            sendGETMessage("[ER] Guerra non attiva!");
        }
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}

if(strpos($text, "/territori") === 0)
{
    global $username, $municipalities, $app_settings;
    $app_running = $active_setting["app_running"];
    if($app_running == 1) {
        $topweights = $municipalities->getWeightsHighscore();
        sendGETMessage("Comuni con più territori:%0A"."1) <b> ".$topweights[0]['owner']." </b> - <b>".$topweights[0]['weight']."</b> 🥇%0A"."2) <b> ".$topweights[1]['owner']." </b>- <b>".$topweights[1]['weight']."</b> 🥈%0A"."3) <b> ".$topweights[2]['owner']." </b>- <b>".$topweights[2]['owner']."</b> 🥉%0A"."4) <b> ".$topweights[3]['owner']." </b>- <b>".$topweights[3]['weight']."</b>%0A"."5) <b> ".$topweights[4]['owner']." </b>- <b>".$topweights[4]['weight']."</b>"); 
    } else {
        sendGETMessage("[ER] Guerra non attiva!");
    }
}

if(strpos($text, "/uccisioni") === 0)
{
    global $username, $municipalities, $app_settings;
    $app_running = $active_setting["app_running"];
    if($app_running == 1) {
        $topkills = $municipalities->getKillsHighscore();
        sendGETMessage("Comuni con più uccisioni:%0A"."1) <b> ".$topkills[0]['name']." </b> - <b>".$topkills[0]['kills']."</b> ⭐⭐⭐%0A"."2) <b> ".$topkills[1]['name']." </b>- <b>".$topkills[1]['kills']."</b> ⭐⭐%0A"."3) <b> ".$topkills[2]['name']." </b>- <b>".$topkills[2]['kills']."</b> ⭐%0A"."4) <b> ".$topkills[3]['name']." </b>- <b>".$topkills[3]['kills']."</b>%0A"."5) <b> ".$topkills[4]['name']." </b>- <b>".$topkills[4]['kills']."</b>"); 
    } else {
        sendGETMessage("[ER] Guerra non attiva!");
    }
}

if(strpos($text, "/forzascontro") === 0)
{
    global $username;
    if($username == "TeamBallo") {
        $app_running = $active_setting["app_running"];
        if($app_running == 1) {
            include("cron.php");
        } else {
            sendGETMessage("[ER] Guerra non attiva!");
        }
    } else {
        sendGETMessage("[ER] Non hai i permessi per accedere a questo comando.");
    }
}

?>
