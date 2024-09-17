<?php
/* header('Access-Control-Allow-Origin: *'); */




/* echo "HI="; */


 
$xmlSmsResponse = $_POST['XML'];
/* echo "SMS=$xmlSmsResponse"; */
        	
if ($xmlSmsResponse !== 'null'){
	
$xmlSms = simplexml_load_string($xmlSmsResponse);

$mobile = $xmlSms->from;
$text = $xmlSms->content;
$source = "";
$parts = 1;
		if(!empty($xmlSms->source))
		{
			$source = $xmlSms->source;
		}
	
		$cyrilic = preg_match('/[А-Яа-яЁёIi1@!$]/u', $text);
		
		$length = mb_strlen($text, 'UTF-16');
		
    	if ($cyrilic)
		{
			if ($length > 20) 
			{
					$result = ($length / 67);
					$whole = floor($result);      
					$fraction = $result - $whole;
					
					if ($fraction > 0)
					{
						$parts = $whole + 1;
					}
					else
					{
						$parts = $whole;
					}
			}
		}
		else 
{ 
//$str = "Это текст электронного письма, которое нужно будет отправить адресату...";
//$str = WordWrap ($str, 30, "<br>");
//echo $str;

			if ($length > 160) 
			{
					$result = ($length / 153);
					$whole = floor($result);      
					$fraction = $result - $whole;
					
					if ($fraction > 0)
					{
						$parts = $whole + 1;
					}
					else
					{
						$parts = $whole;
					}
			}
		}
}



else 
	{echo "empty data!";}


	
