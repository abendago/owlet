<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE circuit>
<!--
	Example circuit.xml file for the controller portion of an application.
	Only the controller circuit has public access - the controller circuit
	contains all of the fuseactions that are used in links and form posts
	within your application.
-->
<circuit access="public" xmlns:php="php/">
	
	<!--
		Default fuseaction for application, uses model and view circuits
		to do all of its work:
	-->
	<fuseaction name="welcome">
		<do action="m.getTime" />
		<do action="v.sayHello" />
	</fuseaction>
	
	<!--
		Contrived example fuseaction that uses the various verbs in the
		"cf" custom lexicon - it should output the Application Data.
		This is provided purely for debugging to show the custom lexicon
		is working (and as an illustration of using the lexicon's verbs).
	-->
	<fuseaction name="debug">
		<php:switch expression="$_fba-&gt;fuseactionVariable">
			<php:case value="fuseaction|do" delimiters="|">
				<php:dump label="Application Data" var="$_fba-&gt;getApplicationData()" />
			</php:case>
			<php:defaultcase>
				<php:dump label="Attributes Scope" var="$attributes" />
			</php:defaultcase>
		</php:switch>
	</fuseaction>
	
</circuit>
