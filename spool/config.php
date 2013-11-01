<?
 $hostname = "";
 $username = "";
 $password = "";
 $dbname = "";
 $dbcon = mysql_connect($hostname, $username, $password);
 mysql_select_db($dbname) or die("Unknown database!");
?>