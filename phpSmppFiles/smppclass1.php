<?php

/*

File		:	smppclass.php
Implements	:	SMPPClass()
Description	:	This class can send messages via the SMPP protocol. Also supports unicode and multi-part messages.
License		:	GNU Lesser Genercal Public License: http://www.gnu.org/licenses/lgpl.html
Commercial advertisement: Contact info@chimit.nl for SMS connectivity and more elaborate SMPP libraries in PHP and other languages.

*/

/*
The following are the SMPP PDU types that we are using in this class.
Apart from the following 5 PDU types,  there are a lot of SMPP directives
that are not implemented in this version.
*/
define('CM_BIND_TRANSMITTER', 0x00000002);
define('CM_BIND_TRANSCEIVER', 0x00000009);
define('CM_QUERY_SM', 0x00000003);
define('CM_SUBMIT_SM', 0x00000004);
define('CM_SUBMIT_MULTI', 0x00000021);
define('CM_UNBIND', 0x00000006);
define('CM_DELIVER_SM', 0x00000005);
define('CM_ENQUIRELINK', 0x00000015);

class SMPPClass {
    
    private $_dest_addr; // номер получателя сообщения
    
// public members:
	/*
	Constructor.
	Parameters:
		none.
	Example:
		$smpp = new SMPPClass();
	*/
	function SMPPClass()
	{
		/* seed random generator */
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		srand($seed);

		/* initialize member variables */
		$this->_debug = false; /* set this to false if you want to suppress debug output. */
		$this->_socket = NULL;
		$this->_command_status = 0;
		$this->_sequence_number = 1;
		$this->_source_address = "";
		$this->_message_sequence = rand(1,255);
		$this->_message_id = ""; 
		$this->dlvrSms = array();
        $this->_dest_addr = null; 
	}

	/*
	For SMS gateways that support sender-ID branding, the method
	can be used to set the originating address.
	Parameters:
		$from	:	Originating address
	Example:
		$smpp->SetSender("31495595392");
	*/
	function SetSender($from)
	{
		if (strlen($from) > 20) {
			$this->debug("Error: sender id too long.<br />");
			return;
		}
		$this->_source_address = $from;
	}

	/*
	This method initiates an SMPP session.
	It is to be called BEFORE using the Send() method.
	Parameters:
		$host		: SMPP ip to connect to.
		$port		: port # to connect to.
		$username	: SMPP system ID
		$password	: SMPP passord.
		$system_type	: SMPP System type
	Returns:
		true if successful, otherwise false
	Example:
		$smpp->Start("smpp.chimit.nl", 2345, "chimit", "my_password", "client01");
	*/
	function Start($host, $port, $username, $password, $system_type)
	{
/*
		$testarr = stream_get_transports();
		$have_tcp = false;
		reset($testarr);
		while (list(, $transport) = each($testarr)) {
			if ($transport == "tcpp") {
				$have_tcp = true;
			}
		}
		if (!$have_tcp) {
			$this->debug("No TCP support in this version of PHP.<br />");
			return false;
		}
*/
		$this->_socket = fsockopen($host, $port, $errno, $errstr, 20);
		// todo: sanity check on input parameters
		if (!$this->_socket) {
			$this->debug("Error opening SMPP session.<br />");
			$this->debug("Error was: $errstr.<br />");
			return;
		}
		socket_set_timeout($this->_socket, 1200);
		$status = $this->SendBindTransceiver($username, $password, $system_type);
		if ($status != 0) {
			$this->debug("Error binding to SMPP server. Invalid credentials?<br />");
		}
		return ($status == 0);
	}
	
	
	//
	// Возвращает ID посланного сообщения выданного SMSC
	//
	public function GetMessageID(){
	
			return $this->_message_id; 
	}

