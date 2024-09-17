<?php
/* header('Access-Control-Allow-Origin: *'); */


/* echo "HI="; */


 

$xmlSmsResponse = $_POST['XML'];
/* echo "SMS=$xmlSmsResponse"; */

if ($xmlSmsResponse !== 'null'){

$xmlSms = simplexml_load_string($xmlSmsResponse);
$mobile =$xmlSms->mobnumber;
$text = $xmlSms->textSms;
/* echo $mobile;
echo $text; */

}
else {echo "empty data!";}


if($mobile == 'null' || $text == 'null'){echo "empty data!";}
else {
// подключаем библиотеку
include_once("smppclass1.php");
//include_once("dbcon.php");

 /* настройки сервера
$smpphost = "127.0.0.1";
$smppport = 2775;
$systemid = "mbibt";
$password = "mbIbt24@";
$system_type = "IBT";
$from = "IBT";*/
 // настройки сервера
$smpphost = "217.11.183.9";
$smppport = 5019;
$systemid = "IBT";
$password = "Nm7F2xpE"; 
$system_type = "IBT";
$from = "IBT";









$smpp = new SMPPClass();
$smpp->SetSender($from);

$smpp->Start($smpphost, $smppport, $systemid, $password, $system_type);

$res = $smpp->Send($mobile, $smpp->strToHTML($text), true);

if ($res){ 

// mysql_query("insert into `out`(mobile,text) VALUES('$mobnumber','$textSms');", $connect); 
//mysql_query("update newtrans set sms='$textSms' where `code`='$idtr';", $connect); 
//$ad="update newtrans set sms='$text' where `code`='$idtr';";
echo "Отправлено";} 
else
{echo "Error".$res;}
		


$smpp->End();}
?>
