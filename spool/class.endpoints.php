<?

class endpoints
{
	var $strURL;

	function endpoints()
	{	}

	function go($strURL, $endPoint)
	{
		$this->url = urldecode($strURL);
		
		if (!$this->url || !(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->url)))
		{
			$arrResults[nStatus] = "0";
			$arrResults[strMessage] = "We received an Invalid URL... Maybe try again...";
		} else {
			// do we have an enpoint by this name, this makes it EASY to ADD ENDPOINTS :)
			if (!method_exists("endpoints", $endPoint))
			{
				$arrResults[nStatus] = "0";
				$arrResults[strMessage] = "No API End Point By That Name... Maybe try again...";
			} else {
				$arrResults = $this->{$endPoint}();
			}
		}

		return $arrResults;
	}

	function encode()
	{
		$strMinifiedURL = $this->generate_chars();

		//check the uniqueness of the chars
		global $dbcon;
		$sql = "SELECT * FROM urlrefs WHERE 'strTinyURL'='".mysql_real_escape_string($strMinifiedURL)."'";
		$res = mysql_query($sql, $dbcon);

		// loop until we found a unique one. 
		$nMaxTries = 100;
		$nTries = 0;
		while( mysql_num_rows($res)>0 && $nTries<=$nMaxTries )
		{
		  $strMinifiedURL = $this->generate_chars();
		  $sql = "SELECT id FROM urlrefs WHERE strTinyURL='http://short.abendago.com/short/".mysql_real_escape_string($strMinifiedURL)."'";
		  $res = mysql_query($sql, $dbcon);
		  $nTries++;
		}

		if ($nTries==$nMaxTries)
		{
			$arrResults[nStatus] = "0";
			$arrResults[strMessage] = "We Could Not Generate a Unique URL For: ".$this->url;
		} else {
			$sql = "INSERT INTO urlrefs (strTinyURL, strFullURL) VALUES ('http://short.abendago.com/short/".mysql_real_escape_string($strMinifiedURL)."', '".mysql_real_escape_string($this->url)."')";
			$res = mysql_query($sql, $dbcon); //insert into the database
			if(mysql_affected_rows()):
				//ok, inserted. now get the data
				$arrResults[nStatus] = "1";
				$arrResults[strMessage] = "Look What We Found! A Short URL ";
				$arrResults[strResultURL] = "http://short.abendago.com/short/$strMinifiedURL";
			else:
				//problem with the database
				$arrResults[nStatus] = "0";
				$arrResults[strMessage] = "Something Didn't Save to The DB Correctly For: ".$this->url;
			endif;
			
		}
		return $arrResults; 
	}

	function decode()
	{
		global $dbcon;
		$sql = "SELECT strFullURL FROM urlrefs WHERE strTinyURL='".mysql_real_escape_string($this->url)."'";
		echo $sql;
		$res = mysql_query($sql, $dbcon);
		$strFullURL =  mysql_result($res, 0);

		if ($strFullURL)
		{
			$arrResults[nStatus] = "1";
			$arrResults[strMessage] = "Look What We Found! A Long URL For You";
			$arrResults[strResultURL] = $strFullURL;
		} else {
			$arrResults[nStatus] = "0";
			$arrResults[strMessage] = "Uhoh! We Couldn't Find a URL That Matches...";
		}
		return $arrResults;
	}

	/* function yourOwnEndPoint()
	{
		return $arrResults;
	}*/

	function generate_chars()
	{
		 $num_chars = 6; //max length of random chars
		 $i = 0;
		 $my_keys = "123456789abcdefghijklmnopqrstuvwxyz"; //keys to be chosen from
		 $keys_length = strlen($my_keys);
		 $url  = "";
		 while($i<$num_chars)
		 {
			  $rand_num = mt_rand(1, $keys_length-1);
			  $url .= $my_keys[$rand_num];
			  $i++;
		 }
		 return $url;
	}

}