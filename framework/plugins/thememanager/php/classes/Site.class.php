<?php

/***

@Date: 7/16/2011
@Author: Justin Bull
@Description: This class manages all methods related to site data
@Dependencies:
	- /php/classes/Query.class.php
	- /php/classes/Util.class.php

***/

class Site 
{

	private $pubkey;
	private $siteInfo;

	protected $domainExceptions = array("emmaactive.com");

	public function __construct($guid)
	{
		if ($guid) $this->pubkey = $guid;
	}
	
	private static function getDomain($url)
	{
		return strtolower(implode('.', array_slice(explode('.', parse_url($url, PHP_URL_HOST)), -2)));
	}

	// query our site data if needed
	public function get() 
	{
		if ($this->pubkey) {
			// dont query user info if we already have it
			if (!$this->siteInfo) {
				$query = new Query;
				$this->siteInfo = $query->select("SELECT * FROM sites WHERE strGUID = '{$this->pubkey}' LIMIT 0,1");
				if (!$this->siteInfo) throw new SiteException('Invalid public key');
			}
			return $this->siteInfo;
		}
	}
	
	public function verify() 
	{
		if (!array_key_exists('bVerified', $this->siteInfo) || $this->siteInfo['bVerified'] !== '1') {
			throw new SiteException('Site not verified');
		}
	}
	
	// NOTE: `HTTP_REFERER` can be spoofed, do not rely on it
	public function checkHost()
	{
		if (array_key_exists('strURL', $this->siteInfo)) $siteHost = self::getDomain($this->siteInfo['strURL']);
		if (defined('HUB_REFERER')) $refererHost = self::getDomain(HUB_REFERER);
		if (!isset($siteHost) || !isset($refererHost)) throw new SiteException('Undefined host');
		/*
		the incoming host must match their defined host
		except for one exception: the incoming host may be ours
		this exception is for the theme manager, which needs to load emma under their GUID not ours..
		*/
		if ($siteHost !== $refererHost && !in_array($refererHost, $this->domainExceptions)) throw new SiteException('Invalid host');		
	}

	// NOTE: `HTTP_REFERER` can be spoofed, do not rely on it
	public function checkSubdomain() 
	{
		if (defined('HUB_REFERER')) {
			$refererHost = self::getDomain(HUB_REFERER);
			$parsed = Util::parseUrl(HUB_REFERER);
		}
		if (isset($parsed)) $isSubdomain = (!empty($parsed['subdomain']) && $parsed['subdomain'] !== 'www') ? true : false;
		if (isset($refererHost)) if (in_array($refererHost, $this->domainExceptions)) return; // exception
		if (isset($isSubdomain) && array_key_exists('bSubdomains', $this->siteInfo)) {
			if ($isSubdomain === true && $this->siteInfo['bSubdomains'] == 0) throw new SiteException('Site does not allow subdomains');
		} else throw new SiteException('Unknown error while checking subdomain');
	}

}

class SiteException extends Exception {}

