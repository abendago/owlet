<?php
	// example custom verb that compiles to a switch call
	// usage:
	// 1. add the lexicon declaration to your circuit file:
	//    <circuit xmlns:php="php/">
	// 2. use the verb in a fuseaction:
	//    <php:switch expression="$someExpr">
	//    <php:case value="someValue">
	//        ... some code ...
	//    </php:catch>
	//    <php:case value="anotherValue|andAnother" delimiters="|">
	//        ... more code ...
	//    </php:case>
	//    <php:defaultcase>
	//        ... default code ...
	//    </php:defaultcase>
	//    </php:switch>
	//
	// how this works:
	// a. validate the attributes passed in ($fb_['verbInfo']['attributes'])
	// b. in start mode, generate the required PHP
	// c. in end mode, generate the required PHP
	//
	if ( $fb_['verbInfo']['executionMode'] == "start" ) {
		//
		// validate attributes:
		// expression is required:
		if ( !array_key_exists("expression",$fb_['verbInfo']['attributes']) ) {
			$this->fb_throw("fusebox.badGrammar.requiredAttributeMissing",
						"Required attribute is missing",
						"The attribute 'expression' is required, for a 'switch' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
		}
		//
		// start mode:
		$this->fb_appendLine('switch ( '.$fb_['verbInfo']['attributes']['expression'].' ) {');
	} else {
		//
		// end mode:
		$this->fb_appendLine('}');
	}
?>
