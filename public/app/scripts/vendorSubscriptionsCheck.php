<?php 
    //include database connecction
    include("../config/db.php");
    try{
        $db = new db();
        $db = $db->connect();
        $sql_subscriptions_query = "SELECT * from vendor_subscription, suppliers WHERE vendor_subscription.VS_Vendor_ID = suppliers.ID AND VS_Days_Left > 0";
        $stmt = $db->prepare($sql_subscriptions_query);
        $stmt->execute();
        $sql_subscription = $stmt->fetchAll(PDO::FETCH_OBJ);


        for ($i=0; $i < sizeof($sql_subscription); $i++) { 
            $DaysLeft = $sql_subscription[$i]->VS_Days_Left;
            $VS_Vendor_ID = $sql_subscription[$i]->VS_Vendor_ID;
            $NewDaysLeft = $DaysLeft - 1;

            //update
            $sql_subscriptions_update_query = "UPDATE vendor_subscription SET VS_Days_Left = '$NewDaysLeft' WHERE VS_Vendor_ID = :VS_Vendor_ID";
            $stmt = $db->prepare($sql_subscriptions_update_query);
            $stmt->bindParam(':VS_Vendor_ID', $VS_Vendor_ID);
            $stmt->execute();

            if ($NewDaysLeft == 0) {
                //subscription expired
                //change product state
                $sql_vendor_products_update_query = "UPDATE products SET Product_Status = '4' WHERE Supplier_ID = :VS_Vendor_ID AND (Product_Status = '0' OR Product_Status = '1')";
                $stmt = $db->prepare($sql_vendor_products_update_query);
                $stmt->bindParam(':VS_Vendor_ID', $VS_Vendor_ID);
                $stmt->execute();

                $VMessage = "Hey ".$sql_subscription[$i]->Name." we hate to see you go but your subscription has expired. Please reactivate under the subscription tab in your portal. Please don't keep us missing you for too long.";

                try{
                    $db = new db();
                    $db = $db->connect();
                    $insertIntoSMSQueueQuery = "INSERT INTO smsservicehelperqueue (SMSID, Message, Phone, SMSStatus) VALUES (NULL, :Message, :Phone, '1')";
                    $stmt = $db->prepare($insertIntoSMSQueueQuery);
                    $stmt->bindParam(':Message', $VMessage);
                    $stmt->bindParam(':Phone', $sql_subscription[$i]->Phone);
                    $stmt->execute();
                    $db = null;
                }catch(PDOException $e){
                    echo '{"error": {"text": '.$e->getMessage().'}';
                }
                //notify vendor
            }elseif($NewDaysLeft == 5){
                // notify vendor
                $VMessage = "Hiya ".$sql_subscription[$i]->Name.", time flies when you're with the right people. Looks like your subscription is about expiring. Kindly extend your subscription when you have the chance to. You have $NewDaysLeft days left.";
                try{
                    $db = new db();
                    $db = $db->connect();
                    $insertIntoSMSQueueQuery = "INSERT INTO smsservicehelperqueue (SMSID, Message, Phone, SMSStatus) VALUES (NULL, :Message, :Phone, '1')";
                    $stmt = $db->prepare($insertIntoSMSQueueQuery);
                    $stmt->bindParam(':Message', $VMessage);
                    $stmt->bindParam(':Phone', $sql_subscription[$i]->Phone);
                    $stmt->execute();
                    $db = null;
                }catch(PDOException $e){
                    echo '{"error": {"text": '.$e->getMessage().'}';
                }
            }elseif($NewDaysLeft == 3){
                // notify vendor
                $VMessage = "Heya ".$sql_subscription[$i]->Name.", did you forget to extend your subscription? No worries, we're here to remind you. You have 3 days left on your subscription.";
                try{
                    $db = new db();
                    $db = $db->connect();
                    $insertIntoSMSQueueQuery = "INSERT INTO smsservicehelperqueue (SMSID, Message, Phone, SMSStatus) VALUES (NULL, :Message, :Phone, '1')";
                    $stmt = $db->prepare($insertIntoSMSQueueQuery);
                    $stmt->bindParam(':Message', $VMessage);
                    $stmt->bindParam(':Phone', $sql_subscription[$i]->Phone);
                    $stmt->execute();
                    $db = null;
                }catch(PDOException $e){
                    echo '{"error": {"text": '.$e->getMessage().'}';
                }
            }elseif($NewDaysLeft == 1){
                // notify vendor
                $VMessage = "Hi ".$sql_subscription[$i]->Name.", please dont leave. You have only 24 hours remaining on your subscription. Please extend your subscription ASAP! We don't want to lose you.";
                try{
                    $db = new db();
                    $db = $db->connect();
                    $insertIntoSMSQueueQuery = "INSERT INTO smsservicehelperqueue (SMSID, Message, Phone, SMSStatus) VALUES (NULL, :Message, :Phone, '1')";
                    $stmt = $db->prepare($insertIntoSMSQueueQuery);
                    $stmt->bindParam(':Message', $VMessage);
                    $stmt->bindParam(':Phone', $sql_subscription[$i]->Phone);
                    $stmt->execute();
                    $db = null;
                }catch(PDOException $e){
                    echo '{"error": {"text": '.$e->getMessage().'}';
                }
            }
        }
        
        $db = null; 
    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
?>