if($mobile == 'null' || $text == 'null'){echo "empty data!";}
else {



                $trans = array(
                                        "а" => "0430",
                                        "б" => "0431",
                                        "в" => "0432",
                                        "г" => "0433",
                                        "д" => "0434",
                                        "е" => "0435",
                                        "ё" => "0451",
                                        "ж" => "0436",
                                        "з" => "0437",
                                        "и" => "0438",
                                        "й" => "0439",
                                        "к" => "043A",
                                        "л" => "043B",
                                        "м" => "043C",
                                        "н" => "043D",
                                        "о" => "043E",
                                        "п" => "043F",
                                        "р" => "0440",
                                        "с" => "0441",
                                        "т" => "0442",
                                        "у" => "0443",
                                        "ф" => "0444",
                                        "х" => "0445",
                                        "ц" => "0446",
                                        "ч" => "0447",
                                        "ш" => "0448",
                                        "щ" => "0449",
										"ъ" => "044A",
                                        "ы" => "044B",
										"ь" => "044C",
                                        "э" => "044D",
                                        "ю" => "044E",
                                        "я" => "044F",
										"і" => "0456",
										"ї"=>  "0457",
                                        "є" => "0454",
										"ӯ" => "04EF",
										"Ӯ" => "04EE",
										"ӣ" => "04E3",
										"Ӣ" => "04E2",
										"Қ" => "049A",
										"қ" => "049B",
										"Ғ" => "0492",
										"ғ" => "0493",
										"ҳ" => "04B3",
										"Ҳ" => "04B2",
										"Ҷ" => "04B6",
										"ҷ" => "04B7",
										
                                        "А" => "0410",
                                        "Б" => "0411",
                                        "В" => "0412",
                                        "Г" => "0413",
                                        "Д" => "0414",
                                        "Е" => "0415",
                                        "Ё" => "0401",
                                        "Ж" => "0416",
                                        "З" => "0417",
										"І" => "0406",
                                        "И" => "0418",
                                        "Й" => "0419",
                                        "К" => "041A",
                                        "Л" => "041B",
                                        "М" => "041C",
                                        "Н" => "041D",
                                        "О" => "041E",
                                        "П" => "041F",
                                        "Р" => "0420",
                                        "С" => "0421",
                                        "Т" => "0422",
                                        "У" => "0423",
                                        "Ф" => "0424",
                                        "Х" => "0425",
                                        "Ц" => "0426",
                                        "Ч" => "0427",
                                        "Ш" => "0428",
                                        "Щ" => "0429",
										"Ъ" => "042A",
                                        "Ы" => "042B",
										"Ь" => "042C",
                                        "Э" => "042D",
                                        "Ю" => "042E",
                                        "Я" => "042F",
										"Ї" => "0407",
                                        "Є" => "0404",
                                        "A" => "0041",
										
										"B" => "0042",
										"C" => "0043",
										"D" => "0044",
										"E" => "0045",
										"F" => "0046",
										"G" => "0047",
										"H" => "0048",
										"I" => "0049",
										"J" => "004A",
										"K" => "004B",
										"L" => "004C",
										"M" => "004D",
										"N" => "004E",
										"O" => "004F",
										"P" => "0050",
										"Q" => "0051",
										"R" => "0052",
										"S" => "0053",
										"T" => "0054",
										"U" => "0055",
										"V" => "0056",
										"W" => "0057",
										"X" => "0058",
										"Y" => "0059",
										"Z" => "005A",
										"a" => "0061",
										"b" => "0062",
										"c" => "0063",
										"d" => "0064",
										"e" => "0065",
										"f" => "0066",
										"g" => "0067",
										"h" => "0068",
										"i" => "0069",
										"j" => "006A",
										"k" => "006B",
										"l" => "006C",
										"m" => "006D",
										"n" => "006E",
										"o" => "006F",
										"p" => "0070",
										"q" => "0071",
										"r" => "0072",
										"s" => "0073",
										"t" => "0074",
										"u" => "0075",
										"v" => "0076",
										"w" => "0077",
										"x" => "0078",
										"y" => "0079",
										"z" => "007A",
								      	"\""=> "0022",								
										" " => "0020",
										"!" => "0021",
										"." => "002E",
										"#" => "0023",
										"$" => "0024",
										"%" => "0025",
										"&" => "0026",
										"'" => "0027",
										"*" => "002A",
										"+" => "002B",
										"," => "002C",
										"-" => "002D",
										"/" => "002F",
				
										"0" => "0030",
										"1" => "0031",
										"2" => "0032",
										"3" => "0033",
										"4" => "0034",
										"5" => "0035",
										"6" => "0036",
										"7" => "0037",
										"8" => "0038",
										"9" => "0039",
					                    "\n" => "000D",	
										":" => "003A",
										";" => "003B",
										"<" => "003C",
										"=" => "003D",
										">" => "003E",
										"?" => "003F",
										"@" => "0040",
										"(" => "0028",
										")" => "0029",
										"«" => "&laquo;",
										"»" => "&raquo;"
  
									
    );
                if(preg_match("/[а-яА-ЯIi1@!$]/",  $text)) {
                        $rate  =  strtr($content, $trans);
				}

                else {					
                        $rate =  $content;
			}


					/*$res = $this->parseSMS($body, $sequence_number);					
					echo "<br />$length<hr />";
					var_dump($stream);
					echo "<hr />";
					var_dump($body);
					echo "<hr />";
					var_dump($res);
					echo "<hr />";
					$state = $this->procsessms($res);*/
			$baseurl = 'http://172.16.119.11:9995/creatio/data/callCentre/operRates/clientRate';



			$params = $params.'?from='.urlencode($mobile);
			$params = $params.'&rate='.($rate);	

			
			$url = $baseurl.$params;
			
			$process = curl_init($url);

			curl_setopt($process, CURLOPT_HEADER, 1);
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_RETURNTRANSFER,TRUE);
			
			$response=curl_exec($process);
			
			if($response === FALSE)
			{
				die(curl_error($process));
			}
			
			$header_size = curl_getinfo($process, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size);

			curl_close($process);
			
			echo $body;
			



/*
// настройки сервера
$smpphost = "localhost";
$smppport = 2775;
$systemid = "faktura";
$password = "Nm7F2wiN";
$system_type = "IBT";
$from = "IBT"; 

// РїРѕРґРєР»СЋС‡Р°РµРј Р±РёР±Р»РёРѕС‚РµРєСѓ
include_once("smppclass1.php");
//include_once("dbcon.php");




 








$smpp = new SMPPClass();
$smpp->SetSender($from);

$smpp->Start($smpphost, $smppport, $systemid, $password, $system_type);

$res = $smpp->Send($mobile, $smpp->strToHTML($text), true);

if ($res){ 
	$servername = "localhost";
	$username = "epayuser";
	$password = "Yagonchi314";
	$dbname = "smpp";
	        	
				$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        		$conn->exec("set names utf8");
$stmt = $conn->prepare('insert into `sms` (mobile, text, source, parts, date) values (:mobile, :text, :source, :parts, now())');
$stmt->bindParam(':mobile',$mobile);
$stmt->bindParam(':text',$text);
$stmt->bindParam(':source',$source);
$stmt->bindParam(':parts',$parts);
$stmt->execute();
echo 'Отправлено';
}
//echo "РћС‚РїСЂР°РІР»РµРЅРѕ";} 
else
{echo "Error".$res;}
		


$smpp->End();
*/
}
?>