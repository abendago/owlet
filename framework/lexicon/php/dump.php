<?php
	// example custom verb that compiles to a var_dump call
	// usage:
	// 1. add the lexicon declaration to your circuit file:
	//    <circuit xmlns:php="php/">
	// 2. use the verb in a fuseaction:
	//    <php:dump var="$somevariable" />
	//    <php:dump label="some label" var="$somevariable" />
	//
	// how this works:
	// a. validate the attributes passed in ($fb_['verbInfo']['attributes'])
	// b. in start mode, generate the required PHP
	// c. in end mode, do nothing (this tag does not nest)
	//
	if ( $fb_['verbInfo']['executionMode'] == "start" ) {
		//
		// validate attributes:
		// var is required:
		if ( !array_key_exists("var",$fb_['verbInfo']['attributes']) ) {
			$this->fb_throw("fusebox.badGrammar.requiredAttributeMissing",
						"Required attribute is missing",
						"The attribute 'var' is required, for a 'dump' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
		}
		// label is optional, create the text to generate - note the quoting technique:
		if ( !array_key_exists("label",$fb_['verbInfo']['attributes']) ) {
			$fb_['verbInfo']['attributes']['label'] = '';
		}
		//
		// start mode:
		$this->fb_appendLine('echo "<pre>\\n'.$fb_['verbInfo']['attributes']['label'].'\n";var_dump('.$fb_['verbInfo']['attributes']['var'].'); echo "</pre>";');
	} else {
		//
		// end mode - do nothing
	}
?>
