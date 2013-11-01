<?php 

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


header("content-type:text/plain");

require_once dirname(__FILE__) . "/../config.php";
require_once dirname(__FILE__) . "/../jsonwrapper.php";
require_once dirname(__FILE__) . "/../classes/Ftp.class.php";
require_once dirname(__FILE__) . "/../classes/Connection.class.php";
require_once dirname(__FILE__) . "/../classes/Cache.class.php";
require_once dirname(__FILE__) . "/../classes/Query.class.php";
require_once dirname(__FILE__) . "/../classes/Site.class.php";
require_once dirname(__FILE__) . "/../classes/Util.class.php";

// identity function, returns its argument unmodified.
function o($o) { return $o; }

// manages json output using api layout
function output($result, $error) {
	$return = array();
	if (isset($result)) $return['result'] = $result;
	if (isset($error)) $return['error'] = $error;
	echo json_encode($return);
}


try {
	

	$connSlave = new Connection(DB_SLAVE_HOST, DB_SLAVE_USER, DB_SLAVE_PASS, DB_SLAVE_TABLE);
	$urldata = Connection::cleanData($_REQUEST);

	if (!array_key_exists('pk', $urldata)) throw new Exception("Public key is required");

	$site = new Site($urldata['pk']);
	$arrSiteData = $site->get();
	$site->verify();

	
	
	$arrSiteSettings = o(new Query)->select("SELECT * FROM sitessettings WHERE nSitesID = " . $arrSiteData['id']);

	
	$ftp = new FTP(FTP_HOST, FTP_USER, FTP_PASS);

	if (empty($arrSiteSettings['themeFile'])) {

		
		
		$ftp->changeDirectory(DIRECTORY_CSS_OUTPUT);
		
		// do not allow an existing file to be overridden
		do {
			$filename = Util::genRandomString(9, "N") . ".css";
		} while ($ftp->fileExists($filename));
		
		$ftp->fileWrite($filename);
		
		Query::update("sitessettings", array('themeFile' => $filename), array('id' => $arrSiteSettings['id']));
		
	} else {
		
		$filename = $arrSiteSettings['themeFile'];
		
	}


	//if (!file_exists(DOCUMENT_ROOT . DIRECTORY_CSS_OUTPUT . "/" . $filename)) throw new Exception("File was not found");
	//if (!Util::remoteFileExists("http://flairstatic.emmaactive.com/frontend-themes/custom/" . $filename)) throw new Exception("File was not found");
	if (!$ftp->fileExists(DIRECTORY_CSS_OUTPUT . "/" . $filename)) throw new Exception("File was not found");

	
	output(array(
		'filename' => $filename
	));
	
	

} catch (CacheException $e) {
	output(null, $e->getMessage());
} catch (ConnectionException $e) {
	output(null, $e->getMessage());
} catch (FtpException $e) {
	output(null, $e->getMessage());
} catch (QueryException $e) {
	output(null, $e->getMessage());
} catch (SiteException $e) {
	output(null, $e->getMessage());
} catch (UtilException $e) {
	output(null, $e->getMessage());
} catch (Exception $e) {
	output(null, $e->getMessage());
}



