<?php
class Email
{

    function test()
    {

        return "natawag";
    }
    function status($id)
    {
        //$result = retrieve from database
        return $result;
    }

    function send($parameter)
    {

        $access_token = "cjloveceleste143143heartheart!";

        //  $parameter["subject"] = "subject";
        // send($parameter)

        //send($subject,$sender,$sender_name)

        $subject = $parameter["subject"];

        #emailaddress of sender
        $sender = $parameter["sender"];

        $sender_name = $parameter["sender_name"];
        $content = urlencode($parameter["content"]);
        $recipients =  json_encode($parameter["recipients"]);
        $attachments =  json_encode($parameter["attachments"]);

        $data = '{ 
    "subject": "' . $subject . '",
    "sender": "' . $sender . '",
    "sender_name": "' . $sender_name . '",
    "content" : "' . $content . '",
    "recipients" : ' . $recipients . ',
    "attachments" : ' . $attachments . '
    
    }';


        $ch2 = curl_init();

        //    $url = "https://vigdev2.carinsurancesolution.ph/U2vmyFyolyDJMsfIEpYBotrnG/send_email";
        $url = "https://www.vigattininsurance.com/U2vmyFyolyDJMsfIEpYBotrnG/send_email";


        curl_setopt($ch2, CURLOPT_URL, $url);
        curl_setopt($ch2, CURLOPT_VERBOSE, TRUE);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $ch2,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $access_token
            )
        );
        $r = curl_exec($ch2);
        curl_close($ch2);


        $response = json_decode($r);


        return $response;
    }
}
