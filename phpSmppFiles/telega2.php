#!/usr/bin/php
<?php
/*******************************************
 * cli_smseagle.php
 *
 * Script to send an SMS via
 * SMSEagle - sms hardware gateway
 *
 * Version : 1.1
 * Date : Nov 23 2015
 * Author : Radoslaw Janowski / WWW.SMSEAGLE.EU
 * License: BSD
 * Copyright (c) 2013-2015, SMSEagle www.smseagle.eu
 * 
 *****************************************/
 
//Set the following values:
$smseagle_ip     = "localhost:1401/send?";
$login     = "zabbix";
$password     = "Passw0rd";
$timezone  = "Asia/Dushanbe"; 
//available timezones can be found here: http://www.php.net/manual/en/timezones.php

 
// Debugging?
$debug    = false;
 
/**********************************************************************
  do not change below unless you know what you are doing
 **********************************************************************/
date_default_timezone_set($timezone);
 
if (count($argv)<3) {
    die ("Usage: ".$argv[0]." recipientmobilenumber \"subject\" \"message\"\n");
}
 
if ( $debug ) file_put_contents("/tmp/smseagle_".date("YmdHis"), serialize($argv));
 
$to         = $argv[1];
$message    = $argv[2];
 
if ( $message == "" || !is_numeric($to) ) {
    die("missing params!\n");
}
 
$apiargs = array(
    "login" => $login,
	"pass" => $password,
    "to" => $to,
    "message" => $message
);
 
 
$url = "http://".$smseagle_ip."username=".$login."&password=".$password."&to=".$to."&content=".$messsage;
$params = "";
 
/* foreach ($apiargs as $k=>$v) {
    if ( $params != "" ) {
        $params .= "&";
    }
    $params .= $k."=".urlencode($v);
}
 */
// $url .= $params;
 
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url );
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
 
if ( $result === false ) {
    file_put_contents("/tmp/smseagle_error_".date("YmdHis"), curl_error($curl));
    die(curl_error($curl)."\n");
} else {
    if ( $debug || $result != 100 ) file_put_contents("/tmp/smseagle_answer_".date("YmdHis"), $result);
}
 
?>