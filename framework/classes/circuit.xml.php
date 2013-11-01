<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE circuit>
<!--
	Example circuit.xml file for the controller portion of an application.
	Only the controller circuit has public access - the controller circuit
	contains all of the fuseactions that are used in links and form posts
	within your application.
-->
<circuit access="public" xmlns:php="php/">
	
	<fuseaction name="main">
		<include template="initialize" />
	</fuseaction>


	<postfuseaction>
	</postfuseaction>
	
	
</circuit>
