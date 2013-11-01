<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


header("content-type:text/plain");

require_once dirname(__FILE__) . "/../config.php";
require_once dirname(__FILE__) . "/../jsonwrapper.php";
require_once dirname(__FILE__) . "/../classes/CssParser.class.php";
require_once dirname(__FILE__) . "/../classes/Util.class.php";

// manages json output using api layout
function output($result, $error) {
	$return = array();
	if (isset($result)) $return['result'] = $result;
	if (isset($error)) $return['error'] = $error;
	echo json_encode($return);
}

foreach ($_REQUEST as $k => $v) ${$k} = $v;


// note: some css colors are duplicated for multiple rules, we just grab one rule and assume the rest are the same value
$requiredCss = array(
	'frontendResourceFile' => array('selector' => '#EMMA_active .EMMA_logo, #EMMA_active .EMMA_btnBotL, #EMMA_active .EMMA_tagDot, #EMMA_active .EMMA_tagLinkLike, #EMMA_active .EMMA_formBtnSubmit span, #EMMA_active .EMMA_windowBtnClose, #EMMA_active .EMMA_windowLogout, #EMMA_active .EMMA_btnCancel, #EMMA_active .EMMA_btnSettings, #EMMA_active .EMMA_iconLabel, #EMMA_active .EMMA_shareButton, #EMMA_active .EMMA_lightboxNavIcon', 'property' => 'background'),
	'windowHeaderColor' => array('selector' => '#EMMA_active .EMMA_window .EMMA_header .EMMA_title', 'property' => 'color'),
	'tooltipLinkColor' => array('selector' => '#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagBubbleContent .EMMA_tagLinkText', 'property' => 'color'),
	'tooltipDescriptionColor' => array('selector' => '#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagBubbleContent .EMMA_tagLinkURL', 'property' => 'color'),
	'shareHeaderColor' => array('selector' => '#EMMA_active .EMMA_node .EMMA_shareHeader', 'property' => 'color'),
	'footerLinkColor' => array('selector' => '#EMMA_active .EMMA_node .EMMA_linkHolder .EMMA_link', 'property' => 'color'),
	'footerBackgroundColor' => array('selector' => '#EMMA_active .EMMA_node .EMMA_linkHolderBackground', 'property' => 'background'),
	'footerBackgroundOpacity' => array('selector' => '#EMMA_active .EMMA_node .EMMA_linkHolderBackground', 'property' => 'opacity'),
	'dotOutlineColor' => array('selector' => '#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagOutline', 'property' => 'background'),
	'dotOutlineOpacity' => array('selector' => '#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagOutline', 'property' => 'opacity'),
	'inputTextColor' => array('selector' => '#EMMA_active .EMMA_formStyle .EMMA_textField input, #EMMA_active .EMMA_formStyle .EMMA_textArea textarea, #EMMA_active .EMMA_formStyle .EMMA_dropDown select', 'property' => 'color'),
	'formOptionColor' => array('selector' => '#EMMA_active .EMMA_formStyle .EMMA_options .EMMA_optForgotPass, #EMMA_active .EMMA_formStyle .EMMA_options .EMMA_optBackToLogin', 'property' => 'color'),
	'dropdownBackgroundColor' => array('selector' => '#EMMA_active .EMMA_formStyle .EMMA_dropDownDiv .EMMA_button:hover', 'property' => 'background'),
	'dropdownTextColor' => array('selector' => '#EMMA_active .EMMA_formStyle .EMMA_dropDownDiv .EMMA_button:hover', 'property' => 'color'),
	'formLabelColor' => array('selector' => '#EMMA_active .EMMA_formStyle .EMMA_formLabel, #EMMA_active .EMMA_formStyle .EMMA_formLabel-sm', 'property' => 'color'),
	'formErrorColor' => array('selector' => '#EMMA_active .EMMA_formStyle .EMMA_formError', 'property' => 'color'),
	'formButtonColor' => array('selector' => '#EMMA_active .EMMA_formBtnSubmit *', 'property' => 'color'),
	'formButtonDisabledColor' => array('selector' => '#EMMA_active .EMMA_formBtnSubmit[disabled] *', 'property' => 'color')
);





try {


	if (!isset($themeFile)) throw new Exception("Could not grab theme styles because the file was not defined");


	
	$themeid = rtrim($themeFile, ".css");
	$file = WEB_CSS_THEME_DIRECTORY . $themeFile;
	$arrCssResults = array();
	$cssPrefix = "#EMMA_active";


	$css = Util::fileGetContents($file);
	
	if (!empty($css)) {

		$parser = new CssParser;
		$parser->parseStr($css);
		$arrParsedCss = $parser->css;
		
		foreach ($requiredCss as $varname => $declaration) {
			$key = str_replace($cssPrefix, "{$cssPrefix}.EMMA_{$themeid}", $declaration['selector']);
			$property = $declaration['property'];
			$result = $arrParsedCss[$key][$property][0];
			// extract url from declaration if needed
			if ($property === "background" && strstr($result, "url")) {
				$pieces = explode('"', $result);
				$result = $pieces[1];
			}
			if ($property === "opacity") $result *= 100; // assume we don't want a decimal value
			// filter out important 
			$result = rtrim($result, "!important");
			$arrCssResults[$varname] = trim($result);
		}

	}
	
	
	
	
	output(array(
		'cssObject' => $arrCssResults
	));


} catch (UtilException $e) {
	output(null, $e->getMessage());
} catch (Exception $e) {
	output(null, $e->getMessage());
}




