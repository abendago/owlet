<?php
	// example custom verb that compiles to a <cfcase> tag
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
	// b. in start mode, generate the required php
	// c. in end mode, generate the required php
	//
	if ( $fb_['verbInfo']['executionMode'] == "start" ) {
		//
		// validate attributes:
		// parent tag must be a switch verb in the same lexicon:
		if ( !array_key_exists("parent",$fb_['verbInfo']) ||
				$fb_['verbInfo']['parent']['lexicon'] != $fb_['verbInfo']['lexicon'] ||
				$fb_['verbInfo']['parent']['lexiconVerb'] != "switch" ) {
			$this->fb_throw("fusebox.badGrammar.invalidNesting",
						"Verb is invalid in this context",
						"The verb 'case' does not appear directly nested within a 'switch' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
		}
		//
		// value is required:
		if ( !array_key_exists("value",$fb_['verbInfo']['attributes']) ) {
			$this->fb_throw("fusebox.badGrammar.requiredAttributeMissing",
						"Required attribute is missing",
						"The attribute 'value' is required, for a 'case' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
		}
		//
		// delimiters is optional:
		if ( !array_key_exists("delimiters",$fb_['verbInfo']['attributes']) ) {
			$fb_['verbInfo']['attributes']['delimiters'] = ',';
		}
		//
		// start mode:
		$arCase = explode($fb_['verbInfo']['attributes']['delimiters'],$fb_['verbInfo']['attributes']['value']);
		if ( strlen($arCase[count($arCase)-1]) ) $deleted = array_pop($arCase);
		for ( $c = 0 ; $c < count($arCase) ; $c++ ) {
			$this->fb_appendLine('case "'.$arCase[$c].'" :');
		}
	} else {
		//
		// end mode:
		$this->fb_appendLine('break;');
	}
?>
