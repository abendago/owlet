<?

foreach(glob("templates/css/*css") as $filename) {
    $xml_file = file_get_contents($filename, FILE_TEXT);
    // and proceed with your code
	print "<h2>$filename</h2>";
	if (preg_match_all('/([#][a-zA-Z0-9_\-]{6})*/', $xml_file, $matches)) {
		#$matches = array_flip($matches);
		foreach($matches as $val)
		{
			foreach($val as $hex)
			{
				if (strlen($hex)>3)
				{
					$arHex[$hex]=true;
				}
			}
		}
	}  
 }
#$arHex = array_flip($arHex);
print_r($arHex);
foreach($arHex as $color=>$val)
{
	$row.="<tr><td>$color :</td><td style='width:120px; height:75px; background-color: $color'></td></tr>";
}


?>

<table>
<?=$row?>
</table>