	/*
	This method sends out one SMS message.
	Parameters:
		$to	: destination address.
		$text	: text of message to send.
		$unicode: Optional. Indicates if input string is html encoded unicode.
	Returns:
		true if messages sent successfull, otherwise false.
	Example:
		$smpp->Send("31649072766", "This is an SMPP Test message.");
		$smpp->Send("31648072766", "&#1589;&#1576;&#1575;&#1581;&#1575;&#1604;&#1582;&#1610;&#1585;", true);
	*/
	function Send($to, $text, $unicode = false,$time = "",$flash = false)
	{
		if (strlen($to) > 20) {
			$this->debug("to-address too long.<br />");
			return;
		}
		if (!$this->_socket) {
			$this->debug("Not connected, while trying to send SUBMIT_SM.<br />");
			// return;
		}
		$service_type = "";
		//default source TON and NPI for international sender
		$source_addr_ton = 1;
		$source_addr_npi = 1;
		$source_addr = $this->_source_address;
                
		if (preg_match('/\D/', $source_addr)) //alphanumeric sender
		{
			$source_addr_ton = 5;
			$source_addr_npi = 0;
		}
		elseif (strlen($source_addr) < 11) //national or shortcode sender
		{
			$source_addr_ton = 5;
			$source_addr_npi = 0;
		}
		$dest_addr_ton = 1;
		$dest_addr_npi = 1;
		$destination_addr = $to;
                $this->_dest_addr = $to;
		$esm_class = 3;
		$protocol_id = 0;
		$priority_flag = 0;
		$schedule_delivery_time = $time;
		$validity_period = $time;
		$registered_delivery_flag = 1;
		$replace_if_present_flag = 0;
			if($flash)
				$data_coding = 240;
			else
				$data_coding = 241;
		$sm_default_msg_id = 0;
		if ($unicode) {
			$text = mb_convert_encoding($text, "UCS-2BE", "HTML-ENTITIES"); /* UCS-2BE */
			$data_coding = 8; /* UCS2 */
			$multi = $this->split_message_unicode($text);
		}
		else {
			$multi = $this->split_message($text);
		}
		$multiple = (count($multi) > 1);
		if ($multiple) {
			$esm_class += 0x00000040;
		}
		$result = true;
		reset($multi);
		while (list(, $part) = each($multi)) {
			$short_message = $part;
			$sm_length = strlen($short_message);
			$status = $this->SendSubmitSM($service_type, $source_addr_ton, $source_addr_npi, $source_addr, $dest_addr_ton, $dest_addr_npi, $destination_addr, $esm_class, $protocol_id, $priority_flag, $schedule_delivery_time, $validity_period, $registered_delivery_flag, $replace_if_present_flag, $data_coding, $sm_default_msg_id, $sm_length, $short_message);
			if ($status != 0) {
				$this->debug("SMPP server returned error $status.<br />");
				$result = false;
			}
		}
		return $result;
	}

	/*
	This method ends a SMPP session.
	Parameters:
		none
	Returns:
		true if successful, otherwise false
	Example: $smpp->End();
	*/
	function End()
	{
		if (!$this->_socket) {
			// not connected
			return;
		}
		$status = $this->SendUnbind();
		if ($status != 0) {
			$this->debug("SMPP Server returned error $status.<br />");
		}
		fclose($this->_socket);
		$this->_socket = NULL;
		return ($status == 0);
	}

	/*
	This method sends an enquire_link PDU to the server and waits for a response.
	Parameters:
		none
	Returns:
		true if successfull, otherwise false.
	Example: $smpp->TestLink()
	*/
	function TestLink()
	{
		$pdu = "";
		$status = $this->SendPDU(CM_ENQUIRELINK, $pdu);
		return ($status == 0);
	}
	
	
	function StatusSMS($message_id)
	{
		if (!$this->_socket) {
			// not connected
			return;
		}
		$pdu = "";

		$message_id = $message_id;
		
		//default source TON and NPI for international sender
		$source_addr_ton = 1;
		$source_addr_npi = 1;
		$source_addr = $this->_source_address;
		if (preg_match('/\D/', $source_addr)) //alphanumeric sender
		{
			$source_addr_ton = 5;
			$source_addr_npi = 0;
		}
		elseif (strlen($source_addr) < 11) //national or shortcode sender
		{
			$source_addr_ton = 2;
			$source_addr_npi = 1;
		}	
		
		$status = $this->SendQuerySM($message_id,$source_addr_ton,$source_addr_npi,$source_addr);
		
		if ($status != 0) {
			$this->debug("SMPP server returned error $status.<br />");
		}
		
		return $status; 
		
	}

