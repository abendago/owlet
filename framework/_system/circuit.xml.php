<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE circuit>
<!--
	Example circuit.xml file for the controller portion of an application.
	Only the controller circuit has public access - the controller circuit
	contains all of the fuseactions that are used in links and form posts
	within your application.
-->

<circuit access="public" xmlns:php="php/">

	<fuseaction name="shortenURL" access="public">
		<include template="views/dsp_encodeform" contentvariable="form"/>
		<include template="views/dsp_nav" contentvariable="navigation"/>
		<include template="views/lay_frame" />
	</fuseaction>

	<fuseaction name="decodeHash" access="public">
		<include template="views/dsp_decodeform" contentvariable="form"/>
		<include template="views/dsp_nav" contentvariable="navigation"/>
		<include template="views/lay_frame" />
	</fuseaction>


	<postfuseaction>
	</postfuseaction>
	
	
</circuit>
