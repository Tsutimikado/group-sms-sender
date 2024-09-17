<?php

if ($_POST['phone'] && $_POST['message']) {
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://smpptest.ibt.tj/sendsms23.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "XML=%3C%3Fxml%20version%3D%221.0%22%20encoding%3D%22utf-8%22%3F%3E%20%3Crequest%3E%20%3Cmobnumber%3E992$phone%3C/mobnumber%3E%20%3CtextSms%3E$message%3C/textSms%3E%20%3C/request%3E",
        CURLOPT_HTTPHEADER => array(

        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
} 
?>