	/*
	This method sends a single message to a comma separated list of phone numbers.
	There is no limit to the number of messages to send.
	Parameters:
		$tolist		: comma seperated list of phone numbers
		$text		: text of message to send
		$unicode: Optional. Indicates if input string is html encoded unicode string.
	Returns:
		true if messages received by smpp server, otherwise false.
	Example:
		$smpp->SendMulti("31777110204,31649072766,...,...", "This is an SMPP Test message.");
	*/
	function SendMulti($tolist, $text, $unicode = false)
	{
		if (!$this->_socket) {
			$this->debug("Not connected, while trying to send SUBMIT_MULTI.<br />");
			// return;
		}
		$service_type = "";
		$source_addr = $this->_source_address;
		//default source TON and NPI for international sender
		$source_addr_ton = 1;
		$source_addr_npi = 1;
		$source_addr = $this->_source_address;
		if (preg_match('/\D/', $source_addr)) //alphanumeric sender
		{
			$source_addr_ton = 5;
			$source_addr_npi = 0;
		}
		elseif (strlen($source_addr) < 11) //national or shortcode sender
		{
			$source_addr_ton = 2;
			$source_addr_npi = 1;
		}
		$dest_addr_ton = 1;
		$dest_addr_npi = 1;
		$destination_arr = explode(",", $tolist);
		$esm_class = 3;
		$protocol_id = 0;
		$priority_flag = 0;
		$schedule_delivery_time = "";
		$validity_period = "";
		$registered_delivery_flag = 0;
		$replace_if_present_flag = 0;
		$data_coding = 241;
		$sm_default_msg_id = 0;
		if ($unicode) {
			$text = mb_convert_encoding($text, "UCS-2BE", "HTML-ENTITIES");
			$data_coding = 8; /* UCS2 */
			$multi = $this->split_message_unicode($text);
		}
		else {
			$multi = $this->split_message($text);
		}
		$multiple = (count($multi) > 1);
		if ($multiple) {
			$esm_class += 0x00000040;
		}
		$result = true;
		reset($multi);
		while (list(, $part) = each($multi)) {
			$short_message = $part;
			$sm_length = strlen($short_message);
			$status = $this->SendSubmitMulti($service_type, $source_addr_ton, $source_addr_npi, $source_addr, $dest_addr_ton, $dest_addr_npi, $destination_arr, $esm_class, $protocol_id, $priority_flag, $schedule_delivery_time, $validity_period, $registered_delivery_flag, $replace_if_present_flag, $data_coding, $sm_default_msg_id, $sm_length, $short_message);
			if ($status != 0) {
				$this->debug("SMPP server returned error $status.<br />");
				$result = false;
			}
		}
		return $result;
	}

// private members (not documented):

