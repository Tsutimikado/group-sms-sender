<?php
$text = $_GET['text'];
$phone = $_GET['phone'];

function sendToTelegram($phone, $text)
{
	$url = 'http://172.16.119.18:8018/api/ibt/accept_http_sms?text='.urlencode($text).'&phone='.$phone;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
	curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT,7);
	$output = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $httpcode;
}

echo sendToTelegram($phone, $text);
?>