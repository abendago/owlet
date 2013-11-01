# README FOR OWLETS
#
# INSTALLATION
###########################

#FILES

Download all source files. 
You should have two primary folders:

 1. Spool 
 2. Framework
 3. database.sql

Spool
-----
Spool contains the files required to startup the ZMQ Workers and Router. Upload them somewhere on your server where you can start spool/spool.php (php spool.php)
If started successfully you will see some output announcing the startup of the 3 workers. 

Framework
---------
I've used the PHP FuseBox Framework. It is a simple MVC XML based framework. Upload these files to the root of your web accessable domain. 
CHMOD the 'parsed' directory to 777 or server writeable.
Important: make sure you have the .htaccess file on the root of your install. 

Database
--------
Setup a mysql database and take note of the database name, username and password
Install the sql table you downloaded in the git repo
Open the file spool/config.php and update the login credentials according to your specification above

RUN
Load the index.php file in your webrowser (on my server it would be http://short.abendago.com/)

#API
The API is very simple to use. There are currently 2 endpoints but more can be added very very easily (I'll cover that shortly)

End Point #1: /api/encode/json
End Point #2: /api/decode/json

By posting a variable of strURL at the API it will process according to the end point instruction (encode or decode). I'm not leveraging it yet, but prepared for various output types. You can see this as the final url element "json" on the 2 end points above. In the future we might want to include other response types aside from JSON. 

For the sake of this challenge responses are always inside a JSON array. 

Response: The API will always return an nStatus of 1|0 (1=success and 0 false)

#ADDING END POINTS
Open the file /spool/class.endpoints.php and find the following code

/* function yourOwnEndPoint()
{
	return $arrResults;
}*/

To add another end point just create a new function to handle the end point and return at least $arrResults[nStatus] = 1, or 0

IE: 

function reverseurl()
{
	$arrResults[nStatus]=1;
	$arrResults[strResonseString]=strrev($this->url);
	return $arrResults;
}

Restart the spool so that the listeners re-initialize.

Now you can call a new end point API as follows:

/api/reverseurl/json

And post a url in the url posting value of strURL