	function ExpectPDU($our_sequence_number)
	{
		do {
			$this->debug("Trying to read PDU.<br />");
			if (feof($this->_socket)) {
				$this->debug("Socket was closed.!!<br />");
			}
			$elength = fread($this->_socket, 4);
			if (empty($elength)) {
				$this->debug("Connection lost.<br />");
				return;
			}
			extract(unpack("Nlength", $elength));
			$this->debug("Reading PDU     : $length bytes.<br />");
			$stream = fread($this->_socket, $length - 4);
			$this->debug("Stream len      : " . strlen($stream) . "<br />");
			extract(unpack("Ncommand_id/Ncommand_status/Nsequence_number", $stream));
			$command_id &= 0x0fffffff;
			$this->debug("Command id      : $command_id.<br />");
			$this->debug("Command status  : $command_status.<br />");
			$this->debug("sequence_number : $sequence_number.<br />");
			$pdu = substr($stream, 12);
			switch ($command_id) {
			case CM_BIND_TRANSMITTER:
				$this->debug("Got CM_BIND_TRANSMITTER_RESP.<br />");
				$spec = "asystem_id";
				extract($this->unpack2($spec, $pdu));
				$this->debug("system id       : $system_id.<br />");
				break;
			case CM_BIND_TRANSCEIVER:
				$this->debug("Got CM_BIND_TRANSCEIVER_RESP.<br />");
				$spec = "asystem_id";
				extract($this->unpack2($spec, $pdu));
				$this->debug("system id       : $system_id.<br />");
				break;
			case CM_UNBIND:
				$this->debug("Got CM_UNBIND_RESP.<br />");
				break;
			case CM_SUBMIT_SM:
				$this->debug("Got CM_SUBMIT_SM_RESP.<br />");
				if ($command_status == 0) {
					$spec = "amessage_id";
					extract($this->unpack2($spec, $pdu));
					$this->debug("message id      : $message_id.<br />");
					$this->_message_id = $message_id;                                       
				}
				break;
				
			case CM_DELIVER_SM:
                     
                    $body = substr($stream, 8, $length);
					/*$res = $this->parseSMS($body, $sequence_number);					
					echo "<br />$length<hr />";
					var_dump($stream);
					echo "<hr />";
					var_dump($body);
					echo "<hr />";
					var_dump($res);
					echo "<hr />";
					$state = $this->procsessms($res);*/
					
					$st = mb_strpos($stream,"Zid:");
					$ls = mb_strrpos($stream, ":")+8;
					
					$sms['short_message'] = mb_substr($stream, $st,  $ls-$st);
					$state = $this->procsessms($sms);
					
					$stat = substr($state['stat'],0,7);
					//$this->debug("dest_addr: ".$res['source_addr']."<br />");
					$this->debug("id_message: ".$state['Zid']."<br />");
					$this->debug("status: ".substr($state['stat'],0,7)."<br />");                                       
				break;		
				
			case CM_QUERY_SM:
				$this->debug("Got CM_QUERY_SM_RESP.<br />");
				if ($command_status == 0) {					
					$spec = "amessage_id/cfinal_date/cmessage_state/cerror_code";
					//extract($this->unpack2($spec, $pdu));
					extract($this->unpack2($spec, $pdu));
					$this->debug("final_date     : $final_date.<br />");
					$this->debug("message_state      : $message_state.<br />");
					$this->debug("mess_id      : $message_id.<br />");
					$this->debug("error_code     : $error_code.<br />");
				}
			break;
			case CM_SUBMIT_MULTI:
				$this->debug("Got CM_SUBMIT_MULTI_RESP.<br />");
				$spec = "amessage_id/cno_unsuccess/";
				extract($this->unpack2($spec, $pdu));
				$this->debug("message id      : $message_id.<br />");
				$this->debug("no_unsuccess    : $no_unsuccess.<br />");
				break;
			case CM_ENQUIRELINK:
				$this->debug("GOT CM_ENQUIRELINK_RESP.<br />");
				break;
			default:
				$this->debug("Got unknown SMPP pdu.<br />");
				break;
			}
			$this->debug("<br />Received PDU: ");
			for ($i = 0; $i < strlen($stream); $i++) {
				if (ord($stream[$i]) < 32) $this->debug("(" . ord($stream[$i]) . ")"); else $this->debug($stream[$i]);
			}
			$this->debug("<br />");
		} while ($sequence_number != $our_sequence_number);
		return $command_status;
	}
	
	
	function SendPDU($command_id, $pdu)
	{
		$length = strlen($pdu) + 16;
		$header = pack("NNNN", $length, $command_id, $this->_command_status, $this->_sequence_number);
		$this->debug("Sending PDU, len == $length<br />");
		$this->debug("Sending PDU, header-len == " . strlen($header) .  "<br />");
		$this->debug("Sending PDU, command_id == " . $command_id  .  "<br />");
		fwrite($this->_socket, $header . $pdu, $length);
		$status = $this->ExpectPDU($this->_sequence_number);
		$this->_sequence_number = $this->_sequence_number + 1;
		return $status;
	}

