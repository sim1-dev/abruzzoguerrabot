<?php

require_once("Settings.php");
require_once("Municipalities.php");

global $channel_id, $bot_token, $settings, $municipalities, $active_setting, $regno_id;

$channel_id = (string)getenv("CHANNEL_ID");
$regno_id = (string)getenv("REGNO_ID");
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
            //START STRENGTH MESSAGE
            $destiny = "";
            $strength = $w["realweight"]/$l["realweight"];
            switch($strength) {
                case 1:
                    $destiny = "";
                    break;
                case 2:
                    $destiny = "con poca fatica";
                    break;
                case 3:
                    $destiny = "impedendogli di contrattaccare";
                    break;
                case 4:
                    $destiny = "senza alcuno sforzo";
                    break;
                case 5:
                    $destiny = "colpendolo ripetutamente senza pietà";
                    break;
                case 6:
                    $destiny = "devastandone ogni edificio";
                    break;
                case 7:
                    $destiny = "convertendone ogni singolo abitante";
                    break;
                case 8:
                    $destiny = "inglobandone ogni singola particella";
                    break;
                case 9:
                    $destiny = "annientandone la speranza";
                        break;
                case 10:
                    $destiny = "massacrandone l'identità";
                    break;
                case 11:
                    $destiny = "cancellandolo dai libri di storia";
                    break;
                case 12:
                    $destiny = "eliminandolo dalle cartine geografiche";
                    break;
                case "default":
                    $destiny = "";
                    break;
            }
            if($strength > 12) {
                $destiny = "rimuovendolo dall'universo";
            }
            //END STRENGTH MESSAGE

            //START SUBJECT
            $subjects = array("il fronte nord", "il fronte sud", "il fronte est", "il fronte ovest", "il centro medico", "il municipio", "gli edifici primari", "le strade principali", "le mura esterne", "gli armamenti", "il centro", "i monumenti", "le chiese", "la zona residenziale", "i centri sociali", "i mezzi di trasporto pubblici", "i condotti idrici", "i collegamenti radio");
            $subject = $subjects[rand(0,sizeof($subjects)-1)];
            //END SUBJECT
            if ($l["realweight"] > 1) {
                $message = "Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha colpito $subject del comune di <b>".$l['name']."</b> (".$l['realweight'].") !";
                sendGETMessageToChannel($message);
                sendMessageToRegno($messsage);
            } else {
                $message = "Il comune di <b>".$w['name']."</b> (".$w['realweight'].") ha sconfitto il comune di <b>".$l['name']."</b> $destiny!%0A"."<b>".($realSize - 1)."</b> comuni rimanenti.";
                sendGETMessageToChannel($message);
                sendMessageToRegno($messsage);
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
            sleep(3);
            $topkills = $municipalities->getKillsHighscore();
            sendGETMessage("Comuni con più uccisioni:%0A"."1) <b> ".$topkills[0]['name']." </b> - <b>".$topkills[0]['kills']."</b> ⭐⭐⭐%0A"."2) <b> ".$topkills[1]['name']." </b>- <b>".$topkills[1]['kills']."</b> ⭐⭐%0A"."3) <b> ".$topkills[2]['name']." </b>- <b>".$topkills[2]['kills']."</b> ⭐%0A"."4) <b> ".$topkills[3]['name']." </b>- <b>".$topkills[3]['kills']."</b>%0A"."5) <b> ".$topkills[4]['name']." </b>- <b>".$topkills[4]['kills']."</b>");
            sendMessageToRegno("Comuni con più uccisioni:%0A"."1) <b> ".$topkills[0]['name']." </b> - <b>".$topkills[0]['kills']."</b> ⭐⭐⭐%0A"."2) <b> ".$topkills[1]['name']." </b>- <b>".$topkills[1]['kills']."</b> ⭐⭐%0A"."3) <b> ".$topkills[2]['name']." </b>- <b>".$topkills[2]['kills']."</b> ⭐%0A"."4) <b> ".$topkills[3]['name']." </b>- <b>".$topkills[3]['kills']."</b>%0A"."5) <b> ".$topkills[4]['name']." </b>- <b>".$topkills[4]['kills']."</b>");
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

function sendGETMessage($message) {
	global $bot_token, $chatId;
    $url = "https://api.telegram.org/bot".$bot_token."/sendMessage?chat_id=".$chatId."&text=".$message."&parse_mode=html";
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
