<?php
	// example custom verb that compiles to a <cfdefaultcase> tag
	// usage:
	// 1. add the lexicon declaration to your circuit file:
	//    <circuit xmlns:php="php/">
	// 2. use the verb in a fuseaction:
	//    <cf:switch expression="$someExpr">
	//    <cf:case value="someValue">
	//        ... some code ...
	//    </cf:catch>
	//    <cf:case value="anotherValue|andAnother" delimiters="|">
	//        ... more code ...
	//    </cf:case>
	//    <cf:defaultcase>
	//        ... default code ...
	//    </cf:defaultcase>
	//    </cf:switch>
	//
	// how this works:
	// a. validate the attributes passed in ($fb_['verbInfo']['attributes'])
	// b. in start mode, generate the required PHP
	// c. in end mode, generate the required PHP
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
						"The verb 'defaultcase' does not appear directly nested within a 'switch' verb in fuseaction {$fb_['verbInfo']['circuit']}.{$fb_['verbInfo']['fuseaction']}.");
		}
		//
		// start mode:
		$this->fb_appendLine('default:');
	} else {
		//
		// end mode:
		$this->fb_appendLine('break;');
	}
?>