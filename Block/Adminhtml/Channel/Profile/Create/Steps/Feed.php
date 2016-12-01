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
 * Channel profile wizard - Feed type seletcion step
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Create\Steps;

class Feed extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
	/**
	 * \Nostress\Koongo\Model\Channel\Feed
	 */
	protected $_feedSource;
	
	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Store\Model\System\Store $storeModel
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Store\Model\System\Store $systemStore,
			\Nostress\Koongo\Model\Channel\Feed $feedSource
	) {
		parent::__construct($context);
		$this->_systemStore = $systemStore;
		$this->_feedSource = $feedSource;
	}
	
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Feed & File Type');
    }
    
    public function getFeedsByLink($jsonEncode = true)
    {
    	$filter = array(\Nostress\Koongo\Model\Channel\Feed::COL_ENABLED => "1");
    	$collection = $this->_feedSource->getFeedCollection($filter,null,\Nostress\Koongo\Model\Channel\Feed::COL_TYPE);
    	
    	$feedsByLink = [];
    	foreach ($collection as $item)
    	{
    		$link = $item->getLink();
    		
    		if(!isset($feedsByLink[$link]))
    			$feedsByLink[$link] = [];
    		$feedsByLink[$link][] = [
                "label" => $item->getType()." (".$item->getFileType().")",
    		    "code" => $item->getCode(),
    		    "id" => "feed-code-radio_".$item->getId()
    		];
    	}
    	
    	if($jsonEncode)
    		$feedsByLink = json_encode($feedsByLink);
    	
    	return $feedsByLink;
    }
   
    public function getChannelManualList() {
    
        $map = array();
        $collection = $this->_feedSource->getFeeds();
        foreach( $collection as $feed) {
            $map[ $feed->getCode()] = $feed->getChannel()->getManualUrl();
        }
    
        return $map;
    }
}