	function SendBindTransmitter($system_id, $smpppassword, $system_type)
	{
		$system_id = $system_id . chr(0);
		$system_id_len = strlen($system_id);
		$smpppassword = $smpppassword . chr(0);
		$smpppassword_len = strlen($smpppassword);
		$system_type = $system_type . chr(0);
		$system_type_len = strlen($system_type);
		$pdu = pack("a{$system_id_len}a{$smpppassword_len}a{$system_type_len}CCCa1", $system_id, $smpppassword, $system_type, 0x33, 0, 0, chr(0));
		$this->debug("Bind Transmitter PDU: ");
		for ($i = 0; $i < strlen($pdu); $i++) {
			$this->debug(ord($pdu[$i]) . " ");
		}
		$this->debug("<br />");
		$status = $this->SendPDU(CM_BIND_TRANSMITTER, $pdu);
		return $status;
	}
	
	function SendBindTransceiver($system_id, $smpppassword, $system_type)
	{
		$system_id = $system_id . chr(0);
		$system_id_len = strlen($system_id);
		$smpppassword = $smpppassword . chr(0);
		$smpppassword_len = strlen($smpppassword);
		$system_type = $system_type . chr(0);
		$system_type_len = strlen($system_type);
		$pdu = pack("a{$system_id_len}a{$smpppassword_len}a{$system_type_len}CCCa1", $system_id, $smpppassword, $system_type, 0x33, 0, 0, chr(0));
		$this->debug("Bind Transceiver PDU: ");
		for ($i = 0; $i < strlen($pdu); $i++) {
			$this->debug(ord($pdu[$i]) . " ");
		}
		$this->debug("<br />");
		$status = $this->SendPDU(CM_BIND_TRANSCEIVER, $pdu);
		return $status;
	}

	function SendUnbind()
	{
		$pdu = "";
		$status = $this->SendPDU(CM_UNBIND, $pdu);
		return $status;
	}

	function SendSubmitSM($service_type, $source_addr_ton, $source_addr_npi, $source_addr, $dest_addr_ton, $dest_addr_npi, $destination_addr, $esm_class, $protocol_id, $priority_flag, $schedule_delivery_time, $validity_period, $registered_delivery_flag, $replace_if_present_flag, $data_coding, $sm_default_msg_id, $sm_length, $short_message)
	{
		$service_type = $service_type . chr(0);
		$service_type_len = strlen($service_type);
		$source_addr = $source_addr . chr(0);
		$source_addr_len = strlen($source_addr);
		$destination_addr = $destination_addr . chr(0);
		$destination_addr_len = strlen($destination_addr);
		$schedule_delivery_time = $schedule_delivery_time . chr(0);
		$schedule_delivery_time_len = strlen($schedule_delivery_time);
		$validity_period = $validity_period . chr(0);
		$validity_period_len = strlen($validity_period);
		// $short_message = $short_message . chr(0);
		$message_len = $sm_length;
		$spec = "a{$service_type_len}cca{$source_addr_len}cca{$destination_addr_len}ccca{$schedule_delivery_time_len}a{$validity_period_len}ccccca{$message_len}";
		$this->debug("PDU spec: $spec.<br />");

		$pdu = pack($spec,
			$service_type,
			$source_addr_ton,
			$source_addr_npi,
			$source_addr,
			$dest_addr_ton,
			$dest_addr_npi,
			$destination_addr,
			$esm_class,
			$protocol_id,
			$priority_flag,
			$schedule_delivery_time,
			$validity_period,
			$registered_delivery_flag,
			$replace_if_present_flag,
			$data_coding,
			$sm_default_msg_id,
			$sm_length,
			$short_message);
		$status = $this->SendPDU(CM_SUBMIT_SM, $pdu);
		return $status;
	}

