<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');


header("content-type:text/plain");

require_once dirname(__FILE__) . "/../config.php";
require_once dirname(__FILE__) . "/../jsonwrapper.php";
require_once dirname(__FILE__) . "/../classes/Connection.class.php";
require_once dirname(__FILE__) . "/../classes/Cache.class.php";
require_once dirname(__FILE__) . "/../classes/Query.class.php";
require_once dirname(__FILE__) . "/../classes/Site.class.php";
require_once dirname(__FILE__) . "/../classes/Util.class.php";


// manages json output using api layout
function output($result, $error) {
	$return = array();
	if (isset($result)) $return['result'] = $result;
	if (isset($error)) $return['error'] = $error;
	echo json_encode($return);
}

$privateThemes = array(
	'aae85f0f-09d6-4c6d-800f-db09046e7de8' => "launch",
	'9e86045e-4131-11e1-b416-72938c046e1b' => "petfood",
	'21ab2ce2-424a-11e1-b416-72938c046e1b' => "cwi"


);

$publicResourceFiles = array(
	'emma' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_default.png",
	'virgin' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_virgin.png",
	'simple' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_simple.png",
	'infoi' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_infoi.png"

);

$privateResourceFiles = array(
	'launch' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_launch.png",
	'petfood' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_petfood.png",
	'cwi' => "http://cdn.emmaactive.com/frontend-themes/resources/grfx_cwi.png"
);

$publicThemeCSS = array(
	'emma' => array(
		'frontendResourceFile' => $publicResourceFiles['emma'],
		'windowHeaderColor' => "#209DC5",
		'tooltipLinkColor' => "#209DC5",
		'tooltipDescriptionColor' => "#888",
		'shareHeaderColor' => "#000000",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "50",
		'inputTextColor' => "#5AC0E4",
		'formOptionColor' => "#209DC5",
		'dropdownBackgroundColor' => "#209DC5",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#BBBBBB",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#196078"
	),
	'virgin' => array(
		'frontendResourceFile' => $publicResourceFiles['virgin'],
		'windowHeaderColor' => "#CC0000",
		'tooltipLinkColor' => "#FFFFFF",
		'tooltipDescriptionColor' => "#888888",
		'shareHeaderColor' => "#FFFFFF",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "50",
		'inputTextColor' => "#FFFFFF",
		'formOptionColor' => "#CC0000",
		'dropdownBackgroundColor' => "#CC0000",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#FFFFFF",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#880000"
	),
	'simple' => array(
		'frontendResourceFile' => $publicResourceFiles['simple'],
		'windowHeaderColor' => "#209DC5",
		'tooltipLinkColor' => "#209DC5",
		'tooltipDescriptionColor' => "#888",
		'shareHeaderColor' => "#000000",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "50",
		'inputTextColor' => "#5AC0E4",
		'formOptionColor' => "#209DC5",
		'dropdownBackgroundColor' => "#209DC5",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#BBBBBB",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#196078"
	),
	'infoi' => array(
		'frontendResourceFile' => $publicResourceFiles['infoi'],
		'windowHeaderColor' => "#209DC5",
		'tooltipLinkColor' => "#209DC5",
		'tooltipDescriptionColor' => "#888",
		'shareHeaderColor' => "#000000",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "50",
		'inputTextColor' => "#5AC0E4",
		'formOptionColor' => "#209DC5",
		'dropdownBackgroundColor' => "#209DC5",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#BBBBBB",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#196078"
	)
);

$privateThemeCSS = array(
	'launch' => array(
		'frontendResourceFile' => $privateResourceFiles['launch'],
		'windowHeaderColor' => "#FFF000",
		'tooltipLinkColor' => "#FFF000",
		'tooltipDescriptionColor' => "#888888",
		'shareHeaderColor' => "#FFF000",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "0",
		'inputTextColor' => "#FFF000",
		'formOptionColor' => "#BBBBBB",
		'dropdownBackgroundColor' => "#FFF000",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#BBBBBB",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#666666"
	),
	'petfood' => array(
		'frontendResourceFile' => $privateResourceFiles['petfood'],
		'windowHeaderColor' => "#FFF000",
		'tooltipLinkColor' => "#FFF000",
		'tooltipDescriptionColor' => "#888888",
		'shareHeaderColor' => "#FFF000",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "0",
		'inputTextColor' => "#FFF000",
		'formOptionColor' => "#BBBBBB",
		'dropdownBackgroundColor' => "#FFF000",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#BBBBBB",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#666666"
	),	
	'cwi' => array(
		'frontendResourceFile' => $privateResourceFiles['cwi'],
		'windowHeaderColor' => "#209DC5",
		'tooltipLinkColor' => "#209DC5",
		'tooltipDescriptionColor' => "#888",
		'shareHeaderColor' => "#000000",
		'footerLinkColor' => "#FFFFFF",
		'footerBackgroundColor' => "#000000",
		'footerBackgroundOpacity' => "70",
		'dotOutlineColor' => "#FFFFFF",
		'dotOutlineOpacity' => "50",
		'inputTextColor' => "#5AC0E4",
		'formOptionColor' => "#209DC5",
		'dropdownBackgroundColor' => "#209DC5",
		'dropdownTextColor' => "#FFFFFF",
		'formLabelColor' => "#BBBBBB",
		'formErrorColor' => "#FFCC33",
		'formButtonColor' => "#FFFFFF",
		'formButtonDisabledColor' => "#196078"
	)

);

