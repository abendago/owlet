<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


header("content-type:text/plain");

require_once dirname(__FILE__) . "/../config.php";
require_once dirname(__FILE__) . "/../jsonwrapper.php";
require_once dirname(__FILE__) . "/../classes/CssParser.class.php";
require_once dirname(__FILE__) . "/../classes/Ftp.class.php";
require_once dirname(__FILE__) . "/../classes/Util.class.php";


// manages json output using api layout
function output($result, $error) {
	$return = array();
	if (isset($result)) $return['result'] = $result;
	if (isset($error)) $return['error'] = $error;
	echo json_encode($return);
}


foreach ($_REQUEST as $k => $v) ${$k} = $v;


# hex value (#AARRGGBB) for the ms gradient filter
//$dotOutlineNoRgbaSupport = "#" . strtoupper(dechex(255 * ($dotOutlineOpacity / 100))) . ltrim($dotOutlineColor, "#");


ob_start();

?>
#EMMA_active .EMMA_logo,
#EMMA_active .EMMA_btnBotL,
#EMMA_active .EMMA_tagDot,
#EMMA_active .EMMA_tagLinkLike,
#EMMA_active .EMMA_formBtnSubmit span,
#EMMA_active .EMMA_windowBtnClose,
#EMMA_active .EMMA_windowLogout,
#EMMA_active .EMMA_btnCancel,
#EMMA_active .EMMA_btnSettings,
#EMMA_active .EMMA_iconLabel,
#EMMA_active .EMMA_shareButton,
#EMMA_active .EMMA_lightboxNavIcon
{
	background:url("<?=$frontendResourceFile?>") no-repeat;
}

#EMMA_active .EMMA_modalMsg h2 { color:<?=$windowHeaderColor?>; }
#EMMA_active .EMMA_window .EMMA_windowUserName span { color:<?=$windowHeaderColor?>; }
#EMMA_active .EMMA_window .EMMA_header .EMMA_title { color:<?=$windowHeaderColor?>; }

#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagBubbleContent .EMMA_tagLinkText { color:<?=$tooltipLinkColor?>; }
#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagBubbleContent .EMMA_tagLinkURL { color:<?=$tooltipDescriptionColor?>; }
#EMMA_active .EMMA_node .EMMA_shareHeader { color:<?=$shareHeaderColor?>; }

#EMMA_active .EMMA_node .EMMA_linkHolder .EMMA_link { color:<?=$footerLinkColor?>; }
#EMMA_active .EMMA_node .EMMA_linkHolderBackground { background:<?=$footerBackgroundColor?>;
	-ms-filter:"alpha(opacity=<?=$footerBackgroundOpacity?>)";
	filter:alpha(opacity=<?=$footerBackgroundOpacity?>);
	-khtml-opacity:.<?=$footerBackgroundOpacity?>;
	-moz-opacity:.<?=$footerBackgroundOpacity?>;
	opacity:.<?=$footerBackgroundOpacity?>;
}

#EMMA_active .EMMA_node .EMMA_tagHolder .EMMA_tag .EMMA_tagOutline { background:<?=$dotOutlineColor?>;
	-ms-filter:"alpha(opacity=<?=$dotOutlineOpacity?>)";
	filter:alpha(opacity=<?=$dotOutlineOpacity?>);
	-khtml-opacity:.<?=$dotOutlineOpacity?>;
	-moz-opacity:.<?=$dotOutlineOpacity?>;
	opacity:.<?=$dotOutlineOpacity?>;
}

#EMMA_active .EMMA_formStyle .EMMA_textField input,
#EMMA_active .EMMA_formStyle .EMMA_textArea textarea,
#EMMA_active .EMMA_formStyle .EMMA_dropDown select { color:<?=$inputTextColor?>; }

#EMMA_active .EMMA_formStyle .EMMA_options .EMMA_optForgotPass,
#EMMA_active .EMMA_formStyle .EMMA_options .EMMA_optBackToLogin { color:<?=$formOptionColor?>; }

#EMMA_active .EMMA_formStyle .EMMA_dropDownDiv .EMMA_button:hover { background:<?=$dropdownBackgroundColor?>; color:<?=$dropdownTextColor?>; }

#EMMA_active .EMMA_formStyle .EMMA_formLabel,
#EMMA_active .EMMA_formStyle .EMMA_formLabel-sm { color:<?=$formLabelColor?>; }

#EMMA_active .EMMA_formStyle .EMMA_formLabel.EMMA_formErrorRow,
#EMMA_active .EMMA_formStyle .EMMA_formLabel-sm.EMMA_formErrorRow { color:<?=$formErrorColor?>; }
#EMMA_active .EMMA_formStyle .EMMA_formError { color:<?=$formErrorColor?>; }
#EMMA_active .EMMA_formStyle .EMMA_formDisabledMsg { color:<?=$formErrorColor?>; }

#EMMA_active .EMMA_formBtnSubmit * { color:<?=$formButtonColor?>; }
#EMMA_active .EMMA_formBtnSubmit[disabled] * { color:<?=$formButtonDisabledColor?> !important; }

#EMMA_active .EMMA_wrapper { z-index:<?=$wrapperIndex?>; }
<?php

$cssTheme = ob_get_contents();
ob_end_clean();



try {
	
	$filename = $wrapperIndex . ".css";

	$ftp = new FTP(FTP_HOST, FTP_USER, FTP_PASS);

	//if (!file_exists(DOCUMENT_ROOT . DIRECTORY_CSS_OUTPUT . "/" . $filename)) throw new Exception("File was not found");
	//if (!Util::remoteFileExists("http://flairstatic.emmaactive.com/frontend-themes/custom/" . $filename)) throw new Exception("File was not found");
	if (!$ftp->fileExists(DIRECTORY_CSS_OUTPUT . "/" . $filename)) throw new Exception("File was not found");


	$cssBase = Util::fileGetContents(WEB_CSS_BASE_FILE);
	$cssBaseParser = new CssParser;
	$cssThemeParser = new CssParser;
	$cssBaseParser->parseStr($cssBase);
	$cssThemeParser->parseStr($cssTheme);
	
	// merge theme css with base css
	foreach ($cssThemeParser->css as $selector => $styles) {
		$declaration = "";
		foreach ($styles as $property => $values) foreach ($values as $value) $declaration .= $property . ":" . $value . ";";
		// filter out empty lines
		if (strlen($declaration) > 0) $cssBaseParser->add($selector, $declaration);
	}


	
	$css = $cssBaseParser->getCSS(true);
	$css = str_replace("#EMMA_active", "#EMMA_active.EMMA_{$wrapperIndex}", $css);

	$output = "/***" . PHP_EOL . PHP_EOL . " EmmaActive CSS" . PHP_EOL . " http://www.emmaactive.com" . PHP_EOL . " @copyright: Copyright (c) 2011 EmmaActive, Inc. All rights reserved." . PHP_EOL . " @license: Dual licensed under the MIT or GPL Version 2 licenses." . PHP_EOL . " @date: " . date('d/m/Y') . PHP_EOL . " @theme: " . $wrapperIndex . PHP_EOL . PHP_EOL . "***/" . PHP_EOL . $css;
	
	

	
	$ftp->changeDirectory(DIRECTORY_CSS_OUTPUT);
	$ftp->fileWrite($filename, $output);




} catch (FtpException $e) {
	output(null, $e->getMessage());
} catch (UtilException $e) {
	output(null, $e->getMessage());
} catch (Exception $e) {
	output(null, $e->getMessage());
}