	function SendSubmitMulti($service_type, $source_addr_ton, $source_addr_npi, $source_addr, $dest_addr_ton, $dest_addr_npi, $destination_arr, $esm_class, $protocol_id, $priority_flag, $schedule_delivery_time, $validity_period, $registered_delivery_flag, $replace_if_present_flag, $data_coding, $sm_default_msg_id, $sm_length, $short_message)
	{
		$service_type = $service_type . chr(0);
		$service_type_len = strlen($service_type);
		$source_addr = $source_addr . chr(0);
		$source_addr_len = strlen($source_addr);
		$number_destinations = count($destination_arr);
		$dest_flag = 1;
		$spec = "a{$service_type_len}cca{$source_addr_len}c";
		$pdu = pack($spec,
			$service_type,
			$source_addr_ton,
			$source_addr_npi,
			$source_addr,
			$number_destinations
		);

		$dest_flag = 1;
		reset($destination_arr);
		while (list(, $destination_addr) = each($destination_arr)) {
			$destination_addr .= chr(0);
			$dest_len = strlen($destination_addr);
			$spec = "ccca{$dest_len}";
			$pdu .= pack($spec, $dest_flag, $dest_addr_ton, $dest_addr_npi, $destination_addr);
		}
		$schedule_delivery_time = $schedule_delivery_time . chr(0);
		$schedule_delivery_time_len = strlen($schedule_delivery_time);
		$validity_period = $validity_period . chr(0);
		$validity_period_len = strlen($validity_period);
		$message_len = $sm_length;
		$spec = "ccca{$schedule_delivery_time_len}a{$validity_period_len}ccccca{$message_len}";

		$pdu .= pack($spec,
			$esm_class,
			$protocol_id,
			$priority_flag,
			$schedule_delivery_time,
			$validity_period,
			$registered_delivery_flag,
			$replace_if_present_flag,
			$data_coding,
			$sm_default_msg_id,
			$sm_length,
			$short_message);

		$this->debug("<br />Multi PDU: ");
		for ($i = 0; $i < strlen($pdu); $i++) {
			if (ord($pdu[$i]) < 32) $this->debug("."); else $this->debug($pdu[$i]);
		}
		$this->debug("<br />");

		$status = $this->SendPDU(CM_SUBMIT_MULTI, $pdu);
		return $status;
	}
	
	function SendQuerySM($message_id,$source_addr_ton,$source_addr_npi,$source_addr){
		
		$message_id = $message_id.chr(0);
		$message_id_len  = strlen($message_id);
		$source_addr_ton = $source_addr_ton.chr(0);
		$source_addr_npi = $source_addr_npi.chr(0);
		$source_addr = $source_addr . chr(0);
		$source_addr_len = strlen($source_addr);					
		
		$pdu = pack("a{$message_id_len}cca{$source_addr_len}",$message_id,$source_addr_ton,$source_addr_npi,$source_addr);		
		$status = $this->SendPDU(CM_QUERY_SM, $pdu);
		return $status;
	
	}

	function split_message($text)
	{
		$this->debug("In split_message.<br />");
		$max_len = 153;
		$res = array();
		if (strlen($text) <= 160) {
			$this->debug("One message: " . strlen($text) . "<br />");
			$res[] = $text;
			return $res;
		}
		$pos = 0;
		$msg_sequence = $this->_message_sequence++;
		$num_messages = ceil(strlen($text) / $max_len);
		$part_no = 1;
		while ($pos < strlen($text)) {
			$ttext = substr($text, $pos, $max_len);
			$pos += strlen($ttext);
			$udh = pack("cccccc", 5, 0, 3, $msg_sequence, $num_messages, $part_no);
			$part_no++;
			$res[] = $udh . $ttext;
			$this->debug("Split: UDH = ");
			for ($i = 0; $i < strlen($udh); $i++) {
				$this->debug(ord($udh[$i]) . " ");
			}
			$this->debug("<br />");
			$this->debug("Split: $ttext.<br />");
		}
		return $res;
	}

