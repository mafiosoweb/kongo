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
 * Channel profile filter settings edit form types and variants format tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\Filter\Edit\Tab;

use Nostress\Koongo\Model\Channel\Profile;

class Visibility extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/*
	 * @var \Magento\Catalog\Model\Product\Visibility
	*/
	protected $visibilitySource;
	
	/*
	 * @var \Magento\Catalog\Model\Product\Type
	 */
	protected $productTypeSource;
	
	/**
	 *
	 * @var \Nostress\Koongo\Helper
	 */
	protected $helper;
	
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     * @param \Magento\Catalog\Model\Product\Visibility $visibilitySource
     * @param \Nostress\Koongo\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [],
    	\Magento\Catalog\Model\Product\Visibility $visibilitySource,
    	\Nostress\Koongo\Helper\Data $helper
    )
    {
    	$this->visibilitySource = $visibilitySource;
    	$this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Nostress\Koongo\Model\Channel\Profile */
        $model = $this->_coreRegistry->registry('koongo_channel_profile');
        $config = $model->getConfigItem(Profile::CONFIG_FILTER,true);
        
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Nostress_Koongo::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('channel_profile_filter_');

        $fieldset = $form->addFieldset('visibility_fieldset', [
            'legend' => __('Visibility').$model->helper->renderTooltip( 'filter_visibility'),
        ]);
        
        
        
        $fieldset->addField(
        	'visibility',
        	'multiselect',
        	[
        		'name' => Profile::CONFIG_FILTER.'[visibility]',
        		'label' => __('General Product Visiblity'),
        		'title' => __('General Product Visiblity'),
        		'required' => false,
        		'values'   => $this->visibilitySource->toOptionArray(),
        		'disabled' => $isElementDisabled,
        		'note' => __(' Default setup is in the moses cases the most appropriate.')
        	]
        );
        
        
        $fieldset->addField(
        	'visibility_parent',
        	'multiselect',
        	[
        		'name' => Profile::CONFIG_FILTER.'[visibility_parent]',
        		'label' => __('Parent Product Visiblity'),
        		'title' => __('Parent Product Visiblity'),
        		'required' => false,
        		'values'   => $this->visibilitySource->toOptionArray(),
        		'disabled' => $isElementDisabled,
        		'note' => __(' Default setup is in the moses cases the most appropriate.')
        	]
        );

        $this->_eventManager->dispatch('adminhtml_koongo_channel_profile_filter_edit_tab_visibility_prepare_form', ['form' => $form]);
        
        $data = [];
        $fields = ['visibility','visibility_parent'];
        
        foreach($fields as $field)
	        if(isset($config[$field]))
	        	$data[$field] = $config[$field];
        
        if(empty($data['visibility']) )
        	$data['visibility'] = array_keys($this->visibilitySource->getOptionArray());
        
        //fill all posibilities if empty
//         if(empty($data['visibility_parent']) )
//         	$data['visibility_parent'] = array_keys($this->visibilitySource->getOptionArray());
               
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Visibility');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Visibility');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
