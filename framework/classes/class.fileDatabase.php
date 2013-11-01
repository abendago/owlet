<?

/* File upload class */

# filename = path to the file
# tableSaveTo = name of the table we are saving to
# fieldName =  name of the table field we are saving to
# randomID = this needs to be a unique random 30 character string

# this class will return the filename of the uploaded image on success


class fileDatabase
{

	function fileDatabase()
	{

	}

	function saveFile($fileName, $filenameWPath, $tableSaveTo, $randomID)
	{
		$fp      = fopen($filenameWPath, 'r');
		$fileSize = filesize($filenameWPath);
		$fileType = filetype($filenameWPath);
		$content = fread($fp, filesize($filenameWPath));
		$content = addslashes($content);
		fclose($fp);

		if(!get_magic_quotes_gpc())
		{
			$fileName = addslashes($fileName);
		}

		$query = "INSERT INTO $tableSaveTo (ranval, name, size, type, content ) ".
		"VALUES ('$randomID', '$fileName', '$fileSize', '$fileType', '$content')";

		mirage_dbdo($query,"INSERT");
	}

	function getFile($randomID, $tableGetFrom)
	{
		$query = "SELECT name, type, size, content " .
				 "FROM $tableGetFrom WHERE ranval = '$randomID'";

		$result = mirage_dbdo($query,"SELECT");
		$name = $result[name];
		$type = $result[type];
		$size = $result[size];
		$content = $result[content];


		header("Content-length: $size");
		header("Content-type: $type");
		header("Content-Disposition: attachment; filename=$name");
		echo $content;
	}



}