	function split_message_unicode($text)
	{
		$this->debug("In split_message.<br />");
		$max_len = 134;
		$res = array();
		if (mb_strlen($text) <= 140) {
			$this->debug("One message: " . mb_strlen($text) . "<br />");
			$res[] = $text;
			return $res;
		}
		$pos = 0;
		$msg_sequence = $this->_message_sequence++;
		$num_messages = ceil(mb_strlen($text) / $max_len);
		$part_no = 1;
		while ($pos < mb_strlen($text)) {
			$ttext = mb_substr($text, $pos, $max_len);
			$pos += mb_strlen($ttext);
			$udh = pack("cccccc", 5, 0, 3, $msg_sequence, $num_messages, $part_no);
			$part_no++;
			$res[] = $udh . $ttext;
			$this->debug("Split: UDH = ");
			for ($i = 0; $i < strlen($udh); $i++) {
				$this->debug(ord($udh[$i]) . " ");
			}
			$this->debug("<br />");
			$this->debug("Split: $ttext.<br />");
		}
		return $res;
	}

	function unpack2($spec, $data)
	{
		$res = array();
		$specs = explode("/", $spec);
		$pos = 0;
		reset($specs);
		while (list(, $sp) = each($specs)) {
			$subject = substr($data, $pos);
			$type = substr($sp, 0, 1);
			$var = substr($sp, 1);
			switch ($type) {
			case "N":
				$temp = unpack("Ntemp2", $subject);
				$res[$var] = $temp["temp2"];
				$pos += 4;
				break;
			case "c":
				$temp = unpack("ctemp2", $subject);
				$res[$var] = $temp["temp2"];
				$pos += 1;
				break;
			case "a":
				$pos2 = strpos($subject, chr(0)) + 1;
				$temp = unpack("a{$pos2}temp2", $subject);
				$res[$var] = $temp["temp2"];
				$pos += $pos2;
				break;
			}
		}
		return $res;
	}

        function procsessms($sms)
        {


        $mass=array();

        $dl = explode(' ', $sms['short_message']);

        foreach($dl as $arr){
                $res = explode(':',$arr);
                        if(isset($res[1])) $mass[$res[0]]=$res[1];
        }

        return $mass;
        }
	
	function parseSMS($body, $sequence_number)
	{
		//check command id

		//unpack PDU
		$ar = unpack("C*", $body);
		//$ar=unpack("C*",$pdu['body']);
		$sms = array('service_type' => $this->getString($ar, 6), 'source_addr_ton' => array_shift($ar), 'source_addr_npi' => array_shift($ar), 'source_addr' => $this->getString($ar, 21), 'dest_addr_ton' => array_shift($ar), 'dest_addr_npi' => array_shift($ar), 'destination_addr' => $this->getString($ar, 21), 'esm_class' => array_shift($ar), 'protocol_id' => array_shift($ar), 'priority_flag' => array_shift($ar), 'schedule_delivery_time' => array_shift($ar), 'validity_period' => array_shift($ar), 'registered_delivery' => array_shift($ar), 'replace_if_present_flag' => array_shift($ar), 'data_coding' => array_shift($ar), 'sm_default_msg_id' => array_shift($ar), 'sm_length' => array_shift($ar), 'short_message' => $this->getString($ar, 255));

		// $this->_sequence_number=$sequence_number;

		//send responce of recieving sms

		return $sms;
	}
	
	function getString(&$ar, $maxlen = 255)
	{
		$s = "";
		$i = 0;
		do {

			$c = array_shift($ar);
			if ($c != 0)
				$s .= chr($c);
			$i++;
		} while ($i < $maxlen && $c != 0);
		return $s;
	}

