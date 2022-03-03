<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\SMS\SMSUslugi;


class Transport{

    static public  $HTTPS_LOGIN     = ""; //Ваш логин для HTTPS-протокола
    static public  $HTTPS_PASSWORD  = ""; //Ваш пароль для HTTPS-протокола
    static public  $HTTPS_ADDRESS   = "https://lcab.sms-uslugi.ru/"; //HTTPS-Адрес, к которому будут обращаться скрипты. Со слэшем на конце.
    static public  $HTTP_ADDRESS    = "http://lcab.sms-uslugi.ru/"; //HTTP-Адрес, к которому будут обращаться скрипты. Со слэшем на конце.
    static public  $HTTPS_METHOD    = "curl"; //метод, которым отправляется запрос (curl)
    static public  $USE_HTTPS       = 1; //1 - использовать HTTPS-адрес, 0 - HTTP
    
    //Класс попытается автоматически определить кодировку ваших скриптов. 
    //Если вы хотите задать ее сами в параметре HTTPS_CHARSET, то укажите HTTPS_CHARSET_AUTO_DETECT значение FALSE
    static public  $HTTPS_CHARSET_AUTO_DETECT = false;
      
    static public  $HTTPS_CHARSET = "utf-8"; //кодировка ваших скриптов. cp1251 - для Windows-1251, либо же utf-8 для, сообственно - utf-8 :)    

	private $charset; //Кодировка
	
	/**
    * Проверка баланса
    * 
    */
	function balance()
    {
		return $this->get( $this->request("balance"), "account" );
	}
	
    /**
    * Запрос отчёта по отправкам
    * 
    * @param string $start - дата начала формирования отчёта
    * @param string $stop - дата окончания формирования отчёта
    * @param array $dop - доп. сведения для фильтра
    */
	function reports($start = "0000-00-00", $stop = "0000-00-00", $dop = [])
    {
		if (!isset($dop["source"])) $dop["source"] = "%";
		if (!isset($dop["number"])) $dop["number"] = "%";
		
		$result = $this->request("report", [
			"start" => $start,
			"stop" => $stop,
			"source" => $dop["source"],
			"number" => $dop["number"],
        ]);
		if ($this->get($result, "code") != 1){
			$return =  ["code" => $this->get($result, "code"), "descr" => $this->get($result, "descr")];
		}
		else{
			$return =  [
				"code" => $this->get($result, "code"), 
				"descr" => $this->get($result, "descr"),
            ];
			if (isset($result['sms'])) $return["sms"] = $result['sms'];
		}
		return $return;
	}
	
    /**
    * Детальный отчёт по SMS с нужным id
    * 
    * @param mixed $smsid
    */
	function detailReport($smsid)
    {
		$result = $this->request("report", ["smsid" => $smsid]);
		if ($this->get($result, "code") != 1){
			$return =  ["code" => $this->get($result, "code"), "descr" => $this->get($result, "descr")];
		}
		else{
			$detail = $result["detail"];
			$return =  [
				"code" => $this->get($result, "code"), 
				"descr" => $this->get($result, "descr"),
				"delivered" => $detail['delivered'],
				"notDelivered" => $detail['notDelivered'],
				"waiting" => $detail['waiting'],
				"enqueued" => $detail['enqueued'],
				"cancel" => $detail['cancel'],
				"onModer" => $detail['onModer'],
            ];
			if (isset($result['sms'])) $return["sms"] = $result['sms'];
		}
		return $return;
	}
	
