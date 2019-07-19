<?php 
    include("../config/db.php");
    try{
        $db = new db();
        $db = $db->connect();
        $selectPendingSMSQuery = "SELECT * FROM smsservicehelperqueue WHERE SMSStatus = '1' OR SMSStatus = '3'";
        $stmt = $db->prepare($selectPendingSMSQuery);
        $stmt->execute();
        $pendingSMSResults = $stmt->fetchAll(PDO::FETCH_OBJ);

        for ($i=0; $i < sizeof($pendingSMSResults); $i++) { 
            //send request
            $URL = "http://api.smsgh.com/v3/messages/send?" . "From=Solushop-GH" . "&To=%2B" . urlencode($pendingSMSResults[$i]->Phone) . "&Content=" . urlencode($pendingSMSResults[$i]->Message) . "&ClientId=dylsfikt" . "&ClientSecret=rrllqthk" . "&RegisteredDelivery=true";
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_URL, $URL);
            $response = curl_exec($ch);
            if ($response) {
                $SMSStatus = '2';
            }else{
                $SMSStatus = '3';
            }

            //update sms status
            $SMSID = $pendingSMSResults[$i]->SMSID;
            $selectPendingSMSQuery = "UPDATE smsservicehelperqueue SET SMSStatus = '$SMSStatus' WHERE SMSID = '$SMSID'";
            $stmt = $db->prepare($selectPendingSMSQuery);
            $stmt->execute();
        }
        $db = null;   
    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
?>