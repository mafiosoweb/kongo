<?php
/**
* Magento Module developed by NoStress Commerce
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@nostresscommerce.cz so we can send you a copy immediately.
*
* @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)
*
*/

/**
* Class for channel
*
* @category Nostress
* @package Nostress_Koongo
*
*/

namespace Nostress\Koongo\Model;

class Channel  extends \Nostress\Koongo\Model\AbstractModel
{
	const CHANNEL_CACHE_DIR = "ChannelCache";
	const CHANNEL_LOGO_FILE = "logo.png";
	const CHANNEL_MANUAL_FILE = "manual.html";
	const CHANNEL_DESCRIPTION_FILE = "short_description.html";
	
	public function getLogoUrl()
	{
		return $this->getCacheUrl().self::CHANNEL_LOGO_FILE;
	}
	
	public function getManualUrl()
	{
		return $this->getCacheUrl().self::CHANNEL_MANUAL_FILE;
	}
	
	public function getDescriptionUrl()
	{
	    return $this->getCacheUrl().self::CHANNEL_DESCRIPTION_FILE;
	}
	
	public function getPageUrl()
	{
		$urlCode = $this->getUrlCode();
		return $this->getPresentationWebsiteUrl().$urlCode.".html";
	}
	
	public function getLabel()
	{
		$code = $this->getChannelCode();
		$label = ucfirst($code);
		$label = str_replace("_", " ", $label);
		return $label;
	}
	
	protected function getCacheUrl()
	{
		if(empty($this->_channelCacheDirUrl))
		{
			$this->_channelCacheDirUrl = $this->getPresentationWebsiteUrl()."media/".self::CHANNEL_CACHE_DIR."/";
		}
		
		$url = $this->_channelCacheDirUrl.$this->getChannelCode()."/";
		return $url;
	}
	
	public function getUrlCode()
	{
		$channelCode = $this->getChannelCode();
		return str_replace("_", "-", $channelCode);
	}
	
	protected function getPresentationWebsiteUrl()
	{
		return $this->helper->getKoongoWebsiteUrl();
	}
}