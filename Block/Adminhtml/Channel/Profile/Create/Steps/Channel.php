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
 * Channel profile wizard - Store and channel seletcion step
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Create\Steps;

class Channel extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
	/**
	 * \Magento\Store\Model\System\Store
	 */
	protected $_systemStore;
	
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
        return $this->isSingleStore() ? __('Channel') : __('Store & Channel');
    }
    
    protected function _getStores() {
        
        if( $this->getData( 'stores') === null) {
            $stores = $this->_systemStore->getStoreCollection();
            $this->setData( 'stores', $stores);
        }
        return $this->getData( 'stores');
    }
    
    public function isSingleStore() {
        
        return (count( $this->_getStores()) == 1);
    }
    
    public function getStoreNamesWithWebsite()
    {
    	$stores = $this->_getStores();
    	$storeOptions = [];
    	foreach ($stores as $store)
    	{
    		$id = $store->getId();
    		$name = $this->_systemStore->getStoreNameWithWebsite($id);
    		$name = str_replace("/", " - ", $name);
    		$storeOptions[$id] = $name;
    	}
 
    	return $storeOptions;
    }
    
    public function getStoresEncoded() {
        
        $stores = $this->getStoreNamesWithWebsite();
        $enc = array();
        foreach( $stores as $id => $name) {
            $enc[] = array( 'value'=>$id, 'label'=>$name);
        }
        
        return json_encode( $enc);
    }
    
    public function getChannelLinkOptions()
    {
    	return $this->_feedSource->toOptionArray();
    }
    
    public function getChannelsList() {
    
        $map = array();
        $collection = $this->_feedSource->getFeeds();
        foreach( $collection as $feed) {
            $channel = $feed->getChannel();
            $map[ $feed->getLink()] = array(
                'description' => $channel->getDescriptionUrl(),
                'logo' => $channel->getLogoUrl(),
                'label' => $channel->getLabel()
            );
        }
    
        return $map;
    }
}
