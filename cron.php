<?php

require_once("Settings.php");
require_once("Municipalities.php");

global $channel_id, $bot_token, $settings, $municipalities, $active_setting;

$channel_id = (string)getenv("CHANNEL_ID");
$bot_token = (string)getenv("BOT_TOKEN");

$db_driver = getenv("DB_DRIVER");
$db_host = getenv("DB_HOST");
$db_port = getenv("DB_PORT");
$db_user = getenv("DB_USER");
$db_password = getenv("DB_PASSWORD");
$db_name = getenv("DB_NAME");


$settings = new Settings($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);
$municipalities = new Municipalities($db_driver, $db_host, $db_port, $db_user, $db_password, $db_name);

$active_setting = $settings->getActiveSetting();


if($active_setting["app_running"] == 1) {
    //sendGETMessageToChannel("APP RUNNING"); //TODO REMOVE
    $alive = $municipalities->getAliveMunicipalities();	
    $realSize = is_array($alive) ? sizeOf($alive) : 0;
        if(($realSize - 1) > 1) {
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
            } else {
                sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha sconfitto il comune di <b>".$l['name']."</b> ! <br><b>".($realSize - 1)."</b> comuni rimanenti.");
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
            sleep(5);
            $topkillers = $municipalities->getKillsHighscore();
            sendGETMessageToChannel("Comuni con più uccisioni: <br> 
                                    1) <b> ".$topkillers[0]['name']." </b> - <b>".$topkillers[0]['kills']."</b> 👑 <br>
                                    2) <b> ".$topkillers[1]['name']." </b>- <b>".$topkillers[1]['kills']."</b>  <br>
                                    3) <b> ".$topkillers[2]['name']." </b>- <b>".$topkillers[2]['kills']."</b> <br>
                                    4) <b> ".$topkillers[3]['name']." </b>- <b>".$topkillers[3]['kills']."</b>  <br>
                                    5) <b> ".$topkillers[4]['name']." </b>- <b>".$topkillers[4]['kills']."</b>  <br>
            ");
            initGuerra(0);
        }
} else {
   // sendGETMessage("[ER] Guerra non attiva!"); //TODO REMOVE
}

function initGuerra($active) {
    global $municipalities, $settings, $active_setting;
    $municipalities->resetGuerra();
    $settings->updateSettingRunning($active_setting["id"], $active);
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

function sendGETMessage($message) {
	global $bot_token, $chatId;
    $url = "https://api.telegram.org/bot".$bot_token."/sendMessage?chat_id=".$chatId."&text=".$message;
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