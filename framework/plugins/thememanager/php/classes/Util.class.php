<?php

/***

@Date: 11/23/2011
@Author: Justin Bull
@Description: This class manages all methods related to user data
@Dependencies:
	- curl extension [http://php.net/manual/en/book.curl.php]
	- /php/config.php

***/

class Util
{
	
	private static $CURL_OPTS = array(
		//CURLOPT_HTTPHEADER => array('Expect:'), // disable the 'Expect: 100-continue' behaviour.
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true
	);
	
	/*
	public static function getCurrentURI()
	{
		$protocol = (strstr($_SERVER["SERVER_PROTOCOL"], "https")) ? "https" : "http";
		$port = ($_SERVER['SERVER_PORT'] !== "80") ? ":{$_SERVER['SERVER_PORT']}" : "";
		return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
	}
	*/

	public static function strTimeDuration($strtime)
	{
		if (is_string($strtime)) return strtotime($strtime) - strtotime('NOW');
		return 0;
	}

	public static function quit($data)
	{
		if (DEVMODE) {
			if (is_array($data)) print_r($data);
			else var_dump($data);
			exit;
		} elseif (HUB_RETURN_METHOD === 'jsonp' && defined('HUB_JSONP_ID')) {
			if (is_string($data)) $data = "'".$data."'";
			elseif (is_array($data)) $data = json_encode($data);
			exit(sprintf("EmmaActive.jsonpHandler['%s'](%s);", HUB_JSONP_ID, $data));
		} elseif (HUB_RETURN_METHOD === 'json') {
			if (is_array($data) && !empty($data)) echo json_encode($data);
			exit;
		} else throw new UtilException('Data return failed because some required parameters were undefined or contained unexpected values');
	}

	public static function println($msg)
	{
		echo $msg . PHP_EOL . "--------------------------------------------------" . PHP_EOL;
	}

	public static function genRandomString($length, $method = "NLUS")
	{
		$a = array(
			'N' => "0123456789",
			'L' => "abcdefghijklmnopqrstuvwxyz",
			'U' => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
			'S' => "!@#$%^&*"
		);
		$string = "";
		$chars = "";
		foreach (str_split($method) as $m) $chars .= $a[$m];
		$max = strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) $string .= substr($chars, mt_rand(0, $max), 1);
		return $string;
	}

	public static function plainMail($to, $subject, $message)
	{
		$postmasteremail = "support@emmaactive.com";
		$headers = 'From: "EmmaActive" <'.$postmasteremail.'>' . PHP_EOL;
		$headers .= 'Reply-To: ' . $postmasteremail . PHP_EOL;
		$headers .= 'Return-Path: ' . $postmasteremail . PHP_EOL;
		if ($to && $subject && $message) mail($to, $subject, $message, $headers);
	}

	public static function fileGetContents($url, $parameters = array())
	{
		if (!extension_loaded('curl')) throw new Exception("PHP extension 'curl' is not loaded");
		// prevent infinite request loops
		// use both methods just to be sure?
		if (HUB_USER_AGENT === SYS_AGENT) throw new UtilException('Infinite request loop denied');
		if (array_key_exists('userAgent', $_REQUEST) && $_REQUEST['userAgent'] === SYS_AGENT) throw new UtilException('Infinite request loop denied');
		$parameters['userAgent'] = SYS_AGENT;
		$opts = self::$CURL_OPTS;
		$opts[CURLOPT_REFERER] = HUB_REFERER;
		$opts[CURLOPT_USERAGENT] = SYS_AGENT;
		$opts[CURLOPT_POSTFIELDS] = http_build_query($parameters, null, '&');
		// setup our curl handle
		$ch = @curl_init($url);
		@curl_setopt_array($ch, $opts);
		$result = @curl_exec($ch);
		// did any errors occur?
		$errorNumber = (int) @curl_errno($ch);
		$errorString = @curl_error($ch);
		@curl_close($ch);
		// oops! request failed
		if ($errorNumber !== 0) throw new UtilException("Request error: {$errorString}");
		return $result;
	}

	public static function parseUrl($url) 
	{
		$r  = "^(?:(?P<scheme>\w+)://)?";
		$r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
		$ip = "(?:[0-9]{1,3}+\.){3}+[0-9]{1,3}"; //ip check
		$s  = "(?P<subdomain>[-\w\.]+)\.)?"; //subdomain
		$d  = "(?P<domain>[-\w]+\.)"; //domain
		$e  = "(?P<extension>\w+)"; //extension
		$r .= "(?P<host>(?(?=".$ip.")(?P<ip>".$ip.")|(?:".$s.$d.$e."))";
		$r .= "(?::(?P<port>\d+))?";
		$r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
		$r .= "(?:\?(?P<arg>[\w=&]+))?";
		$r .= "(?:#(?P<anchor>\w+))?";
		$r  = "!$r!"; // Delimiters
		preg_match($r, $url, $out);
		return $out;
	}

	public static function remoteFileExists($path)
	{
		return (@fopen($path,"r")==true);
	}
	
	/*
	public static function validEmail($email)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) $isValid = false;
		else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) $isValid = false;
			else if ($domainLen < 1 || $domainLen > 255) $isValid = false;
			else if ($local[0] == '.' || $local[$localLen-1] == '.') $isValid = false;
			else if (preg_match('/\\.\\./', $local)) $isValid = false;
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) $isValid = false;
			else if (preg_match('/\\.\\./', $domain)) $isValid = false;
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) 
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) $isValid = false;
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) $isValid = false;
		}
		return $isValid;
	}
	*/



}

class UtilException extends Exception {}