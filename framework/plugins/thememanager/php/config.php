<?php 

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

set_time_limit(10); #limits the maximum execution time in seconds

session_name("EmmaActiveUserSession"); #required for cross-subdomain sessions
session_set_cookie_params(0, '/', '.emmaactive.com');
session_start();

define("SYS_AGENT", "EmmaActive/Accounts/Themer/" . session_id());
define("DEVMODE", false); #warning: if true, frontend will stop working
define("ALLOW_MEMCACHE", true);

//define("DOCUMENT_ROOT", "/var/www/vhosts/emmaactive.com");
define("DIRECTORY_CSS_OUTPUT", "/httpdocs/frontend-themes/css");
define("WEB_CSS_THEME_DIRECTORY", "http://cdn.emmaactive.com/frontend-themes/css/");
define("WEB_CSS_BASE_FILE", "flairdev.emmaactive.com/css/emma.base.css");

define('DB_SLAVE_HOST', "64.64.26.135");
define('DB_SLAVE_USER', "flair");
define('DB_SLAVE_PASS', "cs12271");
define('DB_SLAVE_TABLE', "flair");

// cdn.emmaactive.com
define('FTP_HOST', 'emmaactive.com');
define('FTP_USER', 'flaircdn');
define('FTP_PASS', 'cs12271');

define("HUB_USER_AGENT", $_SERVER['HTTP_USER_AGENT']);
define("HUB_REFERER", $_SERVER['HTTP_REFERER']);



