<?php

require_once("Settings.php");
require_once("Municipalities.php");
require_once("functions.php");

global $channel_id, $bot_token, $settings, $municipalities, $active_setting, $regno_id, $entity;

$channel_id = (string)getenv("CHANNEL_ID");
$regno_id = (string)getenv("REGNO_ID");
$bot_token = (string)getenv("BOT_TOKEN");

$entity = new Entity();
$settings = new Settings();
$municipalities = new Municipalities();

$active_setting = $settings->getActiveSetting();

if($active_setting["app_running"] == 1) {
    $realSize = $municipalities->getAliveMunicipalitiesNumber();
    if(($realSize) > 0) {
        $alive = $municipalities->selectAll();
        $w = $alive[rand(0,sizeof($alive)-1)];
        $mindist = 999999999;
        for($i = 0; $i < sizeOf($alive); $i++) {
            if($alive[$i]["owner"] !== $w["owner"]) {
                $distance = distance(floatval($w["lat"]), floatval($w["long"]), floatval($alive[$i]["lat"]), floatval($alive[$i]["long"]));
                if($distance < $mindist) {
                    $mindist = $distance;
                    $l = $alive[$i];
                }
            }
        }
        $realLooser = $municipalities->getMunicipalityByName($l["owner"]);
        $realWinner = $municipalities->getMunicipalityByName($w["owner"]);
        $wweight = $municipalities->getWeightByName($w["owner"]);
        $lweight = $municipalities->getWeightByName($l["owner"]);
                $destiny = "";
                $strength = $wweight; //TODO IMPLEMENT LOOSER WEIGHT DIVISION FOR GENERAL ATTACK MESSAGES
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
                        $destiny = "modificandone la topografia";
                        break;
                    case 8:
                        $destiny = "convertendone ogni singolo abitante";
                        break;
                    case 9:
                        $destiny = "inglobandone ogni singola particella";
                        break;
                    case 10:
                        $destiny = "annientandone la speranza";
                        break;
                    case 11:
                        $destiny = "massacrandone l'identità";
                        break;
                    case 12:
                        $destiny = "cancellandolo dai libri di storia";
                        break;
                    case 13:
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
                $subjects = $entity->selectAll("subjects");
                $subject = $subjects[rand(0,sizeof($subjects)-1)]["text"];
                //END SUBJECT
                if ($lweight > 1) {
                    if($l['name'] !== $l['owner']) {
                        $message = "Il comune di <b>".$w['owner']."</b> (".$wweight.") ha colpito $subject del comune di <b>".$l['owner']."</b> (".$lweight.") sul territorio di ".$l['name']."!";
                    } else {
                        $message = "Il comune di <b>".$w['owner']."</b> (".$wweight.") ha colpito $subject del comune di <b>".$l['owner']."</b> (".$lweight.")!";
                    }
                    sendGETMessageToChannel($message);
                    sendMessageToRegno($message);
                } else {
                    $message = "Il comune di <b>".$w['owner']."</b> (".$wweight.") ha conquistato il territorio del comune di <b>".$l['owner']."</b> $destiny!%0A"."<b>".($realSize)."</b> comuni rimanenti.";
                    sendGETMessageToChannel($message);
                    sendMessageToRegno($message);
                    $municipalities->addKill($realWinner["id"]);
                    $municipalities->kill($l['id']);
                }
                //SET OWNER TO CLAIMED LAND
                $municipalities->setOwner($l["id"], $w['owner']);
                unset($alive);
        } else {
            //TODO IMPLEMENT STABLE METHOD GET SINGLE ALIVE MUNICIPALITY
            $champion = $municipalities->getChampionMunicipality();
            sendGETMessageToChannel("👑 Il comune di <b>".$champion['owner']."</b> ha vinto la sfida tra comuni! 👑");
            sendMessageToRegno("👑 Il comune di <b>".$champion['owner']."</b> ha vinto la sfida tra comuni! 👑");
            sleep(3);
            $topkills = $municipalities->getKillsHighscore();
            sendGETMessage("Comuni con più uccisioni:%0A"."1) <b> ".$topkills[0]['name']." </b> - <b>".$topkills[0]['kills']."</b> ⭐⭐⭐%0A"."2) <b> ".$topkills[1]['name']." </b>- <b>".$topkills[1]['kills']."</b> ⭐⭐%0A"."3) <b> ".$topkills[2]['name']." </b>- <b>".$topkills[2]['kills']."</b> ⭐%0A"."4) <b> ".$topkills[3]['name']." </b>- <b>".$topkills[3]['kills']."</b>%0A"."5) <b> ".$topkills[4]['name']." </b>- <b>".$topkills[4]['kills']."</b>");
            sendMessageToRegno("Comuni con più uccisioni:%0A"."1) <b> ".$topkills[0]['name']." </b> - <b>".$topkills[0]['kills']."</b> ⭐⭐⭐%0A"."2) <b> ".$topkills[1]['name']." </b>- <b>".$topkills[1]['kills']."</b> ⭐⭐%0A"."3) <b> ".$topkills[2]['name']." </b>- <b>".$topkills[2]['kills']."</b> ⭐%0A"."4) <b> ".$topkills[3]['name']." </b>- <b>".$topkills[3]['kills']."</b>%0A"."5) <b> ".$topkills[4]['name']." </b>- <b>".$topkills[4]['kills']."</b>");
            initGuerra(0);
    }

}
