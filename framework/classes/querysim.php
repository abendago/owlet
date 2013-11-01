<?
class querysim {

	function querysim()
	{
		
	}

	function qsim($strData)
	{
		/* arrData should look like
		\n
		fieldName1, fieldName2, fieldName3
		==================================
		nathan, 1, abendago
		rob, 2, bizmark
		steve, 4, blastradius
		*/

		# first split all our rows into an array
		
		  $nl = "\n";

		$arrRows = split("$nl", $strData);

		# then foreach row go through
		if ($arrRows)
		{
			$i=0;
	
			foreach($arrRows as $key=>$value)
			{
				if ($i==0)
				{
					#this means we are on our header columns
					$arrHeader = split(",", $value);
				} else if ($i==1) {
					#this means we are on our divider row - do nothing#
				} else
				{
					//print "lookg: $value<br>";
					list(
						$arrRes[$i][trim($arrHeader[0])],
						$arrRes[$i][trim($arrHeader[1])],
						$arrRes[$i][trim($arrHeader[2])],
						$arrRes[$i][trim($arrHeader[3])],
						$arrRes[$i][trim($arrHeader[4])],
						$arrRes[$i][trim($arrHeader[5])],
						$arrRes[$i][trim($arrHeader[6])],
						$arrRes[$i][trim($arrHeader[7])],
						$arrRes[$i][trim($arrHeader[8])],
						$arrRes[$i][trim($arrHeader[9])],
						$arrRes[$i][trim($arrHeader[10])],
						$arrRes[$i][trim($arrHeader[11])],
						$arrRes[$i][trim($arrHeader[12])]
						
					) = split(",", $value);
				}
				$i++;
			}
		}

		/*foreach($arrRes as $rkey=>$rvalue)
		{
			print "$rvalue[strName], $rvalue[strAge]<br>";
		}
		*/

		return $arrRes;
	}

}
?>