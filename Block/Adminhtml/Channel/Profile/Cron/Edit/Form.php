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
 * Adminhtml koongo profile cron schedule form block
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Cron\Edit;

use Nostress\Koongo\Model\Channel\Profile;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	/**
	 * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
	 */
	protected $_rendererFieldset;
		
	/**
	 *
	 * @var \Nostress\Koongo\Helper
	 */
	protected $helper;
	
	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory	 
	 * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset	 
	 * @param \Nostress\Koongo\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\Data\FormFactory $formFactory,			
			\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,			
			\Nostress\Koongo\Helper\Data $helper,
			array $data = []
	)
	{		
		$this->_rendererFieldset = $rendererFieldset;	
		$this->helper = $helper;
		parent::__construct($context, $registry, $formFactory, $data);
	}
	
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        
        /* @var $model \Nostress\Koongo\Model\Channel\Profile */
        $model = $this->_coreRegistry->registry('koongo_channel_profile');
//         $channelLabel = $model->getChannel()->getLabel();
        
        /*
         * Checking if user have permissions to save information
        */
        if ($this->_isAllowedAction('Nostress_Koongo::save')) {
        	$isElementDisabled = false;
        } else {
        	$isElementDisabled = true;
        }                       
        
        //Prefix must be rule_ to force conditions filter work properly
        $form->setHtmlIdPrefix('channel_profile_cron_');
         
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __(''),'expanded'  => true]);
        
        if ($model->getId()) {
        	$fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
        
        $this->_eventManager->dispatch('adminhtml_koongo_channel_profile_cron_edit_tab_main_prepare_form', ['form' => $form]);
                      
        $data = [];       
        $data['entity_id'] = $model->getId();
        
        $form->setValues($data);                       
        $this->setForm($form);               
        return parent::_prepareForm();
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
}
