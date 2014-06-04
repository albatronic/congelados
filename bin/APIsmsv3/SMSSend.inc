<?php 
// V3.0 Incluye remite y Eventos programados
class HTTP_Client 
{ 
	var $host; 
	var $port; 
	var $socket; 
	var $errno; 
	var $errstr; 
	var $timeout; 
	var $buf; 
	var $result; 
	var $post_data; 
	var $path = "/"; 
	var $agent_name = "ArsysRequestAgent";

	//Constructor, timeout 30s 
	function HTTP_Client($host, $port, $timeout = 30) 
	{ 
		$this->host = $host; 
		$this->port = $port; 
		$this->timeout = $timeout; 
	} 

	//Opens a connection 
	function connect() 
	{ 
		$this->socket = fsockopen($this->host, 
		$this->port, 
		$this->errno, 
		$this->errstr, 
		$this->timeout 
		); 
		if(!$this->socket) {
			return false; 
		}
		else {
			return true;
		}
	} 

	//Set the path 
	function set_path($path) 
	{ 
		$this->path = $path; 
	} 

	//Send request and clean up 
	function send_request() 
	{ 
		if(!$this->connect()) 
		{ 
			return -1;
		} 
		else 
		{ 
			$this->result = $this->request($this->post_data); 
			return $this->result; 
		} 
	} 

	function request($post_data) 
	{ 
		$this->buf = ""; 
		fwrite($this->socket, 
		"GET $this->path HTTP/1.0\r\n". 
		"Host:$this->host\r\n". 
		"User-Agent: $this->agent_name\r\n". 
		"Content-Type: application/xml\r\n". 
		"Content-Length: ".strlen($post_data). 
		"\r\n". 
		"\r\n".$post_data.
		"\r\n" 
		); 

		while(!feof($this->socket)) 
		$this->buf .= fgets($this->socket, 2048);
		$this->close(); 
		$resultado = preg_replace("/[^<]*/", "", $this->buf,1);
		if (!$resultado) {return -1;}
 	   else {return $resultado;}
	} 
	
	function close() 
	{ 
		fclose($this->socket); 
	} 
}

define ("periodUnica",1);
define ("periodDiaria",2);
define ("periodSemanal",3);
define ("periodMensual",4);
define ("periodAnual",5);


class smsItem {

	var $phone_number ="";
	var $text_sms	  ="";

	var $id           =-1;
	var $pwd          ="";

	var $from			 ="";
	var $descriptionEP ="";
	var $dateEP 		="";
	var $timeEP 		="";
	var $periodEP	 	="";
	var $credit 		="";


	var $url          ="";
	var $smsReferer   ="";
	var $smsHost      ="";

	var $smsResult      ="";
	var $smsDescription ="";
	var $smsCredit		  ="";

   var $server = "sms.arsys.es"; 
   var $port = "8080";
   var $servletSend = "/smsarsys/accion/enviarSms2.jsp";
   var $servletProgram = "/smsarsys/accion/ProgramarSmsEP.jsp";


	function smsItem(){}

	function getAccount(){return $this->id;}
	function setAccount($id_value){$this->id=$id_value;}

	function getPwd(){return $this->pwd;}
	function setPwd($pwd_value){$this->pwd=$pwd_value;}

	function getTo(){return $this->phone_number;}
	function setTo($phone_number_value){$this->phone_number=$phone_number_value;}

	function getText(){return $this->text_sms;}
	function setText($text_sms_value){$this->text_sms=$text_sms_value;}

	function getReferer(){return $this->smsReferer;}
	function setReferer($referer_value){$this->smsReferer=$referer_value;}

	function getHost(){return $this->smsHost;}
	function setHost($host_value){$this->smsHost=$host_value;}

	function getFrom(){return $this->from;}
	function setFrom($from_value){$this->from=$from_value;}

	function getDescriptionEP(){return $this->descriptionEP;}
	function setDescriptionEP($descriptionEP_value){$this->descriptionEP=$descriptionEP_value;}

	function getDateEP(){return $this->dateEP;}
	function setDateEP($dateEP_value){$this->dateEP=$dateEP_value;}

	function getTimeEP(){return $this->timeEP;}
	function setTimeEP($timeEP_value){$this->timeEP=$timeEP_value;}

	function getPeriodEP(){return $this->periodEP;}
	function setPeriodEP($periodEP_value){$this->periodEP=$periodEP_value;}

	function getResult(){return $this->smsResult;}
	function getDescription(){return  $this->smsDescription;}
	function getCredit(){return  $this->smsCredit;}

	
	function Send() {
		$param=$this->param."?id=".$this->id.
			"&phoneNumber=".$this->phone_number.
			"&psw=".$this->pwd.
			"&textSms=".urlencode($this->text_sms)
			;

			if (($this->from) != "")
			 	$param.="&remite=".urlencode($this->from);

			$url = $this->servletSend.$param;
			$httpclient = new HTTP_Client($this->server,$this->port, $timeout = 30);
			$httpclient->path = $url;
			$resultado = $httpclient->send_request();

			if($resultado == -1)
			{
				$this->smsResult="KO";
				$this->smsDescription="Error de conexión al servidor SMS";
			}
			else
 		   {
				$simple = $resultado;
				// Decodifico el resultado
				$p = xml_parser_create();
				$skipWhite = xml_parser_set_option ($p,XML_OPTION_SKIP_WHITE,1); 
				xml_parse_into_struct($p,$simple,$vals,$index);
				xml_parser_free($p);
	
				$auxResult = $index[RESULT][0];
				$estadoResult = $vals[$auxResult][value];
	
				$auxDescription = $index[DESCRIPTION][0];
				$estadoDescription = $vals[$auxDescription][value];

				$auxCredito = $index[CREDIT][0];
				$estadoCredito = $vals[$auxCredito][value];
	
				$this->smsResult=$estadoResult;
				$this->smsDescription=$estadoDescription;
				$this->smsCredit=$estadoCredito;
			}
			return $resultado;
	}

	function Program() {
		$param=$this->param."?id=".$this->id.
			"&phoneNumber=".urlencode($this->phone_number).
			"&psw=".$this->pwd.
			"&textSms=".urlencode($this->text_sms).
			"&descripcion=".urlencode($this->descriptionEP).
			"&fecha=".$this->dateEP.
			"&hora=".$this->timeEP.
			"&periodicidadEnvio=".$this->periodEP
			;

			if (($this->from) != "")
			 	$param.="&remite=".urlencode($this->from);
 
			$url = $this->servletProgram.$param;
			$httpclient = new HTTP_Client($this->server,$this->port, $timeout = 30);
			$httpclient->path = $url;
			$resultado = $httpclient->send_request();

			if($resultado == -1)
			{
				$this->smsResult="KO";
				$this->smsDescription="Error de conexión al servidor SMS";
			}
			else
 		   {
				$simple = $resultado;
				// Decodifico el resultado
				$p = xml_parser_create();
				$skipWhite = xml_parser_set_option ($p,XML_OPTION_SKIP_WHITE,1); 
				xml_parse_into_struct($p,$simple,$vals,$index);
				xml_parser_free($p);
	
				$auxResult = $index[RESULT][0];
				$estadoResult = $vals[$auxResult][value];
	
				$auxDescription = $index[DESCRIPTION][0];
				$estadoDescription = $vals[$auxDescription][value];

				$auxCredito = $index[CREDIT][0];
				$estadoCredito = $vals[$auxCredito][value];
	
				$this->smsResult=$estadoResult;
				$this->smsDescription=$estadoDescription;
				$this->smsCredit=$estadoCredito;

			}
			return $resultado;

	}
}
?> 
