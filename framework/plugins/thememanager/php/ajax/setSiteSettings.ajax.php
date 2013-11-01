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

function checkTypeCast($array) {
	$s = $array;
	foreach ($array as $k => $v) {
		if (is_array($v)) $s[$k] = json_encode($v);
		// conditionals are stored in numerical form
		elseif ($v === "false") $s[$k] = "0";
		elseif ($v === "true") $s[$k] = "1";
	}
	return $s;
}

try {
	

	$connSlave = new Connection(DB_SLAVE_HOST, DB_SLAVE_USER, DB_SLAVE_PASS, DB_SLAVE_TABLE);
	$urldata = Connection::cleanData($_REQUEST);
	
	foreach ($urldata as $k => $v) ${$k} = $v;

	if (!isset($pk)) throw new Exception("Public key is required");
	if (!isset($settings)) throw new Exception("Settings are required");

	$site = new Site($pk);
	$arrSiteData = $site->get();
	$site->verify();


	if (isset($themeTracker)) Query::update("sites", array('themeTracker' => $themeTracker), array('id' => $arrSiteData['id']));

	
	
	
	// FORMATTING
	if (is_array($settings)) $settings = checkTypeCast($settings);
	else throw new Exception("Unexpected setting format");
	



	$arrSiteSettings = o(new Query)->select("SELECT * FROM sitessettings WHERE nSitesID = " . $arrSiteData['id']);
	Query::update("sitessettings", $settings, array('id' => $arrSiteSettings['id']));

	
	
	

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