	//отправка смс
	//params = array (text => , source =>, datetime => , action =>, onlydelivery =>, smsid =>)
	function send($params = [], $phones = [])
    {
		$phones = (array)$phones;
		if (!isset($params["action"])) $params["action"] = "send";
		$someXML = "";
		if (isset($params["text"])) $params["text"] = htmlspecialchars($params["text"]);
		foreach ($phones as $phone){
			if (is_array($phone)){
				if (isset($phone["number"])){
					$someXML .= "<to number='".$phone['number']."'>";
					if (isset($phone["text"])){
						$someXML .= htmlspecialchars($phone["text"]);
					}
					$someXML .= "</to>";
				}
			}
			else{
				$someXML .= "<to number='$phone'></to>";
			}
		}
		$result = $this->request("send", $params, $someXML);
		if ($this->get($result, "code") != 1){
			$return =  ["code" => $this->get($result, "code"), "descr" => $this->get($result, "descr")];
		}
		else{
			$return = [
				"code" => 1,
				"descr" => $this->get($result, "descr"),
				"datetime" => $this->get($result, "datetime"),
				"action" => $this->get($result, "action"),
				"allRecivers" => $this->get($result, "allRecivers"),
				"colSendAbonent" => $this->get($result, "colSendAbonent"),
				"colNonSendAbonent" => $this->get($result, "colNonSendAbonent"),
				"priceOfSending" => $this->get($result, "priceOfSending"),
				"colsmsOfSending" => $this->get($result, "colsmsOfSending"),
				"price" => $this->get($result,"price"),
				"smsid" => $this->get($result,"smsid"),
            ];
		}
		return $return;
		
	}
	
	function get($responce, $key)
    {
		if (isset($responce[$key])) return $responce[$key];
		return false;
	}
	
	function parseXML($xml)
    {
		$array = SMSTransport_XML2Array::createArray($xml);
		return $array["data"];
	}
	
	function request($action, $params = [], $someXML = "")
    {
		$xml = $this->makeXML($params,$someXML);
		if (self::$HTTPS_METHOD == "curl"){
			return $this->parseXML( $this->request_curl($action,$xml) );
		}
		$this->error(t("В настройках указан неизвестный метод запроса - '").self::$HTTPS_METHOD."'");
	}
	
	function request_curl($action,$xml)
    {
        if (self::$USE_HTTPS == 1)
            $address = self::$HTTPS_ADDRESS."API/XML/".$action.".php";
        else
            $address = self::$HTTP_ADDRESS."API/XML/".$action.".php";
		$ch = curl_init($address);
		curl_setopt($ch, CURLOPT_URL, $address);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	function makeXML($params,$someXML = "")
    {
		$xml = "<?xml version='1.0' encoding='UTF-8'?>
		<data>
			<login>".self::$HTTPS_LOGIN."</login>
			<password>".self::$HTTPS_PASSWORD."</password>
			";
		foreach ($params as $key => $value){
			$xml .= "<$key>$value</$key>";
		}
		$xml .= "$someXML
		</data>";
		$xml = $this->getConvertedString($xml);
		return $xml;
	}
	
	function detectCharset($string, $pattern_size = 50)
    {
		$first2 = substr($string, 0, 2);
	    $first3 = substr($string, 0, 3);
	    $first4 = substr($string, 0, 3);
	    
	    $UTF32_BIG_ENDIAN_BOM = chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF);
		$UTF32_LITTLE_ENDIAN_BOM = chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00);
		$UTF16_BIG_ENDIAN_BOM = chr(0xFE) . chr(0xFF);
		$UTF16_LITTLE_ENDIAN_BOM = chr(0xFF) . chr(0xFE);
		$UTF8_BOM = chr(0xEF) . chr(0xBB) . chr(0xBF);
	    
