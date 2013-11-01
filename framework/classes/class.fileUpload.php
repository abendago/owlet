<?

/* File upload class */

# destinationDir = the directory you want to upload into, this directory must be 777 to allow uploads.
# ufile = the file being uploaded (should be whatever is being passed from the form)
# ufilename =  the name you want to save this file as

# this class will return the filename of the uploaded image on success


class file_upload
{
	var $ufile;
	var $destinationDir;
	var $originalFileName;
	var $fileExtension;
	var $fileSize;
	var $width;
	var $height;
	var $fileTypesAllowed=array("jpg", "jpeg", "gif", "bmp", "pdf", "mp3", "mpeg", "mpg", "doc", "xls","bmp","htaccess","tif","flv","png", "mov", "avi","mp3", "wav","wmv","ppt","pub","swf","fla","psd","txt","csv","text");

	function file_upload($destinationDir, $ufile, $ufileName)
	{
		$this->ufile = $ufile;
		$this->destinationDir = $destinationDir;
		$this->ufileName = $ufileName;
		$this->fileExtension= strtolower(substr($ufileName,strrpos($ufileName,".")+1));//plus 1 to start AFTER the period
	}

	function renameFile()
	{

	}

	function doUpload()
	{
		if(
			(($this->ufile != "none")||($this->ufile != ""))
			&&
			(in_array($this->fileExtension,$this->fileTypesAllowed))
		  ) 
		{
			$dir = $this->destinationDir;
		
			  if(!copy($this->ufile,"$dir/".$this->ufileName)) {
				print("Was unable to upload the file $imageswf");
				$imageswf_name = $old_imgswf;
			  }
		 
		} 
		else 
		{ 
			if ($removeimage){
				
				$this->ufileName ="";
			} else {
			
				$this->ufileName = $old_imgswf; 
			}
		}
		#DONE UPLOADING #
		return $this->ufileName;

	}

/* 
		$file = $this->ufile;  //Converts the array into a new string containing the path name on the server where your file is.
		$myFileName = $_POST['MyFile']; //Retrieve file path and file name    
		$myfile_replace = str_replace('\\', '/', $myFileName);    //convert path for use with unix
		$myfile = basename($myfile_replace);    //extract file name from path
		$destination_file = "/".$myfile;  //where you want to throw the file on the webserver (relative to your login dir)
		
		// connection settings
		$ftp_server = "127.0.0.1";  //address of ftp server (leave out ftp://)
		$ftp_user_name = ""; // Username
		$ftp_user_pass = "";   // Password
		$conn_id = ftp_connect($ftp_server);        // set up basic connection
		
		// login with username and password, or give invalid user message
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die("<h1>You do not have access to this ftp server!</h1>");
		$upload = ftp_put($conn_id, $destination_file, $file, FTP_BINARY);  // upload the file
		
		if (!$upload) // check upload status
		{  
			echo "<h2>FTP upload of $myFileName has failed!</h2> <br />";
		}

		ftp_close($conn_id); // close the FTP stream
*/

}