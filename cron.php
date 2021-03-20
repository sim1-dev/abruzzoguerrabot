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
        if(is_array($alive) && $realSize > 1) {
            //START WEIGHT VALUES
            for($i = 0; $i < $realSize; $i++) {
                sendGETMessageToChannel("current weight: ".$alive[$i]["weight"]);
                while($alive[$i]["weight"] > 1) {
                    sendGETMessageToChannel("size of array before: ".sizeOf($alive));
                    $extraelement = $alive[$i];
                    array_push($alive, $alive[$i]);
                    sendGETMessageToChannel("old weight: ".$alive[$i]["weight"]);
                    $alive[$i]["weight"] = $alive[$i]["weight"] - 1;
                    sendGETMessageToChannel("new weight: ".$alive[$i]["weight"]);
                    sendGETMessageToChannel("size of array after: ".sizeOf($alive));
                    sleep(1);
                }
                sleep(1);
            }
            //END WEIGHT VALUES
            $w = $alive[rand(0,sizeof($alive)-1)];
            $l = $alive[rand(0,sizeof($alive)-1)];
            while ($w == $l)
            {
                $l = $alive[rand(0,sizeof($alive)-1)];		
            }
            if ($l["weight"] < 1) {
                sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['weight'].") ha colpito il comune di ".$l['name']." (".$l['weight'].")! ".$realSize." comuni rimanenti.");
            } else {
                sendGETMessageToChannel("Il comune di <b>".$w['name']."</b> (".$w['weight'].") ha sconfitto il comune di ".$l['name']."! ".$realSize." comuni rimanenti.");
            }
            //DECREASE LOOSER WEIGHT
            $municipalities->updateMunicipalityWeight($l["id"], $l["weight"] - 1);
            //INCREASE WINNER WEIGHT
            $municipalities->updateMunicipalityWeight($w["id"], $w["weight"] + 1);
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