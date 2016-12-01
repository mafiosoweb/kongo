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
 * Channel profile feed settings edit form sort products tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab;

use Nostress\Koongo\Model\Channel\Profile;

class Sort extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Attributes
	*/
	protected $attributeSource;
	
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     * @param \Nostress\Koongo\Model\Config\Source\Attributes $attributeSource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [],
    	\Nostress\Koongo\Model\Config\Source\Attributes $attributeSource
    )
    {
    	$this->attributeSource = $attributeSource;
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
        $config = $model->getConfigItem(Profile::CONFIG_FEED,true,Profile::CONFIG_COMMON);
        $channelLabel = $model->getChannel()->getLabel();
        
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
        $form->setHtmlIdPrefix('channel_profile_');

        $fieldset = $form->addFieldset('sort_fieldset', [
            'legend' => __('Sort Products Settings').$model->helper->renderTooltip( 'advanced_sort_products')
        ]);

        $options = $this->attributeSource->toIndexedArray($model->getStoreId(),$channelLabel);
        array_unshift($options, _("Default (Product id)"));
        $fieldset->addField(
        	'sort_attribute',
        	'select',
        	[
        		'name' => Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[sort_attribute]',
        		'label' => __('Attribute for Sorting Products'),
        		'title' => __('Attribute for Sorting Products'),
        		'required' => false,
        		'options' => $options,
        		'disabled' => $isElementDisabled,
        		'note' => __("You may need to sort the products in the feed file. If this is your case, then select here the attribute for products sorting.")
        	]
        );

        $fieldset->addField(
            'sort_order',
            'select',
            [
                'name' => Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[sort_order]',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
        		'options' => ['ASC' => __('ASC'), 'DESC' => __('DESC')],
                'disabled' => $isElementDisabled,
        		'note' => __("Choose ASC for ascending order and DESC for descending order.")
            ]
        );

        $this->_eventManager->dispatch('adminhtml_koongo_channel_profile_general_edit_tab_sort_prepare_form', ['form' => $form]);

        $data = [];
       	$fields = ['sort_attribute','sort_order'];
        
        foreach($fields as $field)
	        if(isset($config[$field]))
	        	$data[$field] = $config[$field];

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
        return __('Sort Products Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Sort Products Settings');
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