$publicThemeJS = array(
	'emma' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'medium',
		'dotOutlineSize' => 6,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 19,
			'tailHeight' => 8,
			'stroke' => false,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "radial",
				'backgroundColor' => "0-#FFFFFF 1-#CCCCCC",
				'strokeWidth' => 1,
				'strokeColor' => "#000000",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => true,
		'tooltipOrientation' => "top",
		'tooltipURL' => true
	),
	'virgin' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'medium',
		'dotOutlineSize' => 6,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 9,
			'tailHeight' => 8,
			'stroke' => true,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "solid",
				'backgroundColor' => "#000000",
				'strokeWidth' => 1,
				'strokeColor' => "#FFFFFF",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => true,
		'tooltipOrientation' => "right",
		'tooltipURL' => false
	),
	'simple' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'small',
		'dotOutlineSize' => 4,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 9,
			'tailHeight' => 8,
			'stroke' => false,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "solid",
				'backgroundColor' => "#FFFFFF",
				'strokeWidth' => 1,
				'strokeColor' => "#000000",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => true,
		'tooltipOrientation' => "right",
		'tooltipURL' => false
	),
	'infoi' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'medium',
		'dotOutlineSize' => 4,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 9,
			'tailHeight' => 4,
			'stroke' => false,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "solid",
				'backgroundColor' => "#FFFFFF",
				'strokeWidth' => 1,
				'strokeColor' => "#000000",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => true,
		'tooltipOrientation' => "right",
		'tooltipURL' => false
	)
);

$privateThemeJS = array(
	'launch' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'small',
		'dotOutlineSize' => 0,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 9,
			'tailHeight' => 8,
			'stroke' => true,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "solid",
				'backgroundColor' => "#000000",
				'strokeWidth' => 1,
				'strokeColor' => "#FFFFFF",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => true,
		'tooltipOrientation' => "right",
		'tooltipURL' => false
	),
	'petfood' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'medium',
		'dotOutlineSize' => 0,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 9,
			'tailHeight' => 8,
			'stroke' => true,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "solid",
				'backgroundColor' => "#000000",
				'strokeWidth' => 1,
				'strokeColor' => "#FFFFFF",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => false,
		'tooltipOrientation' => "right",
		'tooltipURL' => false
	),'cwi' => array(
		'animations' => true,
		'allowBottomLinks' => false,
		'allowShare' => true,
		'dotSize' => 'medium',
		'dotOutlineSize' => 6,
		'canvas' => array(
			'cornerRadius' => 5,
			'tailWidth' => 19,
			'tailHeight' => 8,
			'stroke' => false,
			'shadow' => true,
			'theme' => array(
				'backgroundStyle' => "radial",
				'backgroundColor' => "0-#FFFFFF 1-#CCCCCC",
				'strokeWidth' => 1,
				'strokeColor' => "#000000",
				'shadowOffsetX' => 0,
				'shadowOffsetY' => 0,
				'shadowBlur' => 5,
				'shadowColor' => "#000000"
			)
		),
		'tooltip' => false,
		'tooltipOrientation' => "top",
		'tooltipURL' => true
	)
);





try {
	
	
	$connSlave = new Connection(DB_SLAVE_HOST, DB_SLAVE_USER, DB_SLAVE_PASS, DB_SLAVE_TABLE);
	$urldata = Connection::cleanData($_REQUEST);
	
	foreach ($urldata as $k => $v) ${$k} = $v;

	if (!isset($pk)) throw new Exception("Public key is required");

	$site = new Site($pk);
	$arrSiteData = $site->get();
	$site->verify();

	
	$resourceFiles = $publicResourceFiles;
	$themeCSS = $publicThemeCSS;
	$themeJS = $publicThemeJS;

	if (in_array($pk, array_keys($privateThemes))) {
		
		$index = $privateThemes[$pk];
		$resourceFiles[$index] = $privateResourceFiles[$index];
		$themeCSS[$index] = $privateThemeCSS[$index];
		$themeJS[$index] = $privateThemeJS[$index];
	}


	output(array(
		'themeTracker' => $arrSiteData['themeTracker'],
		'resourceFiles' => $resourceFiles,
		'themeCSS' => $themeCSS,
		'themeJS' => $themeJS
	));

} catch (CacheException $e) {
	output(null, $e->getMessage());
} catch (ConnectionException $e) {
	output(null, $e->getMessage());
} catch (QueryException $e) {
	output(null, $e->getMessage());
} catch (SiteException $e) {
	output(null, $e->getMessage());
} catch (UtilException $e) {
	output(null, $e->getMessage());
} catch (Exception $e) {
	output(null, $e->getMessage());
}


