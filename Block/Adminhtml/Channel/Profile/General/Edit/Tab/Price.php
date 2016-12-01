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
 * Channel profile feed settings edit form price and date format tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */

namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab;

use Nostress\Koongo\Model\Channel\Profile;

class Price extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Datetimeformat
	*/
	protected $datetimeSource;
	
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Priceformat
	 */
	protected $priceformatSource;
	
	/*
	 * @var \Nostress\Koongo\Model\Config\Source\Decimaldelimiter
	*/
	protected $decimaldelimiterSource;
	
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
     * @param \Nostress\Koongo\Model\Config\Source\Datetimeformat $datetimeSource
     * @param\Nostress\Koongo\Model\Config\Source\Priceformat $priceformatSource
     * @param \Nostress\Koongo\Model\Config\Source\Decimaldelimiter $decimaldelimiterSource
     * @param \Nostress\Koongo\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [],
    	\Nostress\Koongo\Model\Config\Source\Datetimeformat $datetimeSource,
    	\Nostress\Koongo\Model\Config\Source\Priceformat $priceformatSource,
    	\Nostress\Koongo\Model\Config\Source\Decimaldelimiter $decimaldelimiterSource,
    	\Nostress\Koongo\Helper\Data $helper
    )
    {
    	$this->datetimeSource = $datetimeSource;
    	$this->priceformatSource = $priceformatSource;
    	$this->decimaldelimiterSource = $decimaldelimiterSource;
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

        $fieldset = $form->addFieldset('price_fieldset', [
            'legend' => __('Price and Date Format').$model->helper->renderTooltip( 'advanced_price_and_date'),
        ]);

        $fieldset->addField(
            'decimal_delimiter',
            'select',
            [
                'name' => Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[decimal_delimiter]',
                'label' => __('Decimal Delimiter'),
                'title' => __('Decimal Delimiter'),
                'required' => false,
        		'options' => $this->decimaldelimiterSource->toIndexedArray(),
                'disabled' => $isElementDisabled,
        		'note' => __('Delitimiter of decimal numbers, in most cases use default value "dot".')
            ]
        );
        
        $fieldset->addField(
       		'price_format',
       		'select',
       		[
        		'name' => Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[price_format]',
        		'label' => __('Price Format'),
        		'title' => __('Price Format'),
        		'required' => false,
        		'options' => $this->priceformatSource->toIndexedArray(),
        		'disabled' => $isElementDisabled,
        		'note' => __("Format of the exported price attribute values.")
       		]
        );
        
        $fieldset->addField(
        	'currency',
        	'select',
        	[
        		'name' => Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[currency]',
        		'label' => __('Currency'),
        		'title' => __('Currency'),
        		'required' => false,
        		'options' => $this->helper->getStoreCurrenciesOptionArray($model->getStoreId()),
        		'disabled' => $isElementDisabled,
        		'note' => __("Choose from currencies allowed for current store.")
        	]
        );
        
        $fieldset->addField(
        	'datetime_format',
        	'select',
        	[
        		'name' => Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'[datetime_format]',
        		'label' => __('Datetime Format '),
        		'title' => __('Datetime Format '),
        		'required' => false,
        		'options' => $this->datetimeSource->toIndexedArray(),
        		'disabled' => $isElementDisabled,
        		'note' => __("Format of the date and time used in the feed.")
        	]
        );

        $this->_eventManager->dispatch('adminhtml_koongo_channel_profile_general_edit_tab_price_prepare_form', ['form' => $form]);

        $data = [];
        $fields = ['currency','decimal_delimiter','price_format','datetime_format'];
        
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
        return __('Price and Date Format');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Price and Date Format');
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