	/**
	* @private function
	* Prints the binary string as hex bytes.
	* @param $maxlen - maximum length to read.
	*/
	function printHex($pdu)
	{
		$a = "";
		$ar = unpack("C*", $pdu);
		foreach ($ar as $v) {
			$s = dechex($v);
			if (strlen($s) < 2)
				$s = "0$s";
			$a .= "$s ";
		}
		return $a . "<br />";
	}
      
// преобразование русских символов в html-символы для перевода в UTF-8
function strToHTML($text) {
                $trans = array(
                                        "а" => "&#1072;",
                                        "б" => "&#1073;",
                                        "в" => "&#1074;",
                                        "г" => "&#1075;",
                                        "д" => "&#1076;",
                                        "е" => "&#1077;",
                                        "ё" => "&#1105;",
                                        "ж" => "&#1078;",
                                        "з" => "&#1079;",
                                        "и" => "&#1080;",
                                        "й" => "&#1081;",
                                        "к" => "&#1082;",
                                        "л" => "&#1083;",
                                        "м" => "&#1084;",
                                        "н" => "&#1085;",
                                        "о" => "&#1086;",
                                        "п" => "&#1087;",
                                        "р" => "&#1088;",
                                        "с" => "&#1089;",
                                        "т" => "&#1090;",
                                        "у" => "&#1091;",
                                        "ф" => "&#1092;",
                                        "х" => "&#1093;",
                                        "ц" => "&#1094;",
                                        "ч" => "&#1095;",
                                        "ш" => "&#1096;",
                                        "щ" => "&#1097;",
										"ъ" => "&#1098;",
                                        "ы" => "&#1099;",
										"ь" => "&#1100;",
                                        "э" => "&#1101;",
                                        "ю" => "&#1102;",
                                        "я" => "&#1103;",
										"і" => "&#1110;",
										"ї"=>  "&#1111;",
                                        "є" => "&#1108;",
										"ӯ" => "&#1263;",
										"Ӯ" => "&#1262;",
										"ӣ" => "&#1251;",
										"Ӣ" => "&#1250;",
										"Қ" => "&#1178;",
										"қ" => "&#1179;",
										"Ғ" => "&#1170;",
										"ғ" => "&#1171;",
										"ҳ" => "&#1203;",
										"Ҳ" => "&#1202;",
										"Ҷ" => "&#1206;",
										"ҷ" => "&#1207;",
										
                                        "А" => "&#1040;",
                                        "Б" => "&#1041;",
                                        "В" => "&#1042;",
                                        "Г" => "&#1043;",
                                        "Д" => "&#1044;",
                                        "Е" => "&#1045;",
                                        "Ё" => "&#1025;",
                                        "Ж" => "&#1046;",
                                        "З" => "&#1047;",
										"І" => "&#1030;",
                                        "И" => "&#1048;",
                                        "Й" => "&#1049;",
                                        "К" => "&#1050;",
                                        "Л" => "&#1051;",
                                        "М" => "&#1052;",
                                        "Н" => "&#1053;",
                                        "О" => "&#1054;",
                                        "П" => "&#1055;",
                                        "Р" => "&#1056;",
                                        "С" => "&#1057;",
                                        "Т" => "&#1058;",
                                        "У" => "&#1059;",
                                        "Ф" => "&#1060;",
                                        "Х" => "&#1061;",
                                        "Ц" => "&#1062;",
                                        "Ч" => "&#1063;",
                                        "Ш" => "&#1064;",
                                        "Щ" => "&#1065;",
										"Ъ" => "&#1066;",
                                        "Ы" => "&#1067;",
										"Ь" => "&#1068;",
                                        "Э" => "&#1069;",
                                        "Ю" => "&#1070;",
                                        "Я" => "&#1071;",
										"Ї" => "&#1031;",
                                        "Є" => "&#1028;",
										
										"«" => "&laquo;",
										"»" => "&raquo;"
                                );

                if(preg_match("/[а-яА-Я]/", $text)) {
                        return strtr($text, $trans);
                }
                else {
                        return $text;
                }
        }	  
        
	function debug($str)
	{
		if ($this->_debug) {
			echo $str;
		}
	}
};