	    if ($first3 == $UTF8_BOM) return 'UTF-8';
	    elseif ($first4 == $UTF32_BIG_ENDIAN_BOM) return 'UTF-32';
	    elseif ($first4 == $UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32';
	    elseif ($first2 == $UTF16_BIG_ENDIAN_BOM) return 'UTF-16';
	    elseif ($first2 == $UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16';
	    
	    $list = ['CP1251', 'UTF-8', 'ASCII', '855', 'KOI8R', 'ISO-IR-111', 'CP866', 'KOI8U'];
	    $c = strlen($string);
	    if ($c > $pattern_size)
	    {
	        $string = substr($string, floor(($c - $pattern_size) /2), $pattern_size);
	        $c = $pattern_size;
	    }
	
	    $reg1 = '/(\xE0|\xE5|\xE8|\xEE|\xF3|\xFB|\xFD|\xFE|\xFF)/i';
	    $reg2 = '/(\xE1|\xE2|\xE3|\xE4|\xE6|\xE7|\xE9|\xEA|\xEB|\xEC|\xED|\xEF|\xF0|\xF1|\xF2|\xF4|\xF5|\xF6|\xF7|\xF8|\xF9|\xFA|\xFC)/i';
	
	    $mk = 10000;
	    $enc = 'UTF-8';
	    foreach ($list as $item)
	    {
	        $sample1 = @iconv($item, 'cp1251', $string);
	        $gl = @preg_match_all($reg1, $sample1, $arr);
	        $sl = @preg_match_all($reg2, $sample1, $arr);
	        if (!$gl || !$sl) continue;
	        $k = abs(3 - ($sl / $gl));
	        $k += $c - $gl - $sl;
	        if ($k < $mk)
	        {
	            $enc = $item;
	            $mk = $k;
	        }
	    }
	    return $enc;
    }
	
	function getConvertedString($value, $from = false)
    {
		if (self::$HTTPS_CHARSET_AUTO_DETECT){
			if (!$this->charset){
				$this->charset = $this->detectCharset($value);
			}	
		}
		else{
			$this->charset = self::$HTTPS_CHARSET;
		}
		
		if (strtolower($this->charset) != "utf-8") {
			if (function_exists("iconv")){
				if (!$from)
					return iconv($this->charset,"utf-8",$value);
				else 
					return iconv("utf-8",$this->charset,$value);
			}
			else
				$this->error(t("Не удается перекодировать переданные параметры в кодировку utf-8 - отсутствует функция iconv"));
		}
		return $value;
	}
	
	function error($text)
    {
		die($text);
	}	
		
	function __construct()
    {
		//require_once(dirname(__FILE__)."/config.php");
		//if (!defined("HTTPS_LOGIN")) $this->error(t("Не удалось подключить конфигурационный файл. Проверьте путь до файла config.php и права на него"));
	}
}

class SMSTransport_XML2Array {

    private static $xml = null;
	private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new \DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
		self::$encoding = $encoding;
    }

    /**
     * Convert an XML to Array
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMDocument
     */
    public static function &createArray($input_xml) 
    {
        $xml = self::getXMLRoot();
        if ($input_xml === false) {
            throw new \Exception('[XML2Array] Error connection.');
        } else if(is_string($input_xml)) {
			$parsed = $xml->loadXML($input_xml);
			if(!$parsed) {
				throw new \Exception('[XML2Array] Error parsing the XML string.');
			}
		} else {
			if(get_class($input_xml) != 'DOMDocument') {
				throw new \Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
			}
			$xml = self::$xml = $input_xml;
		}
		$array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $array;
    }

    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node) 
    {
		$output = [];

		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE:
				$output['@cdata'] = trim($node->textContent);
				break;

			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;

			case XML_ELEMENT_NODE:

				// for each child node, call the covert function recursively
				for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
					$child = $node->childNodes->item($i);
					$v = self::convert($child);
					if(isset($child->tagName)) {
						$t = $child->tagName;

						// assume more nodes of same kind are coming
						if(!isset($output[$t])) {
							$output[$t] = [];
						}
						$output[$t][] = $v;
					} else {
						//check if it is not an empty text node
						if($v !== '') {
							$output = $v;
						}
					}
				}

				if(is_array($output)) {
					// if only one node of its kind, assign it directly instead if array($value);
					foreach ($output as $t => $v) {
						if(is_array($v) && count($v)==1) {
							$output[$t] = $v[0];
						}
					}
					if(empty($output)) {
						//for empty nodes
						$output = '';
					}
				}

				// loop through the attributes and collect them
				if($node->attributes->length) {
					$a = [];
					foreach($node->attributes as $attrName => $attrNode) {
						$a[$attrName] = (string) $attrNode->value;
					}
					// if its an leaf node, store the value in @value instead of directly storing it.
					if(!is_array($output)) {
						$output = ['@value' => $output];
					}
					$output['@attributes'] = $a;
				}
				break;
		}
		return $output;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot()
    {
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }
}