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
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Ftp;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_template = 'Nostress_Koongo::koongo/channel/profile/ftp/container.phtml';
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Nostress_Koongo';
        $this->_controller = 'adminhtml_channel_profile_ftp';

        parent::_construct();

        if ($this->_isAllowedAction('Nostress_Koongo::save')) {
            $this->buttonList->update('save', 'label', __('Save Settings'));
        } else {
            $this->buttonList->remove('save');
        }
        
        $this->buttonList->add(
        		'save_execute',
        		[
        		'class' => 'save primary',
        		'label' => __('Save & Upload'),
        		'data_attribute' => [
			        		'mage-init' => [
			        		'button' => [
				        		'event' => 'save',
				        		'target' => '#edit_form',
				        		'eventData' => ['action' => ['args' => ['back' => 'upload']]],
				        	],
		        		],
	        		]
        		]
        );
        
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
    }
    
    public function getProfile() {
        
        return $this->_coreRegistry->registry('koongo_channel_profile');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __("Edit Profile #%1 - FTP Submission ", $this->escapeHtml($this->_coreRegistry->registry('channel_profile')->getId()));
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/channel_profile/');
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            
        ";
        return parent::_prepareLayout();
    }
}
